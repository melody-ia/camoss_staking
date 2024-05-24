<?php
$sub_menu = "600200";
include_once('./_common.php');

// $debug=1;
include_once('./bonus_inc.php');
auth_check($auth[$sub_menu], 'r');

if(!$debug){
    $dupl_check_sql = "select mb_id from soodang_pay where day='".$bonus_day."' and allowance_name = '{$code}' ";
    $get_today = sql_fetch( $dupl_check_sql);

    if($get_today['mb_id']){
        alert($bonus_day.' '.$code." 수당은 이미 지급되었습니다.");
        die;
    }
} 

if( !function_exists( 'array_column' ) ):
    
    function array_column( array $input, $column_key, $index_key = null ) {
    
        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        
        return $result;
    }
endif;



//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_member";
$sql_search=" WHERE pv >= 500000 AND mb_level < 7";
$sql_mgroup=" ORDER BY mb_no asc";

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
$result = $pre_result;
$result_cnt = sql_num_rows($pre_result);


// 롤업설정
$rollup_config = bonus_pick('rollup');

if(strpos($rollup_config['rate'],',') > 0){
    $rollup_rate_1 = explode(',',$rollup_config['rate']);
}
$rollup_rate_array =[];
for($i=0; $i < count($rollup_rate_1); $i++){
    $rollup_rate_array[$i] = explode(':', preg_replace("/[^0-9-:]/", "", $rollup_rate_1[$i]));
}

$rollup_rate_1 = explode(',',$rollup_config['rate']);
$rollup_config['layer'];

$bonus_layer = 20;

// 수당제한 제외 
$balanace_ignore = FAlSE;


ob_start();

// 설정로그 
echo "<span class ='title' style='font-size:20px;'>".$bonus_row['name']." 수당 정산</span><br>";
$rollup_rate = [];
$rollup_layer = [];
$rollup_condition = [];

echo "지급조건 -".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_limit_tx."<br>";

for($i =0; $i < count($rollup_rate_array); $i++){

    array_push($rollup_rate,$rollup_rate_array[$i][0]*10000); 
    array_push($rollup_layer,$rollup_rate_array[$i][1]);
    array_push($rollup_condition,$rollup_rate_array[$i][2]);

    echo "매출 : ".Number_format($rollup_rate[$i]);
    echo " | ".$rollup_layer[$i]." 대";
    echo " | 추천인 : ".$rollup_condition[$i]." 명";
    echo "<br>";
}
// print_R($rollup_rate);

echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick=bonus_url('".$category."')>돌아가기</div>";

function find_rank_index($val,$mem_cnt){
    global $rollup_rate;
    $result = 0;

    if($mem_cnt >= 5){
        if($val >= $rollup_rate[8]) {$result = 8;}
        if($val >= $rollup_rate[7]) {$result = 7;}
        if($val >= $rollup_rate[6]) {$result = 6;}
    }
    
    if($mem_cnt == 4){
        if($val >= $rollup_rate[5]) {$result = 5;}
    }
    
    if($mem_cnt == 3){
        if($val >= $rollup_rate[4]) {$result = 4;}
    }
    
    if($mem_cnt == 2){
        if($val >= $rollup_rate[3]) {$result = 3;}
    }
    
    if($mem_cnt < 2){
        if($val >= $rollup_rate[2]) {$result = 2;}
        if($val >= $rollup_rate[1]) {$result = 1;}
        if($val >= $rollup_rate[0]) {$result = 0;}
        if($val < $rollup_rate[0]) {$result = -1;}
    }

    return $result;

}

function bonus_rate($val){
    if($val <= 3){$result = 10;}
    if($val > 3 && $val <= 15){$result = 5;}
    if($val > 15 && $val <= 20){$result = 2;}
    return $result;
}




?>

<html>

<body>
    <header>정산시작</header>
    <div>

        <?
$mem_list = array();

if($result_cnt > 0){
    excute();
}

// 추천트리 하부 
/* 
function return_down_manager($mb_id,$cnt=0){
	global $config,$g5,$mem_list;

	$mb_result = sql_fetch("SELECT mb_id,mb_rate from g5_member WHERE mb_id = '{$mb_id}' ");
	$result = recommend_downtree($mb_result['mb_id'],0,$cnt);
	return $result;
}


function recommend_downtree($mb_id,$count=0,$cnt = 0){
	global $mem_list;

	if($cnt == 0 || ($cnt !=0 && $count < $cnt)){
		
		$recommend_tree_result = sql_query("SELECT mb_id,mb_rate from g5_member WHERE mb_recommend = '{$mb_id}' ");
		$recommend_tree_cnt = sql_num_rows($recommend_tree_result);

		if($recommend_tree_cnt > 0 ){
			++$count;
			while($row = sql_fetch_array($recommend_tree_result)){
				$list['mb_id'] = $row['mb_id'];
                $list['mb_rate'] = $row['mb_rate'];
				$list['depth'] = $count;
                
				array_push($mem_list,$list);
				recommend_downtree($row['mb_id'],$count,$cnt);
			}
		}
	}
	return $mem_list;
} */

$brcomm_arr = array();
// $test_array = brecommend_array('test6',0,3);
// $mining_matching_sum = array_sum(array_column($test_array, 'mb_rate'));

// print_R($mining_matching_sum);

function brecom_grade($mb_id,$limited =0)
{
    global $config, $brcomm_arr, $debug;
    $origin = $mb_id;

    // 후원 하부 L,R 구분
    list($leg_list, $cnt) = brecommend_direct($mb_id);

    if ($cnt > 1) {

        $L_member = $leg_list[0]['mb_id'];
        $R_member = $leg_list[1]['mb_id'];

        $brcomm_arr = [];
        array_push($brcomm_arr, $leg_list[0]);
        $manager_list_L = brecommend_array($L_member, 0);

        /* echo "<br><br> L ::<br>";
        print_R($manager_list_L); */

        $brcomm_arr = [];
        array_push($brcomm_arr, $leg_list[1]);
        $manager_list_R = brecommend_array($R_member, 0);


        /* echo "<br><br> R ::<br>";
        print_R($manager_list_R); */
    }else{
        return 0;
    } 
}

// 후원트리 하부
function brecommend_array($brecom_id, $count, $limit=0)
{
    global $mem_list;

    // $new_arr = array();
    $b_recom_sql = "SELECT mb_id,mb_name,grade,mb_rate,mb_save_point,mb_brecommend_type,pv from g5_member WHERE mb_brecommend='{$brecom_id}' ";
    $b_recom_result = sql_query($b_recom_sql);
    $cnt = sql_num_rows($b_recom_result);

    if ($cnt < 1) {
        // 마지막
    } else {
        ++$count;
        while ($row = sql_fetch_array($b_recom_result)) {
            brecommend_array($row['mb_id'], $count,$limit);

            // print_R($count.' :: '.$row['mb_id']."<br>");
            // $mem_list[$count]['id'] = $brecom_id;
            if($limit != 0 && $count <= $limit){
                $row['count'] = $count;
                array_push($mem_list, $row);

            }
            
        }
    }
    return $mem_list;
} 

function brecommend_direct($mb_id)
{
    $down_leg = array();
    $sql = "SELECT mb_id,mb_name,grade,mb_rate,mb_save_point,mb_brecommend_type,pv,mb_index FROM g5_member where mb_brecommend = '{$mb_id}' AND mb_brecommend != '' ORDER BY mb_brecommend_type ASC ";
    $sql_result = sql_query($sql);
    $cnt = sql_num_rows($sql_result);

    while ($result = sql_fetch_array($sql_result)) {
        array_push($down_leg, $result);
    }
    return array($down_leg, $cnt);
}


function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition, $code, $rollup_rate,$rollup_layer,$rollup_condition,$pre_condition_in,$bonus_limit,$bonus_layer;
    global $minings,$mining_target,$mining_amt_target,$mem_list,$mining_rate,$now_mining_coin,$mining,$balanace_ignore;
    global $debug;


   
    
    for ($i=0; $row=sql_fetch_array($result); $i++) {   

        $comp=$row['mb_id'];
        $mb_balance = $row['mb_balance'];
        $mb_index = $row['mb_index'];
        $pv = $row['pv'];

        // 직추천자수 
        $mem_cnt_sql = "SELECT count(*) as cnt FROM g5_member where mb_recommend = '{$comp}' AND mb_level > 0 AND pv > 0";
        $mem_cnt_result = sql_fetch($mem_cnt_sql);
        $mem_cnt = $mem_cnt_result['cnt'];

        $item_rank = find_rank_index($pv,$mem_cnt);
       
        $live_bonus_rate = 0.9;
        $shop_bonus_rate = 0.1;

        echo "<br><br><span class='title block gold' style='font-size:30px;'>".$comp."</span><br>";
        if($debug){
            echo "<code> ITEM_RANK :: ".$item_rank."</code>";
        }
        
        
        echo "▶추천인수 : <span class='blue'>" . $mem_cnt . "</span>";

        if($item_rank >= 0){ // 매칭레벨

            $matching_lvl = $rollup_layer[$item_rank];

            // 후원하부
            $brecom_list = brecommend_array($comp,0,$matching_lvl);
            $brecom_list_sum = array_sum(array_column($brecom_list, 'pv'));
            

            echo "<br>";
            echo "▶▶ 보유PV: <strong>".shift_kor($pv)."</strong> | 패키지등급 : ".$item_rank." | 매칭레벨 : <span class='blue'>".$matching_lvl."</span><br> ";
            echo "▶▶▶ 후원라인 하부 <span class='blue'>".$matching_lvl."대</span> 하부PV :: ";
            echo "<span class='blue'>".shift_auto($brecom_list_sum)."</span>";
            echo "<br>";

            if(count($brecom_list) > 0){
                for($k=0; $k < count($brecom_list); $k++ ){   
                    $rows = $brecom_list[$k];
                    
                    $recomm = $rows['mb_id'];
                    $recomm_name = $rows['mb_name'];
                    $count = $rows['count'];
                    
                    // 데일리 수당
                    $daily_soodang = "SELECT allowance_name, day, mb_id, benefit FROM {$g5['bonus']} WHERE day = '{$bonus_day}' AND allowance_name = 'daily' AND mb_id = '{$recomm}' ";
                    $daily_soodang_result = sql_fetch($daily_soodang);
                    $today_sales=$daily_soodang_result['benefit'];

                    // 지급률
                    $bonus_rate = bonus_rate($count); 
                    $bonus_rates = $bonus_rate * 0.01;

                    // 지급보너스
                    $benefit=(($today_sales*0.5)*$bonus_rates);// 매출자 * 수당비율
                    $benefit_point = shift_auto($benefit);
                                 
                    
                    $balance_limit = $mb_index; // 수당한계
                    $benefit_limit = $mb_index - ($mb_balance + $benefit); // 수당합계

                    if($benefit_limit > 0){
                        $benefit_limit = $benefit;
                    }else{
                        $benefit_limit = $benefit_limit*-1;
                    }
                    
                    $benefit_limit_point = shift_auto($benefit_limit);

                    $live_benefit = $benefit_limit * $live_bonus_rate;
                    $shop_benefit = $benefit_limit * $shop_bonus_rate;


                        // 수당 로그
                        echo "<br>".$recomm." | ".$count." 대 :: ".shift_auto($today_sales).'*0.5 *'.$bonus_rates;

                    // 기록용 
                    $rec = $code.' Bonus from '. $recomm.' - '.$count."대  | P =".$live_benefit.", SP = ".$shop_benefit;
                    $rec_adm = ''. $recomm.' - '.$count.'대 :'.shift_auto($today_sales).'*0.5 *'.$bonus_rate.'='.$benefit."P =".$live_benefit.", SP = ".$shop_benefit; 

                    if($benefit > $benefit_limit && $balance_limit != 0 ){

                        $rec_adm .= "<span class=red> |  Bonus overflow :: ".shift_auto($benefit_limit - $benefit)."</span>";
                        echo "<span class=blue> ▶▶ 수당 지급 : ".$benefit_point."</span>";
                        echo "<span class=red> ▶▶▶ 수당 초과 (한계까지만 지급) : ".$benefit_limit_point." </span><br>";

                    }else if($benefit != 0 && $balance_limit == 0 && $benefit_limit == 0){
            
                        $rec_adm .= "<span class=red> | Sales zero :: ".shift_auto(($benefit_limit - $benefit),COIN_NUMBER_POINT)."</span>";
                        echo "<span class=blue> ▶▶ 수당 지급 : ".shift_auto($benefit)."</span>";
                        echo "<span class=red> ▶▶▶ 수당 초과 (기준매출없음) : ".$benefit_limit_point." </span><br>";
                    }else if($benefit == 0){
            
                        echo "<span class=black> ▶▶ 수당 미발생 </span>";
                    }else{
                        echo "<span class=blue>  ▶▶ 수당 지급 : ".$benefit_limit." (P지급".$live_benefit." / SP지급 : ".$shop_benefit.")</span><br>";
                    }

                            // 디버그 로그
                            if($debug){
                                echo "<code>";
                                echo "현재수당 : ".shift_auto($mb_balance)."  | 수당한계 :". shift_auto($balance_limit);
                                echo " | 발생할수당: ".$benefit." | 지급할수당 :".$benefit_limit;
                                echo "</code><br>";
                            }
                    
                    
                   

                    if($benefit > 0 && $benefit_limit > 0){
    
                        $record_result = soodang_record($comp, $code, $benefit_limit,$rec,$rec_adm,$bonus_day);
        
                        if($record_result){
                            
                            if($balanace_ignore){
                                $balance_ignore_sql = ", mb_balance_ignore = mb_balance_ignore + {$benefit_limit} ";
                            }else{
                                $balance_ignore_sql = "";
                            }
    
                            $balance_up = "update g5_member set mb_balance = mb_balance + {$live_benefit} {$balance_ignore_sql}, mb_shop_point = mb_shop_point + {$shop_benefit}   where mb_id = '".$comp."'";
    
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

                }
            }else{
                echo "<span class=blue> ▶▶ 하부라인 없음 </span>";    
            }
        }else{
            echo "<span class=blue> ▶▶ 롤업수당조건 미달 </span>";
        }

        $mem_list = array();
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