<?php
$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');
$debug = false;

auth_check($auth[$sub_menu], 'r');


//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_order AS o WHERE od_soodang_date ='2024-10-12' AND o.od_cash_no NOT IN ('P0') AND pay_end != 1 ";
$sql_mgroup=' ORDER BY o.mb_no asc';

$pre_sql = "select * 
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";


if($debug){
    echo "<code>";
    print_r($pre_sql);
    echo "</code><br>";
}

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

$direct_rate = $bonus_row['rate'];

ob_start();

// 설정로그 
echo "<span class ='title' style='font-size:20px;'>".$bonus_row['name']." 수당 정산</span><br>";
echo "<strong>".strtoupper($code)." 수당 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 -".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx." | ".$bonus_limit_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준매출(정산패키지) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";
header('Content-Type: text/html; charset=utf-8');
?>

<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>
<header>정산시작</header>    
<div>
<?

// 디버그 로그 
if($debug){
	echo "<code>";
    print_r($sql);
	echo "</code><br>";
}


if($result_cnt > 0){
  
    while($pre_row = sql_fetch_array($pre_result)){
        $from_mb_id = $pre_row['mb_id'];
        $from_od_name = $pre_row['od_name'];
        $from_pay_id = $pre_row['pay_id'];
        $from_upstair = $pre_row['upstair'];

        $comp_sql = "SELECT mb_recommend from g5_member WHERE mb_id = '{$from_mb_id}' ";
        $comp_result = sql_fetch($comp_sql);
        $comp = $comp_result['mb_recommend'];

        echo "<br><span class='title block gold' style='font-size:30px;'>".$from_od_name." | ". $from_pay_id ." | ".$from_mb_id."</span><br>";
        echo "<span class='strong'>▶직추천인 : ".$comp."</span>";

        $comp_pack_sql = "SELECT * FROM g5_order WHERE mb_id = '{$comp}' AND pay_end != 1 order by od_soodang_date desc limit 0,1";
        $comp_pack_result = sql_query($comp_pack_sql);
        $comp_pack_result_cnt = sql_num_rows($comp_pack_result);

        if($comp_pack_result_cnt > 0){
            while($row = sql_fetch_array($comp_pack_result)){
            
                $od_name = $row['od_name'];
                $pay_id = $row['pay_id'];
                $pay_limit = $row['pay_limit'];
                $pay_ing = $row['pay_ing'];

                echo "<br><span >".$od_name." | ". $pay_id ." </span><br>";
                
                $benefit = $from_upstair * $direct_rate/100;
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
                        echo "▶ 직추천 보너스 : ".$from_upstair."*".$direct_rate." = ".$benefit."</br>";
                    }

                // 기록용 
                $rec = $code.' Bonus from '. $from_mb_id.'::'.$from_od_name." | P =".$live_benefit.", CP = ".$shop_benefit;
                $rec_adm = $from_mb_id.'|'.$from_od_name.'|'.$from_pay_id.'::'.shift_auto($from_upstair).'*'.$direct_rate.'='.$benefit." | P =".$live_benefit.", CP = ".$shop_benefit; 


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
        }else{
            echo "<br><span class='red'>기준매출없음</span><br><br><br>";
        }
    }
        
}else{
    echo "<span style='display: flex;justify-content: center; color:red;'>정산할 패키지가 존재하지 않습니다.</span>";
}

?>

<?include_once('./bonus_footer.php');?>

<?
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}
?>