<?
include_once($cron_path.'/data/dbconfig.php');

$host_name = G5_MYSQL_HOST;
$user_name = G5_MYSQL_USER;
$user_pwd = G5_MYSQL_PASSWORD;
$database = G5_MYSQL_DB;
$conn = mysqli_connect($host_name,$user_name,$user_pwd,$database);


$bonus_day = date("Y-m-d", strtotime(date("Y-m-d")."-1 day"));

$bonus_row = bonus_pick($code);
if($bonus_row['limited'] > 0){
    $bonus_limit = $bonus_row['limited']/100;
}else{
    $bonus_limit = $bonus_row['limited'];
}
$bonus_limit_tx = bonus_limit_tx($bonus_limit);
$bonus_condition = $bonus_row['source'];
$bonus_condition_tx = bonus_condition_tx($bonus_condition);


if(strpos($bonus_row['layer'],',')>0){
    $bonus_layer = explode(',',$bonus_row['layer']);
}else{
    $bonus_layer = $bonus_row['layer'];
}
$bonus_layer_tx = bonus_layer_tx($bonus_layer);


$live_bonus_rate = 0.9;
$shop_bonus_rate = 0.1;


function bonus_pick($val){    
    global $conn;
    $pick_sql = "select * from wallet_bonus_config where code = '{$val}' ";
    $pick_result = mysqli_query($conn, $pick_sql);
    $list = mysqli_fetch_array($pick_result);
    return $list;
}


function bonus_condition_tx($bonus_condition){
    if($bonus_condition == 1){
        $bonus_condition_tx = '추천 계보';
    }else if($bonus_condition == 2){
        $bonus_condition_tx = '후원(바이너리) 계보';
    }else if($bonus_condition == 3){
        $bonus_condition_tx='후원2(바이너리) 계보';
    }
    return $bonus_condition_tx;
}

function bonus_layer_tx($bonus_layer){
    if($bonus_layer == '' || $bonus_layer == '0'){
        $bonus_layer_tx = '전체지급';
    }else{
        $bonus_layer_tx = $bonus_layer.'단계까지 지급';
    }
    return $bonus_layer_tx;
}

function bonus_limit_tx($bonus_limit){
    if($bonus_limit == '' || $bonus_limit == 0){
        $bonus_limit_tx = '상한제한없음';
    }else{
        $bonus_limit_tx = (Number_format($bonus_limit*100)).'% 까지 지급';
    }
    return $bonus_limit_tx;
}


function clean_coin_format($val, $decimal = 8){
	$_num = (int)str_pad("1",$decimal+1,"0",STR_PAD_RIGHT);
	return floor($val*$_num)/$_num;
}

function clean_number_format($val, $decimal = 2){
	$_decimal = $decimal <= 0 ? 1 : $decimal;
	$_num = number_format(clean_coin_format($val,$decimal), $_decimal);
    $_num = rtrim($_num, 0);
    $_num= rtrim($_num, '.');

    return $_num;
}


/* 수당초과 계산 */
function bonus_limit_check($mb_id,$bonus,$kind = '$'){
    global $bonus_limit,$config;

    if($bonus_limit == 0){
        $bonus_limit = 100;
    }

    // $mem_sql="SELECT mb_balance, mb_rate,(SELECT SUM(benefit) FROM soodang_pay WHERE mb_id ='{$mb_id}' AND DAY = '{$bonus_day}') AS b_total FROM g5_member WHERE mb_id ='{$mb_id}' ";
    $mem_sql="SELECT mb_balance, mb_rate, mb_save_point FROM g5_member WHERE mb_id ='{$mb_id}' ";
    $mem_result = sql_fetch($mem_sql);

    $mb_balance = $mem_result['mb_balance'];
    $mb_pv = $mem_result['mb_save_point'] * $bonus_limit;
    
    if($mb_id == 'admin' || $mb_id == $config['cf_admin']){
        $mb_pv = 100000000000;
        $admin_cash = 1;
    }

    if($mb_pv > 0 ){
        if( ($mb_balance + $bonus) < $mb_pv){
            
            $mb_limit = $bonus;
        }else{
            
            $mb_limit = $mb_pv - $mb_balance;
            if($mb_limit < 0){
                $mb_limit = 0;
            }
        }
    }else{
        $mb_limit = 0;
    }
    
    return array($mb_balance,$mb_pv,$mb_limit,$admin_cash);
}


function soodang_record($mb_id, $code, $bonus_val,$rec,$rec_adm,$bonus_day,$mb_no='',$mb_level = '', $mb_name = ''){
    global $g5,$debug,$now_datetime;

    $soodang_sql = " insert `{$g5['bonus']}` set day='".$bonus_day."'";
    $soodang_sql .= " ,mb_id			= '".$mb_id."'";
    $soodang_sql .= " ,allowance_name	= '".$code."'";
    $soodang_sql .= " ,benefit		=  ".$bonus_val;	
    $soodang_sql .= " ,rec			= '".$rec."'";
    $soodang_sql .= " ,rec_adm		= '".$rec_adm."'";
    $soodang_sql .= " ,datetime		= '".$now_datetime."'";

    if($mb_no != ''){
        $soodang_sql .= " ,mb_no		= '".$mb_no."'";
    }
    if($mb_level != ''){
        $soodang_sql .= " ,mb_level		= '".$mb_level."'";
    }
    if($mb_name != ''){
        $soodang_sql .= " ,mb_name		= '".$mb_name."'";
    }

    // 수당 푸시 메시지 설정
    /* $mb_push_data = sql_fetch("SELECT fcm_token,mb_sms from g5_member WHERE mb_id = '{$mb_id}' ");
    $push_agree = $mb_push_data['mb_sms'];
    $push_token = $mb_push_data['fcm_token'];

    $push_images = G5_URL.'/img/marker.png';
    if($push_token != '' && $push_agree == 1){
        setPushData("[DFINE] - ".$mb_id." 수당 지급 ", $code.' =  +'.$bonus_val.' ETH', $push_token,$push_images);
    } */
    
    if($debug){
        echo "<code>";
        print_r($soodang_sql);
        echo "</code>";
        return true;
    }else{
        return sql_query($soodang_sql);
    }
}

function sql_query($result){
    global $conn;

    return mysqli_query($conn, $result);
}

function sql_num_rows($result){
    return mysqli_num_rows($result);
}

function sql_fetch($result){
   $row = sql_query($result);
   $row_result = mysqli_fetch_array($row);
   return $row_result;
}

function sql_fetch_array($result){
    return mysqli_fetch_array($result);
}



// 원 표시
function shift_kor($val){
	return Number_format($val, 0);
}

// 달러 표시
function shift_doller($val){
	return Number_format($val, 2);
}

// 코인 표시
function shift_coin($val){
	return Number_format($val, COIN_NUMBER_POINT);
}

// 달러 , ETH 코인 표시
function shift_auto($val,$coin = '원'){
	if($coin == '$'){
		return shift_doller($val);
	}else if($coin == '원'){
		return shift_kor($val);
	}else{
		return shift_coin($val);
	}
}


/* 추천인 트리 */
$mem_list = [];

/* 추천상위매니저 검색 */
function return_up_manager($mb_id,$cnt=0){
	global $config;
	$origin = $mb_id;
	$manager_list = [];
	$i = 0;
    
    if($mb_id != 'admin' && $mb_id != $config['cf_admin']){
		
		if($cnt == 0){
			do{
				$manager = recommend_uptree($mb_id);
				$mb_id = $manager;
				array_push($manager_list,$manager);
			}while( 
				$manager != 'khan'
			);
		
			if(count($manager_list) < 2){
				return $origin;
			}else{
				return $manager_list[count($manager_list)-2];
			}
		}else{
			do{
				$i++;
				$manager = recommend_uptree($mb_id);
				$mb_id = $manager;
				array_push($manager_list,$manager);
			}while( $i < $cnt );

			return $manager_list[$cnt-1];
		}
    }else{
        return $mb_id;
    }
}

function recommend_uptree($mb_id){
    $result = sql_fetch("SELECT mb_recommend,mb_level from g5_member WHERE mb_id = '{$mb_id}' ");
    return $result['mb_recommend'];
}


/* 추천하부매니저 검색 */
function return_down_manager($mb_no,$cnt=0){
	global $config,$g5,$mem_list;

	$mb_result = sql_fetch("SELECT mb_id,mb_name,mb_level,grade,mb_rate,rank,recom_sales from g5_member WHERE mb_no = '{$mb_no}' ");
	$list = [];
	$list['mb_id'] = $mb_result['mb_id'];
	$list['mb_name'] = $mb_result['mb_name'];
	$list['mb_level'] = $mb_result['mb_level'];
	$list['grade'] = $mb_result['grade'];
	$list['depth'] = 0;
	$list['mb_rate'] = $mb_result['mb_rate'];
	$list['recom_sales'] = $mb_result['recom_sales'];
	$list['rank'] = $mb_result['rank'];
	
	$mb_add = sql_fetch("SELECT COUNT(mb_id) as cnt,IFNULL( (SELECT noo  from  recom_bonus_noo WHERE mb_id = '{$mb_result['mb_id']}' ) ,0) AS noo FROM g5_member WHERE mb_recommend = '{$mb_result['mb_id']}' ");
	
	$list['cnt'] = $mb_add['cnt'];
	$list['noo'] = $mb_add['noo'];

	$mem_list = [$list];
	$result = recommend_downtree($mb_result['mb_id'],1,$cnt);
	// print_R(arr_sort($result,'count'));
	// prinT_R($result);
	return $result;
}


function recommend_downtree($mb_id,$count=0,$cnt = 0){
	global $mem_list;

	if($cnt == 0 || ($cnt !=0 && $count < $cnt)){
		
		$recommend_tree_result = sql_query("SELECT mb_id,mb_name,mb_level,grade,mb_rate,rank,recom_sales from g5_member WHERE mb_recommend = '{$mb_id}' ");
		$recommend_tree_cnt = sql_num_rows($recommend_tree_result);
		if($recommend_tree_cnt > 0 ){
			++$count;

			while($row = sql_fetch_array($recommend_tree_result)){
				$list['mb_id'] = $row['mb_id'];
				$list['mb_name'] = $row['mb_name'];
				$list['mb_level'] = $row['mb_level'];
				$list['grade'] = $row['grade'];
				$list['mb_rate'] = $row['mb_rate'];
				$list['recom_sales'] = $row['recom_sales'];
				$list['rank'] = $row['rank'];
				
				$mb_add = sql_fetch("SELECT COUNT(mb_id) as cnt,IFNULL( (SELECT noo  from  recom_bonus_noo WHERE mb_id = '{$row['mb_id']}' ) ,0) AS noo FROM g5_member WHERE mb_recommend = '{$row['mb_id']}' ");
	
				$list['cnt'] = $mb_add['cnt'];
				$list['noo'] = $mb_add['noo'];
				$list['depth'] = $count;
				array_push($mem_list,$list);
				recommend_downtree($row['mb_id'],$count,$cnt);
			}
		}
	}
	return $mem_list;
}




$brcomm_arr = [];
// 후원인 하부 회원 
function return_brecommend($mb_id,$limit,$binding = false,$where = 1 ){
	global $config, $brcomm_arr, $debug;
	$origin = $mb_id;

	list($leg_list, $cnt) = brecommend_direct($mb_id,$where);

	$L_member = $leg_list[0]['mb_id'];
	$R_member = $leg_list[1]['mb_id'];

	// echo "L : ".	$L_member;
	// echo "R : ".	$R_member;
	
	if($L_member){
		$brcomm_arr_L = array();
		array_push($brcomm_arr_L, $leg_list[0]);
		$manager_list_L = brecommend_array($L_member, 1 , $limit,$where);
		$brcomm_arr_L = array_merge($brcomm_arr_L,arr_sort($manager_list_L,'count'));
	}else{
		$brcomm_arr_L = [];
	}
	$brcomm_arr  = array();
	
	if($R_member){
		$brcomm_arr_R = array();
		array_push($brcomm_arr_R, $leg_list[1]);
		$manager_list_R = brecommend_array($R_member, 1 , $limit);
		$brcomm_arr_R = array_merge($brcomm_arr_R,arr_sort($manager_list_R,'count'));
	}else{
		$brcomm_arr_R = [];
	}

	$brcomm_arr  = array();
	
	if(!$binding){
		return array($brcomm_arr_L,$brcomm_arr_R); 
	}else{
		return array_merge($brcomm_arr_L,$brcomm_arr_R);
	}
	
}
/* 
function brecommend_array($brecom_id, $count, $limit =0,$where =1)
{
	global $brcomm_arr;

	// $new_arr = array();
	if($where == 2){
		$where = 2;
		$b_recom_sql = "SELECT A.mb_id,A.mb_brecommend_type,B.grade,B.mb_rate,B.mb_save_point, {$count}  AS count FROM g5_member_binary A LEFT JOIN g5_member B ON A.mb_id = B.mb_id WHERE A.mb_brecommend = '{$brecom_id}' AND A.mb_brecommend != '' ORDER BY mb_brecommend_type ASC";
	}else{
		$b_recom_sql = "SELECT mb_id,grade,mb_rate,mb_save_point,mb_brecommend_type, {$count} as count from g5_member WHERE mb_brecommend='{$brecom_id}' ORDER BY mb_brecommend_type ASC ";
	}
	$b_recom_result = sql_query($b_recom_sql);
	$cnt = sql_num_rows($b_recom_result);
	
	if($limit != 0 && $count >= $limit){
		
	}else{
		if ($cnt < 1) {
			// 마지막
		} else {
			++$count;

			while ($row = sql_fetch_array($b_recom_result)) {
				brecommend_array($row['mb_id'], $count, $limit,$where);
				// print_R($count.' :: '.$row['mb_id'].' | type ::'.$row['grade']);
				// $brcomm_arr[$count]['count'] = $count;
				array_push($brcomm_arr, $row);
			}
			
		}
	}
	
	return $brcomm_arr;
} */

/* 
function brecommend_direct($mb_id,$where = 1)
{

	$down_leg = array();
	if($where == 2){
		$sql = "SELECT A.mb_id,A.mb_brecommend_type,B.grade,B.mb_rate,B.mb_save_point, 1 AS count FROM g5_member_binary A LEFT JOIN g5_member B ON A.mb_id = B.mb_id WHERE A.mb_brecommend = '{$mb_id}' AND A.mb_brecommend != '' ORDER BY mb_brecommend_type ASC";
	}else{
		$sql = "SELECT mb_id,grade,mb_rate,mb_save_point,mb_brecommend_type, 1 AS count FROM g5_member where mb_brecommend = '{$mb_id}' AND mb_brecommend != '' ORDER BY mb_brecommend_type ASC ";
	}
	$sql_result = sql_query($sql);
	$cnt = sql_num_rows($sql_result);

	while ($result = sql_fetch_array($sql_result)) {
		array_push($down_leg, $result);
	}
	return array($down_leg, $cnt);
}
 */


// 배열정렬 + 지정값 이상 카운팅
function array_index_sort($list, $key, $average)
{
	$count = 0;
	$a = array_count_values(array_column($list, $key));

	foreach ($a as $key => $value) {

		if ($key >= $average) {
			$count += intval($value);
		}
	}
	return array($a, $count);
}

// php 버전 대응 패치
if( !function_exists( 'array_column' ) ):
    
    function array_column( array $input, $column_key, $index_key = null ) {
    
        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        
        return $result;
    }
endif;


// 배열정렬 
function arr_sort($array, $key, $sort='asc') {
	$keys = array();
	$vals = array();

	foreach ($array as $k=>$v) {
		$i = $v[$key].'.'.$k;
		$vals[$i] = $v;
		array_push($keys, $k);
	}

	unset($array);

	if ($sort=='asc') {
		ksort($vals);
	} else {
		krsort($vals);
	}

	$ret = array_combine($keys, $vals);
	unset($keys);
	unset($vals);

	return $ret;
}

/* 결과 합계 중복제거*/
function array_index_sum($list, $key,$category)
{
	$sum = null;
	$count = 0;
	$a = array_count_values(array_column($list, $key));
	

	foreach ($a as $key => $value) {
		
		if($category == 'int'){
			// echo $key." ";
			$sum += $key; 
			// echo "= ".$sum."<br>";
		}else if ($category == 'text'){
			$sum .= $key.' | '; 
		}
	}
	return $sum;
}

/* 결과 합계 */
function array_int_sum($list, $key){
	return array_sum(array_column($list, $key));
}


// 경고메세지를 경고창으로
function alert($msg='', $url='', $error=true, $post=false)
{
    global $g5, $config, $member, $is_member, $is_admin, $board;

    run_event('alert', $msg, $url, $error, $post);

    $msg = $msg ? strip_tags($msg, '<br>') : '올바른 방법으로 이용해 주십시오.';

    $header = '';
    if (isset($g5['title'])) {
        $header = $g5['title'];
    }
    include_once(G5_BBS_PATH.'/alert.php');
    exit;
}
?>