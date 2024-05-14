<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/purchase_proc.php');

function remove_zeroes($string) {

	$result = $string;
	if(strlen($string) >= 5){
		$result = preg_replace('/(?<=\d)0000/', '', $string)."만";
	}
    return $result;
}

// $debug = 1;
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');
$soodang_date = date('Y-m-d', strtotime("+0 day"));

$mb_id = $member['mb_id'];
$mb_no = $member['mb_no'];
$goods_price = $_POST['goods_price'];
$curencys = $_POST['curencys'];

$target = "mb_balance_calc";
$orderid = date("YmdHis",time()).'01';
$remove_zeroes = remove_zeroes($goods_price);

$sql = "insert g5_order set
	od_id = '{$orderid}'
	, mb_no = '{$mb_no}'
	, mb_id = '{$mb_id}'
	, od_cart_price = {$goods_price}
	, od_cash = {$goods_price}
	, upstair = {$goods_price}
	, od_name = '{$remove_zeroes}'
	, od_tno = '{$pack_id}'
	, od_receipt_time = '{$now_datetime}'
	, od_time = '{$now_datetime}'
	, od_date = '{$now_date}'
	, od_soodang_date = '{$soodang_date}'
	, od_settle_case = '{$curencys}'
	, od_status = '재구매'
	, od_cash_no = '재구매'	
	, pv = 0 ";

if($debug){
	$rst = 1;
	echo "구매내역 Invoice 생성<br>";
	echo $sql."<br><br>";
}else{

	$member_bucks_check_sql = "select sum(mb_balance + mb_balance_calc - mb_shift_amt) as balance from g5_member where mb_id = '{$mb_id}'";
	$member_bucks_check_row = sql_fetch($member_bucks_check_sql);

	$balance = floor($member_bucks_check_row['balance']);

	if($balance < $goods_price){
		echo json_encode(array("result" => "failed",  "code" => "0001", "sql" => "잔고가 부족합니다."));
		return false;
	}

	$rst = sql_query($sql);
}

if($rst){

	$update_point = "update g5_member set pv = pv + {$goods_price}, $target = ($target - $goods_price)";

	$max_limit_point = $goods_price * ($limited/100);

	$update_point .= ", mb_index = ( mb_index + {$max_limit_point}) ";
	$update_point .= " where mb_id ='".$mb_id."'";

	if($debug){
		echo "회원 금액 반영<br>";
		echo $update_point."<br>";
	}else{
		sql_query($update_point);
		ob_end_clean();
		echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => $save_hist)));
	}
}else{
	ob_end_clean();
	echo (json_encode(array("result" => "failed",  "code" => "0001", "sql" => $save_hist)));
}

?>

<?if($debug){?>
<style>
    .red{color:red;font-size:16px;font-weight:900}
    .blue{color:blue;font-size:16px;font-weight:900}
    .title {font-weight:900}
    code{text-decoration: italic;color:green;display:block}
    .box{background:#f5f5f5;border:1px solid #ddd;padding:20px;}
</style>
<?}?>
