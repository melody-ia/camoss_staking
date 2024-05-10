<?php 
include_once('./_common.php');
$it_id = isset($_GET['it_id']) ? get_search_string(trim($_GET['it_id'])) : '';
$no = isset($_GET['no']) ? preg_replace('/[^0-9a-z]/i', '', $_GET['no']) : '';

$row = get_shop_item($it_id, true);

$imagefile = G5_DATA_PATH.'/item/'.$row['it_img'.$no];
$imagefileurl = run_replace('get_item_image_url', G5_DATA_URL.'/item/'.$row['it_img'.$no], $row, $no);

echo json_encode(array("code"=>"0001","result"=>$imagefileurl))
?>