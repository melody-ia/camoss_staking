<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/purchase_proc.php');

// $debug = 1;
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');
$soodang_date = date('Y-m-d', strtotime("+0 day"));


$mb_id = $_POST['mb_id'];
$mb_no = $_POST['mb_no'];
$mb_rank = $_POST['rank'];



$func = $_POST['func'];
$input_val= $_POST['input_val'];
$output_val = $_POST['output_val'];
$it_point = $_POST['it_point'];
$pack_name= $_POST['select_pack_name'];
$pack_id = $_POST['select_pack_id'];
$pack_maker = $_POST['select_maker'];
$it_supply_point = $_POST['it_supply_point'];

$val = substr($pack_maker,1,1);
$coin_val = $curencys[0];

if($debug){
	$mb_id = 'test38';
	$mb_no = 39;
	$mb_rank = 0;
	$func = 'new';
	$input_val =3000; // 결제금액 
	$output_val =3000; // 구매금액
	$pack_name = 'P4';
	$pack_id = 2023040400;
	$it_point = 3000;
	$it_supply_point = 0;
}


$pv = $it_supply_point;

if($func == "new"){
	$orderid = date("YmdHis",time()).'02';
}else{
	$orderid = $_POST['od_id'];
}

$member_bucks_check_sql = "select sum(mb_deposit_point+mb_deposit_calc+mb_balance+mb_balance_calc - mb_shift_amt) as deposit_total, SUM(mb_deposit_point+mb_deposit_calc) AS deposit_limit from g5_member where mb_id = '{$mb_id}'";
$member_bucks_check_row = sql_fetch($member_bucks_check_sql);

$deposit_total = floor($member_bucks_check_row['deposit_total']);
$deposit_limit = floor($member_bucks_check_row['deposit_limit']);

// 개별모드 
if(Solitare == true){
	$pay_limit = $it_point*($limited/100);
	$pay_id =  generateOrderCode(3);
	$Sol_sql = ", pay_limit = '{$pay_limit}', pay_id = '{$pay_id}' ";
}else{
	$Sol_sql = "";
}

$it_point = conv_number($it_point);
$input_val = conv_number($it_point);


if($deposit_limit <= $input_val){
	$deposit_cal_value = ($input_val-$deposit_limit)*1.07;
	$puchase_value = $deposit_limit + $deposit_cal_value;
	$output_val = $puchase_value;
	
	if( $deposit_total < $puchase_value){
		if($debug){
			echo $deposit_total. ' / '.  $puchase_value; 
		}

		echo json_encode(array("result" => "failed",  "code" => "0001", "sql" => "잔고가 부족합니다.\n재구매시에는 수수료를 포함한 구매가능금액이 필요합니다."));
		return false;
	}

	$target_sql = " mb_deposit_calc = mb_deposit_calc - {$deposit_limit} ";

	if($deposit_cal_value > 0){
		$target_sql .= ", mb_balance_calc = mb_balance_calc - {$deposit_cal_value}";
		$Sol_sql .= ", od_refund_price = {$deposit_cal_value} ";
	}
}else{
	$target_sql = " mb_deposit_calc = mb_deposit_calc - {$input_val} ";
}

$max_limit_point = $it_point * ($limited/100);

$sql = "insert g5_order set
	od_id				= '".$orderid."'
	, mb_no             = '".$mb_no."'
	, mb_id             = '".$mb_id."'
	, od_cart_price     = ".$input_val."
	, od_cash    		= ".$output_val."
	, od_name           = '{$pack_name}'
	, od_tno            = '{$pack_id}'
	, od_receipt_time   = '".$now_datetime."'
	, od_time           = '".$now_datetime."'
	, od_date           = '".$now_date."'
	, od_soodang_date   = '".$soodang_date."'
	, od_settle_case    = '".$coin_val."'
	, od_cash_no 		= '".$pack_maker."'	
	, od_status         = '패키지구매(관리자)'
	, upstair    		= ".$it_point."
	, pv				= ".$pv." 
	, od_misu           = ".$max_limit_point."".$Sol_sql;


if($debug){
	$rst = 1;
	echo "구매내역 Invoice 생성<br>";
	echo $sql."<br><br>";
}else{

	

	if($deposit_total < $it_point){
		echo json_encode(array("result" => "failed",  "code" => "0001", "sql" => "잔고가 부족합니다."));
		return false;
	}

	$rst = sql_query($sql);
}

$logic = purchase_package($mb_id,$pack_id);



if($rst && $logic){

	$update_point = " UPDATE g5_member set pv = pv + {$input_val}, {$target_sql}";
	$mb_level = sql_fetch("SELECT mb_level from g5_member WHERE mb_id = '{$mb_id}' ")['mb_level'];

	if($mb_level == 0){
		$update_point .= ", mb_level = 1 " ;
	}

	if($mb_rank >= $val){
		$update_rank = $mb_rank;
	}else{
		$update_rank = $val;
	}

	$sql = "SELECT limited from wallet_bonus_config WHERE idx =0 ";
	$row = sql_fetch($sql);
	if($row['limited'] > 0){ 
		$limited = $row['limited'];
	}

	// 해당 패키지로 받을 수 있는 수당 한도(300%)
	$max_limit_point = $input_val * ($limited/100);
	
	$update_point .= ", mb_rate = ( mb_rate + {$pv}) ";
	$update_point .= ", mb_save_point = ( mb_save_point + {$it_point}) ";
	$update_point .= ", mb_index = ( mb_index + {$max_limit_point}) ";
	$update_point .= ", rank = '{$update_rank}', rank_note = '{$pack_name}', sales_day = '{$now_datetime}' ";
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
