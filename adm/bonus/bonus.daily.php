<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

// 데일리수당
$debug = false;

function find_rate($rate_array,$pv){

	$result = 0;
	
	foreach($rate_array as $key => $value){
		if($key <= $pv){
			$result = $value;
			break;
		}
	}

	return $result;
}

$reset_daily_benefit_sql = "update g5_member set mb_my_sales = 0";
if($debug){
	echo "<code>";
	print_r($reset_daily_benefit_sql);
	echo "</code><br>";
}else{
	$reset_result = sql_query($reset_daily_benefit_sql);
	if(!$reset_result){
		echo "<script>alert('DAILY 지급중 문제가 발생하였습니다.');
				history.back();</script>";
	}
}

$order_list_sql = "select m.mb_level,m.mb_no, m.mb_id, m.grade, m.mb_name, m.pv, (m.mb_balance + m.mb_shop_point) as mb_balance, m.mb_balance_ignore, m.mb_index
from g5_member m where m.pv > 0 AND mb_level < 10 ";
$order_list_result = sql_query($order_list_sql);
$order_cnt = sql_num_rows($order_list_result);

if($debug){
	echo "<code>";
	print_r($order_list_sql);
	echo "</code><br>";
}

$goods_sql = "select it_name,it_price, it_supply_point from g5_item where it_maker <> 'P0' order by it_price asc";
$bonus_row = bonus_pick($code);;


$goods_row = sql_query($goods_sql);
$soodang_rate = $bonus_row['rate'];
$soodang_config = explode(",",$soodang_rate);

$soodang_cnt = count($soodang_config);
$goods_cnt = sql_num_rows($goods_row);
if($soodang_cnt != $goods_cnt){
	echo "<script>alert('DAILY 수당 비율과 상품 갯수가 맞지 않습니다.');
	history.back();</script>";
}

$rate = "";
$rate_array = [];
for($i = 0; $i < $row = sql_fetch_array($goods_row); $i++){
	if($row['it_supply_point'] != $soodang_config[$i]){
		echo "<script>alert('DAILY 수당 비율과 상품 비율이 맞지 않습니다.');
		history.back();</script>";
		return false;
	}
	$clean_price_format = clean_number_format($row['it_price']);
	$rate .= $row['it_name'].':'."{$clean_price_format} -> {$soodang_config[$i]}% <br> ";
	$rate_array[$row['it_price']] = $soodang_config[$i];
	
}
krsort($rate_array);

// 설정로그 
echo "<p><strong>".strtoupper($code)." 지급비율 / 지급한계 : ".$bonus_row['limited']."% </strong><br>".$rate."   </p><br>";
echo "<strong>수당지급일 : ".$bonus_day."<br>";
echo "정산대상 회원 : <span class='red'>".$order_cnt."</span></strong><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";
?>

<html>
	<body>
		<header>정산시작</header>    
		<div>
	
<?php

	if(!$get_today){

		for($i = 0; $i < $order_list_row = sql_fetch_array($order_list_result); $i++){
			$comp = $order_list_row['mb_id'];

			$pack_order_sql = "SELECT * from g5_order O WHERE O.mb_id = '{$comp}' AND O.pay_end != 1 AND O.od_soodang_date <= '{$bonus_day}' AND O.od_cash_no <> 'P0' ";
			$pack_order_result = sql_query($pack_order_sql);
			$pack_order_cnt = sql_num_rows($pack_order_result);

			if($pack_order_cnt > 0){
				echo "<br><br><span class='title block gold' style='font-size:30px;'>".$comp."</span><br>";

				while($row = sql_fetch_array($pack_order_result)){
					$od_name = $row['od_name'];
					$pay_id = $row['pay_id'];
					$rate = $row['pv'];
					$upstair = $row['upstair'];
					$pay_limit = $row['pay_limit'];
					$pay_ing = $row['pay_ing'];

					// $balance_benefit = $calc_benefit * $live_bonus_rate;
					// $shop_benefit = $calc_benefit * $shop_bonus_rate;

					echo "<span class='subtitle'>".$od_name." | ". $pay_id . "</span><br>";

					$benefit = $upstair * $rate/100;
					$benefit_limit = $pay_limit - ($pay_ing + $benefit);// 수당합계
					$balance_limit = $pay_limit; // 수당한계
					$package_end = '';

                    if($benefit_limit > 0){
                        $benefit_limit = $benefit;
                    }else{
                        if($benefit_limit*-1 > $benefit){
                            $benefit_limit = 0;
                        }else{
                            $benefit_limit = $benefit + $benefit_limit;
							
							$package_end = " ,pay_end = 1 ";
							// 완료처리
                        }
                    }
                    
					$benefit_point = shift_auto($benefit);
                    $benefit_limit_point = shift_auto($benefit_limit);

                    $live_benefit = $benefit_limit * $live_bonus_rate;
                    $shop_benefit = $benefit_limit * $shop_bonus_rate;

						if($debug){
							echo "<code>누적수당: ".shift_auto($pay_ing)." | 수당한계: ".shift_auto($pay_limit);
							echo " | 발생할수당: ".shift_auto($benefit)." | 지급할수당:".$benefit_limit."</code>";
							echo "▶데일리보너스 : ".$upstair."*".$rate." = ".$benefit."</br>";
						}

					// 기록용 
					$rec = $code.' Bonus from '. $od_name.' - '.$pay_id." | P =".$live_benefit.", CP = ".$shop_benefit;
					$rec_adm = $od_name.' - '.$pay_id.':'.shift_auto($upstair).'*'.$rate.'='.$benefit." | P =".$live_benefit.", CP = ".$shop_benefit; 
 

					if($benefit > $benefit_limit && $balance_limit != 0 ){

                        $rec_adm .= "<span class=red> |  Bonus overflow :: ".shift_auto($benefit_limit - $benefit,2)."</span>";
						echo "<span class=blue> ▶▶ 수당 지급 : ".shift_auto($benefit)."</span>";
                        echo "<span class=red> ▶▶▶ 수당 초과 (한계까지만 지급) : ".$benefit_limit_point." </span><br>";

						echo "<code>누적수당: ".shift_auto($pay_ing)." | 수당한계: ".shift_auto($pay_limit);
						echo " | 발생할수당: ".shift_auto($benefit)." | 지급할수당:".$benefit_limit."</code>";

                    }else if($benefit != 0 && $balance_limit == 0 && $benefit_limit == 0){
            
                        $rec_adm .= "<span class=red> | Sales zero :: ".shift_auto(($benefit_limit - $benefit),COIN_NUMBER_POINT)."</span>";
                        echo "<span class=blue> ▶▶ 수당 지급 : ".shift_auto($benefit)."</span>";
                        echo "<span class=red> ▶▶▶ 수당 초과 (기준매출없음) : ".$benefit_limit_point." </span><br>";
                    }else if($benefit == 0){
                        echo "<span class=black> ▶▶ 수당 미발생 </span><br>";
                    }else{
                        echo "<span class=blue>  ▶▶ 수당 지급 : ".$benefit_limit." (P지급".$live_benefit." / CP지급 : ".$shop_benefit.")</span><br>";
                    }
					echo "<br><br>";



					// 실제 지급 및 기록
					if($benefit > 0 && $benefit_limit > 0){
    
                        $record_result = soodang_record($comp, $code, $benefit_limit,$rec,$rec_adm,$bonus_day,$mb_no,$mb_level,$mb_name,$pay_id);
        
                        if($record_result){
                            
                            if($balanace_ignore){
                                $balance_ignore_sql = ", mb_balance_ignore = mb_balance_ignore + {$benefit_limit} ";
                            }else{
                                $balance_ignore_sql = "";
                            }

    
                            $balance_up = "UPDATE g5_member set mb_balance = mb_balance + {$live_benefit} {$balance_ignore_sql}, mb_shop_point = mb_shop_point + {$shop_benefit}   where mb_id = '".$comp."'";
							$order_up = "UPDATE g5_order set pay_ing = pay_ing + {$benefit_limit} {$package_end} WHERE pay_id = '{$pay_id}' ";
    
                            // 디버그 로그
                            if($debug){
                                echo "<code>";
                                print_R($balance_up);
								echo "<br>";
								prinT_R($order_up);
                                echo "</code>";
                            }else{
                                sql_query($balance_up);
								sql_query($order_up);
                            }
                        }
                    }
				}
			}
		}
		
	}else{
		echo "<span style='display: flex;justify-content: center; color:red;'>정산할 회원이 존재하지 않습니다.</span>";
	}

	include_once('./bonus_footer.php');
	
	//로그 기록
	if($debug){}else{
		$html = ob_get_contents();
		//ob_end_flush();
		$logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
		fopen($logfile, "w");
		file_put_contents($logfile, ob_get_contents());
	}
 ?>