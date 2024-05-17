<?php
include_once('./_common.php');

if($_POST['check'] == "id"){
  $registerId = $_POST['registerId']; 
  $search = "mb_id = '{$registerId}'";

  $sql = "select exists(select mb_id from g5_member_del where {$search}) as exist";
  $is_exist = sql_fetch($sql)['exist'];
  if($is_exist){
    echo json_encode(array("response"=>"이미 존재하는 아이디입니다.","code" => "000"));
    return false;
  }
}

if($_POST['check'] == "wallet"){
  $registerId = $_POST['registerId'];
  $search = "mb_name = '{$registerId}'";
}

if($_POST['check'] == "recom"){
  $registerId = $_POST['registerId'];
  $search = "mb_recommend = '{$registerId}'";
}

$sql = "SELECT mb_id FROM g5_member WHERE ".$search;
  
$result = sql_query($sql);

$count = sql_num_rows($result);

if($count > 0){
  $response = json_encode(array("response"=>"이미 존재하는 아이디입니다.","code" => "000"));
}else{
  $response = json_encode(array("response"=>"Available","code" => "001","wallet"=>$registerId));
}

echo $response;

 ?>
