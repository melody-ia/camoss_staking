<?php
include_once('./_common.php');
include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');
$pin = $_POST['pin'];
$mb_hp = $_REQUEST['mb_hp'];
$mb_id = $_REQUEST['mb_id'];

$check_pin = sql_fetch("SELECT mb_id, auth_key,phone_number from auth_sms WHERE mb_id = '{$mb_id}' AND phone_number = '{$mb_hp}'  order by no desc limit 0,1");

$debug_mode = LIVE_MODE;

if($debug_mode){
    $input_value = Encrypt($pin);
}else{
    $input_value = $pin;
}

if($input_value == $check_pin['auth_key']){
    echo json_encode(array("result" => "success"));
}else{
    echo json_encode(array("result" => "failed","error" => $input_value),JSON_UNESCAPED_UNICODE);
}

?>