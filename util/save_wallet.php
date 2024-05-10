<?php
include_once("./_common.php");

$mb_id = $_POST['mb_id'];
$address = $_POST['address'];
$private_key = $_POST['privateKey'];
$network = $_POST['network'];
$encrypt = $_POST['encrypt'];

if($network == 'mainnet'){

    $erc20_w = "erc20w";
    $erc20_k = "erc20pk"; 

}else{

    $erc20_w = "erc20w_test";
    $erc20_k = "erc20pk_test"; 

}

if($encrypt == "Y"){
   $key = $mb_id.'@willsoft@';
   $encrypt_private_key = "(HEX(AES_ENCRYPT('$private_key','$key')))";
 }else{
    $encrypt_private_key = "'$private_key'";
 }

$sql = "update g5_member set {$erc20_w} = '{$address}' , {$erc20_k} = {$encrypt_private_key} where mb_id = '{$mb_id}'";
$result = sql_query($sql);

if($result){
   $code = "0001";
}else{
   $code = "0002";
}
echo json_encode(array("code"=>$code));
?>