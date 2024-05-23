<?php
$sub_menu = "600200";
include_once('./_common.php');
$debug = false;
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');



//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_order AS o, g5_member AS m ";
$sql_search=" WHERE o.mb_id=m.mb_id AND od_date ='".$bonus_day."'  AND o.od_cash_no LIKE 'P%' AND od_cash !=0 ";
$sql_mgroup=' GROUP BY m.mb_id ORDER BY m.mb_no asc';

$pre_sql = "select count(*) 
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

ob_start();

// 설정로그 
echo "<span class ='title' style='font-size:20px;'>".$bonus_row['name']." 수당 정산</span><br>";
echo "<strong>".strtoupper($code)." 수당 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 -".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx." | ".$bonus_limit_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html><body>
<header>정산시작</header>    
<div>
<?

$sql = "WITH soodang AS ( 
    SELECT o.mb_id,o.od_name,m.mb_recommend, o.upstair, o.upstair * ({$bonus_row['rate']} * 0.01) AS benefit 
    FROM g5_order o JOIN g5_member m ON o.mb_id = m.mb_id 
    WHERE o.od_soodang_date = '{$bonus_day}' AND o.od_cash_no LIKE 'P%' AND o.od_cash !=0
    ), 
    `member` AS ( 
    SELECT mb_no, mb_id, mb_name, mb_level, grade, mb_deposit_point, mb_balance+mb_shop_point-mb_balance_ignore AS total_balance, mb_index 
    FROM g5_member WHERE mb_id IN (select mb_recommend from soodang) 
    ) 
    SELECT m.mb_no,m.mb_id,m.mb_name,m.mb_level,m.grade,s.upstair,s.benefit,s.mb_id AS from_mb_id,m.mb_deposit_point,m.total_balance,m.mb_index,s.od_name
    FROM soodang s JOIN `member` m ON s.mb_recommend = m.mb_id";
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
    global $g5, $bonus_day, $code, $live_bonus_rate, $shop_bonus_rate, $bonus_row;
    global $debug;

    $ids = [];
    $datas= [];
    $member_update = "update g5_member set ";
    $mb_balance_update = "";
    $mb_shop_point_update = "";
    $member_where_update = "where mb_id in (";

    $soodang_pay_insert = "insert into soodang_pay(allowance_name,day,mb_id,mb_no,benefit,mb_level,grade,mb_name,rec,rec_adm,
    origin_balance,origin_deposit,datetime) values";
    $soodang_pay_values_insert = "";

    for ($i=0; $row=sql_fetch_array($result); $i++) {   
        
        $mb_no = $row['mb_no'];
        $mb_id = $row['mb_id'];
        $mb_name = $row['mb_name'];
        $mb_level = $row['mb_level'];
        $grade = $row['grade'];
        $upstair = $row['upstair'];
        $benefit = $row['benefit'];
        $from_mb_id = $row['from_mb_id'];
        $mb_deposit_point = $row['mb_deposit_point'];
        $total_balance = $row['total_balance'];
        $mb_index = $row['mb_index'];
        $od_name = $row['od_name'];

        $ids[$mb_id] = $mb_id;
        $datas[$mb_id]['mb_no'] = $mb_no;
        $datas[$mb_id]['mb_name'] = $mb_name;
        $datas[$mb_id]['mb_level'] = $mb_level;
        $datas[$mb_id]['grade'] = $grade;
        $datas[$mb_id]['upstair'] += $upstair;
        $datas[$mb_id]['benefit'] += $benefit;
        $datas[$mb_id]['from_mb_id'] = $from_mb_id;
        $datas[$mb_id]['mb_deposit_point'] = $mb_deposit_point;
        $datas[$mb_id]['total_balance'] = $total_balance;
        $datas[$mb_id]['limit'] = $mb_index;
        $format_benefit = number_format($benefit);
        $datas[$mb_id]['history'] .= "상품 : {$od_name} | 직추천인 : <span class='red'> {$from_mb_id} </span>
        =><span class='blue'> {$format_benefit} 원</span><br>";

    }

    foreach($ids as $key => $value){

        $data = $datas[$key];

        echo "<br><span class='title block' style='font-size:30px;'>{$key}</span><br>";
        echo "<code>";
        echo "현재수당: ".number_format($data['total_balance'])." 원 | 수당한계: ".number_format($data['limit'])." 원 | 발생수당: ".number_format($data['benefit'])." 원 <br><br>";
        echo "</code>";
        echo $data['history']."<br>";

        $calc_benefit = $data['limit']-($data['benefit']+$data['total_balance']) > 0 ? $data['benefit'] : $data['limit']-$data['total_balance'];
        $expected = clean_number_format($data['benefit']);
        $over_benefit = "";

        if($calc_benefit <= 0){
            $calc_benefit = 0;
            $clean_total_balance = clean_number_format($data['total_balance']);
            $clean_limit = clean_number_format($data['limit']);
            $over_benefit .= "(over benefit : {$clean_total_balance} / {$clean_limit})";
        }

        $balance_benefit = $calc_benefit * $live_bonus_rate;
        $shop_benefit = $calc_benefit * $shop_bonus_rate;

        echo "<code>";
        echo "<span>실제수당: ".clean_number_format($calc_benefit)." 원</span><br>";
        echo "<span>수당: ".clean_number_format($balance_benefit)." 원 | 쇼핑포인트: ".clean_number_format($shop_benefit)." 원</span>";
        echo "</code>";

        if($mb_balance_update == ""){$mb_balance_update .= "mb_balance = case mb_id ";}
        if($mb_shop_point_update == ""){$mb_shop_point_update .= ",mb_shop_point = case mb_id ";}

        $mb_balance_update .= "when '{$key}' then mb_balance + {$balance_benefit} ";
        $mb_shop_point_update .= "when '{$key}' then mb_shop_point + {$shop_benefit} "; 

        $member_where_update .= "'{$key}',";
        $clean_upstair = clean_number_format($upstair);
        $clean_total_balance = clean_coin_format($data['total_balance']);
        $clean_mb_deposit_point = clean_coin_format($data['mb_deposit_point']);

        $clean_balance_benefit = clean_number_format($balance_benefit);
        $clean_shop_benefit = clean_number_format($shop_benefit);

        $soodang_pay_values_insert .= "('{$code}','{$bonus_day}','{$key}',{$data['mb_no']},
        {$calc_benefit},{$data['mb_level']},{$data['grade']},'{$data['mb_name']}',
        '{$code} bonus {$bonus_row['rate']}% : {$clean_balance_benefit} 원, shop bonus : {$clean_shop_benefit} 원 {$over_benefit}',
        '{$clean_upstair}(총 구매액)*{$bonus_row['rate']}%(지급률) {$over_benefit}={$clean_balance_benefit} 원, shop bonus : {$clean_shop_benefit} 원(expected : {$expected} 원)',
        {$clean_total_balance},{$clean_mb_deposit_point},now()),";

    }

    $mb_balance_update .= "else mb_balance end ";
    $mb_shop_point_update .= "else mb_shop_point end ";

    $member_where_update = $member_where_update != "" ? substr($member_where_update,0,-1).")" : "";
    $soodang_pay_values_insert = $soodang_pay_values_insert != "" ? substr($soodang_pay_values_insert,0,-1) : "";

    if(count($ids) > 0){

        $member_sql = $member_update . $mb_balance_update . $mb_shop_point_update . $member_where_update ;
        $log_sql = $soodang_pay_insert.$soodang_pay_values_insert;

        if($debug){
            echo "<code>";
            print_R($member_sql);
            echo "</code>";
            echo "<br>";
            echo "<code>";
            print_R($log_sql);
            echo "</code>";
        }else{
            $result = sql_query($log_sql);
            if($result){
                $result = sql_query($member_sql);
                if(!$result){
                    echo "<code>ERROR:: MEMBER SQL -> {$member_sql}</code>";
                }
            }else{
                echo "<code>ERROR:: LOG SQL -> {$log_sql}</code>";
            }
        }
    }else{
		echo "<span style='display: flex;justify-content: center; color:red;'>정산할 회원이 존재하지 않습니다.</span>";
	}
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