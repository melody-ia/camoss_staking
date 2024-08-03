<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

// $debug =1;

// 지난주 날짜 구하기 
/* $today=$bonus_day;
$timestr        = strtotime($today);
$week           = date('w', strtotime($todate));
$weekfr         = $timestr - ($week * 86400);
$weekla         = $weekfr + (6 * 86400);
$week_frdate    = date('Y-m-d', $weekfr - (86400 * 5)); // 지난주 시작일자
$week_todate    = date('Y-m-d', $weekla - (86400 * 5)); // 지난주 종료일자 */

$month = date('m', $timestr);

echo "이번달 : ".$month."<br>";

$d_1 = mktime(0,0,0, date("m"), 1, date("Y")); // 이번달 1일

// 지난달 1일~말일
$prev_month = strtotime("-1 month", $d_1); // 지난달

$day = date('d', $timestr);
$lastday = date('t', $timestr);
$balanace_ignore = TRUE;

if($day > 13 && $day <= 18){
    $half = '1/2';
    $half_frdate    = date('Y-m-01', $timestr); // 매월 1 시작일자
    $half_todate    = date('Y-m-15', $timestr); // 매월 15
}else{
    $half = '2/2';
    $half_frdate    = date('Y-m-16', $prev_month); // 전월 15
    $half_todate    = date('Y-m-'.$lastday, $prev_month); // 전월 말일
}



// $bonus_rate = explode(',',$bonus_row['rate']);
$bonus_rate = $bonus_row['rate']*0.01;

$bonus_condition = $bonus_row['source'];
$bonus_condition_tx = bonus_condition_tx($bonus_condition);
$bonus_layer = $bonus_row['layer'];
$bonus_layer_tx = bonus_layer_tx($bonus_layer);


//보름간 매출 합계 
/* $total_order_query = "SELECT SUM(pv) AS hap FROM g5_shop_order WHERE od_date BETWEEN '{$half_frdate}' AND '{$half_todate}' ";
$total_order_reult = sql_fetch($total_order_query);
$total_order = $total_order_reult['hap'];
$grade_order = ($total_order * 0.02); */


// 디버그 로그 
/* if($debug){
	echo "매출 합계 - <code>";
    print_r($total_order_query);
	echo "</code><br>";
} */


//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_member ";
$sql_search=" WHERE center_use > 0 ".$pre_condition.$admin_condition;
$sql_mgroup=" ORDER BY mb_no asc ";

$pre_sql = "select *
                {$sql_common}
                {$sql_search}
                {$sql_mgroup}";

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

// 디버그 로그 
if($debug){
	echo "대상회원 - <code>";
    print_r($pre_sql);
	echo "</code><br>";
}


ob_start();

// 설정로그 
echo "<strong>센터 지급비율 : ". $bonus_row['rate']."% / 소개수당 지급비율(센터회원의 추천인) : 1 %   </strong> <br>";
echo "<br><strong> 현재일 : ".$bonus_day." |  ".$half."(지급산정기준) : <span class='red'>".$half_frdate."~".$half_todate."</span><br>";

echo "<br><br>기준대상자(센터회원) : <span class='red'>".$result_cnt."</span>";
echo "</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";
?>

<html><body>
<header>정산시작</header>    
<div>
<?


//회원 리스트를 읽어 온다.
$sql_search=" WHERE center_use = 1 ".$pre_condition.$admin_condition;
$sql_mgroup=" ORDER BY mb_no asc ";
$sql = "select * FROM g5_member
                {$sql_search}
                {$sql_mgroup}";

$result = sql_query($sql);

// 디버그 로그 
if($debug){
	echo "<code>";
    print_r($sql);
	echo "</code><br>";
}


excute();

function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition, $code, $bonus_rate,$pre_condition_in,$bonus_limit,$week_frdate,$week_todate,$half_frdate,$half_todate,$balanace_ignore,$live_bonus_rate,$shop_bonus_rate;
    global $debug,$log_sql;


    for ($i=0; $row=sql_fetch_array($result); $i++) {   
   
        $mb_no=$row['mb_no'];
        $mb_id=$row['mb_id'];
        $mb_name=$row['mb_name'];
        $mb_level=$row['mb_level'];
        $mb_deposit=$row['mb_deposit_point'];
        $mb_balance=$row['mb_balance'];
        $mb_shop_point = $row['mb_shop_point'];
        $mb_ignore = $row['mb_balance_ignore'];
        $mb_index = $row['mb_index'];
        $grade=$row['grade'];


        $recom= 'mb_center'; //센터멤버
        $sql = " SELECT mb_no, mb_id, mb_name, grade, mb_level, mb_balance,mb_recommend, mb_deposit_point,pv FROM g5_member WHERE {$recom} = '{$mb_id}' ";
        $sql_result = sql_query($sql);
        $sql_result_cnt = sql_num_rows($sql_result);

        
        // $center_bonus = $total_order *($bonus_rate);
        echo "<br><br><span class='title block' style='font-size:30px;'>".$mb_id."</span><br>";
        echo "센터하부회원 : <span class='red'> ".$sql_result_cnt."</span> 명 <br>";
        
        $recom_half_total = 0;
        while( $center = sql_fetch_array($sql_result) ){   
            
            $recom_id = $center['mb_id'];
            $half_bonus_sql = "SELECT SUM(od_cart_price) AS hap FROM g5_order WHERE od_soodang_date BETWEEN '{$half_frdate}' AND '{$half_todate}' AND mb_id = '{$recom_id}' ";
            $half_bonus_result = sql_fetch($half_bonus_sql);

            if($half_bonus_result['hap'] > 0){
                $recom_half_bonus = $half_bonus_result['hap'];
            }else{
                $recom_half_bonus = 0;
            }

            $recom_half_total += $recom_half_bonus;

            echo "<br><br>-<br><br>".$recom_id;
            echo " | 기간내 매출 : <span class='blue'>".Number_format($recom_half_bonus)."</span>";

            $recom_direct_member_sql = "SELECT mb_recommend FROM g5_member WHERE mb_id = '{$recom_id}'";
            $recom_direct_member = sql_fetch($recom_direct_member_sql)['mb_recommend'];

            $sub_benefit = $recom_half_bonus * 0.01;

            if($recom_half_bonus > 0 ){

                echo "<br><br> 추천인  <span class='blue'> ▶ ". $recom_direct_member."</span>  | 센터소개수당(1%) : ".$sub_benefit;
               
                $live_benefit = $sub_benefit * $live_bonus_rate;
                $shop_benefit = $sub_benefit * $shop_bonus_rate;

                $rec = "Bonus By Center ".$recom_id;
                $rec_adm = "CENTER : ".$recom_half_bonus."* 0.01 = ".$sub_benefit." | (P지급".$live_benefit." / SP지급 : ".$shop_benefit.")";

                echo "<span class=blue> ▶▶ 수당 지급 : ".Number_format($sub_benefit)." (P지급".$live_benefit." / SP지급 : ".$shop_benefit.")</span><br>";
                
                $record_sub_result = soodang_record($mb_id, $code, $sub_benefit,$rec,$rec_adm,$bonus_day);
                if($record_sub_result){
                    if($balanace_ignore){
                        $balance_ignore_sql = ", mb_balance_ignore = mb_balance_ignore + {$sub_benefit} ";
                    }else{
                        $balance_ignore_sql = "";
                    }

                    $balance_up = "update g5_member set mb_balance = mb_balance + {$live_benefit} {$balance_ignore_sql}, mb_shop_point = mb_shop_point + {$shop_benefit}   where mb_id = '".$recom_direct_member."'";
        
                    // 디버그 로그
                    if($debug){
                        echo "<code>";
                        print_R($balance_up);
                        echo "</code>";
                    }else{
                        sql_query($balance_up);
                    }
                }
            }
            echo "";

        } 

        $benefit = $recom_half_total * $bonus_rate;
        $direct_benefit = $recom_half_total*0.01;
        
        echo "<br><br>======================================<br><span class='title box'> 기간내 하부 총매출 : <span class='blue'>".Number_format($recom_half_total)."</span></span>";
        echo "<br><span class='red'>".$mb_id. " </span> | 센터수당 : <span class='blue'>".Number_format($benefit)." (".($bonus_rate*100)."%)</span>";
        // echo " | 소개수당 : <span class='blue'>".Number_format($direct_benefit)." (".(1)."%)</span></span><br>";
 
        $total_balance = $mb_balance + $mb_shop_point - $mb_ignore;
        $benefit_limit = $benefit; // 수당합계

        $live_benefit = $benefit_limit * $live_bonus_rate;
        $shop_benefit = $benefit_limit * $shop_bonus_rate;

        $rec=$code.' Bonus By Center:'.$mb_id;
        $rec_adm= 'CENTER | '.$recom_half_total.'*'.$bonus_rate.'='.$benefit." (P지급".$live_benefit." / SP지급 : ".$shop_benefit.")";

        echo "<span class=blue> ▶▶ 수당 지급 : ".Number_format($benefit)." (P지급".$live_benefit." / SP지급 : ".$shop_benefit.")</span><br><br>";
        
        if($benefit > 0 && $benefit_limit > 0){
            
            $record_result = soodang_record($mb_id, $code, $benefit_limit,$rec,$rec_adm,$bonus_day);
            if($record_result){
                if($balanace_ignore){
                    $balance_ignore_sql = ", mb_balance_ignore = mb_balance_ignore + {$benefit_limit} ";
                }else{
                    $balance_ignore_sql = "";
                }

                $balance_up = "update g5_member set mb_balance = mb_balance + {$live_benefit} {$balance_ignore_sql}, mb_shop_point = mb_shop_point + {$shop_benefit}   where mb_id = '".$mb_id."'";
    
                // 디버그 로그
                if($debug){
                    echo "<code>";
                    print_R($balance_up);
                    echo "</code>";
                }else{
                    sql_query($balance_up);
                }
            }

        }
        $recom_half_total = 0;
    } // for

   
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