<?php
require('../functions.php');

// if(isset($_GET['member_id']) && $_GET['member_id'] !== '' && isset($_GET['token']) && $_GET['token'] !== '' && isset($_GET['user_id']) && $_GET['user_id'] !== ''){
if(isset($_GET['member_id']) && $_GET['member_id'] !== ''){
  $member_id = substr('0000' . $_GET['member_id'], -4);
  $user_id = $_GET['user_id'];
  $token = $_GET['token'];
}else{
  debug('no member id or user id or token');
  exit();
}
$shift_supervisors = array(3818,4873,2810,1867,4274,5501,0027,2282,3741,2362,3840,5442,2541,2147,2905,2502,2955,4514,2547,3711,2369,3142,5543,1695,2349,3407,3537,5475,4263,5481,2579,0169,4396,4310,2490,4153,3606,4339);

// function check_token($token, $user_id, $current_time){
//   $query = $select_token_query = "SELECT *, '{{current_time}}' AS `current_time` FROM users WHERE token = '{{token}}' AND user_id = '{{user_id}}' ORDER BY start_time DESC limit 1;";
//   $query = str_replace('{{token}}', $token, $query);
//   $query = str_replace('{{user_id}}', $user_id, $query);
//   $query = str_replace('{{current_time}}', $current_time, $query);
//   debug('token_query', $query);
//   $statement = $GLOBALS['db']->query($query);
//   $row_count = $statement->rowCount();
//   if($row_count === 1){
//     return true;
//   }else{
//     return false;
//   }
// }
// $is_active_token = false;
// $is_active_token = check_token($token, $user_id, $current_time);
// if($is_active_token === false){
//   debug('token is not active');
//   exit();
// }

$insert_query = <<<QUERY_END
INSERT INTO access
(
  member_id
  , start_time
  , end_time
)
VALUES
(
  {{member_id}}
  , '{{start_time}}'
  , '{{end_time}}' 
)
;
QUERY_END;

$delete_query = <<<QUERY_END
DELETE FROM access
WHERE member_id = {{member_id}}
;
QUERY_END;

// jQuery.getJSON("http://api.briankranson.com/add.php?user_id=api_user&token="+sessionStorage.getItem('bk_api_token')+"&member_id=1234").done(function(data) {console.log(data)});
$query = $select_query;
$query = str_replace('{{member_id}}', $member_id, $query);
$query = str_replace('{{current_time}}', $current_time, $query);
debug('select_query', $query);
$statement = $db->query($query);
$row_count = $statement->rowCount();
debug('row count', $row_count);
// if($row_count === 1){
//   $row = $statement->fetch(PDO::FETCH_ASSOC);
//   $row_id = $row['id'];
//   $start_time = $row['start_time'];
//   $end_time = $row['end_time'];
//   $current_time = $row['current_time'];
//   debug('we did not insert', $row_id.' '.$member_id.' '.$start_time.' '.$current_time.' '.$end_time);
//   $output = json_decode('{}');
//   $output->success = 'false';
//   $output->message = 'member already added, cannot add more than once for given time period';
//   $output->member_id = $member_id;
//   $output->start_time = $start_time;
//   $output->end_time = $end_time;
//   echo json_encode($output);
// }else{
  $query = $delete_query;
  $query = str_replace('{{member_id}}', $member_id, $query);
  $result = $db->exec($query);

  $start_time = date('Y-m-d H:i:s');
  // $end_time = date('Y-m-d H:i:s', strtotime("$start_time + 4 hours"));
  $end_time = date('Y-m-d 23:23:59');
  if(in_array($member_id, $shift_supervisors)){
    $end_time = date('Y-m-d H:i:s', strtotime("$start_time + 365 days"));
  }
  $query = $insert_query;
  $query = str_replace('{{member_id}}', $member_id, $query);
  $query = str_replace('{{start_time}}', $start_time, $query);
  $query = str_replace('{{end_time}}', $end_time, $query);
  debug('insert_query', $query);
  $result = $db->exec($query);
  $insert_id = $db->lastInsertId();
  debug('added', $insert_id);
  $output = json_decode('{}');
  $output->success = 'true';
  $output->message = 'member added';
  $output->member_id = $member_id;
  $output->start_time = $start_time;
  $output->end_time = $end_time;
  echo json_encode($output);
// }

?>