<?php
include_once('./_common.php');

$mb_id = $_POST['user_id'];
$mb_pwd = $_POST['user_pwd'];

$sql = "SELECT mb_password FROM g5_member WHERE mb_id = '{$mb_id}'";
$row = sql_fetch($sql);

$check_pwd = check_password($mb_pwd, $row['mb_password']);

if($check_pwd){
  echo json_encode(array("result"=>"OK"));
}else{
  echo json_encode(array("result"=>"FAIL"));
}
?>
