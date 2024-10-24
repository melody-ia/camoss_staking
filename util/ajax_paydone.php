<?php
include_once('./_common.php');

$var = $_REQUEST;
$sql = "UPDATE g5_order SET pay_end = {$var['pay_end']} where pay_id = '{$var['pay_id']}' ";

$result = sql_query($sql);

if($result){
    echo (json_encode(array("result" => "success",  "code" => "0001")));
}else{
    echo (json_encode(array("result" => "failed",  "code" => "0002")));
}

?>
