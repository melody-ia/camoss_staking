<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

include_once(G5_THEME_PATH.'/_include/wallet.php');
// $debug =1;

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<?
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/popup.php');

/* function generateRandomCharString($length = 3) {
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
} */

// 리퍼러 체크
referer_check();

if (!($w == '' || $w == 'u')) {
	alert('w 값이 제대로 넘어오지 않았습니다.');
}

if ($w == 'u' && $is_admin == 'super') {
	if (file_exists(G5_PATH.'/DEMO'))
		alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
}

if ($wx=="Y"){
	$mb_id = trim($_POST['mb_id']);
}else{
	// if (!chk_captcha()) {
	// 	alert('자동등록방지 숫자가 틀렸습니다.');
	// }
	if($w == 'u')
		$mb_id = isset($_SESSION['ss_mb_id']) ? trim($_SESSION['ss_mb_id']) : '';
	else if($w == '')
		$mb_id = trim($_POST['mb_id']);
	else
		alert('잘못된 접근입니다', G5_URL);
}

//print_R($_POST);

/*## 회원아이디 자동생성 ################################################*/
/*
if (!$_POST['mb_id']) {
	$mb_id = pkMdMbid(7); // 6자리 sitecode 포함 총 9자리 자동생성
}
*/

if(!$mb_id)
	alert('회원아이디 값이 없습니다. 올바른 방법으로 이용해 주십시오.');


$mb_password    = trim($_POST['mb_password']);
$mb_password_re = trim($_POST['mb_password_re']);
$mb_name        = trim($_POST['mb_name']);
// $mb_nick        = trim($_POST['mb_nick']);
$mb_nick        = '';
$mb_email       = trim($_POST['mb_email']);
$gp             = trim($_POST['gp']);
$mb_sex         = isset($_POST['mb_sex'])           ? trim($_POST['mb_sex'])         : "";
$mb_birth       = isset($_POST['mb_birth'])         ? trim($_POST['mb_birth'])       : "";
$mb_homepage    = isset($_POST['mb_homepage'])      ? trim($_POST['mb_homepage'])    : "";
$mb_tel         = isset($_POST['mb_tel'])           ? trim($_POST['mb_tel'])         : "";
$mb_hp          = isset($_POST['mb_hp'])            ? trim($_POST['mb_hp'])          : "";
$mb_zip1        = isset($_POST['mb_zip'])           ? substr(trim($_POST['mb_zip']), 0, 3) : "";
$mb_zip2        = isset($_POST['mb_zip'])           ? substr(trim($_POST['mb_zip']), 3)    : "";
$mb_addr1       = isset($_POST['mb_addr1'])         ? trim($_POST['mb_addr1'])       : "";
$mb_addr2       = isset($_POST['mb_addr2'])         ? trim($_POST['mb_addr2'])       : "";
$mb_addr3       = isset($_POST['mb_addr3'])         ? trim($_POST['mb_addr3'])       : "";
$mb_addr_jibeon = isset($_POST['mb_addr_jibeon'])   ? trim($_POST['mb_addr_jibeon']) : "";
$mb_signature   = isset($_POST['mb_signature'])     ? trim($_POST['mb_signature'])   : "";
$mb_profile     = isset($_POST['mb_profile'])       ? trim($_POST['mb_profile'])     : "";
$mb_recommend   = isset($_POST['mb_recommend'])     ? trim($_POST['mb_recommend'])   : "";
$mb_brecommend   = isset($_POST['mb_brecommend'])     ? trim($_POST['mb_brecommend'])   : "";
$mb_mailling    = isset($_POST['mb_mailling'])      ? trim($_POST['mb_mailling'])    : 0;
$mb_sms         = isset($_POST['mb_sms'])           ? trim($_POST['mb_sms'])         : 0;
$mb_open        = isset($_POST['mb_open'])           ? trim($_POST['mb_open'])       : 0;
$mb_1           = isset($_POST['mb_1'])             ? trim($_POST['mb_1'])           : "";
$mb_2           = isset($_POST['mb_2'])             ? trim($_POST['mb_2'])           : "";
$mb_3           = isset($_POST['mb_3'])             ? trim($_POST['mb_3'])           : "";
$mb_4           = isset($_POST['mb_4'])             ? trim($_POST['mb_4'])           : "";
$mb_5           = isset($_POST['mb_5'])             ? trim($_POST['mb_5'])           : "";
$mb_6           = isset($_POST['mb_6'])             ? trim($_POST['mb_6'])           : "";
$mb_7           = isset($_POST['mb_7'])             ? trim($_POST['mb_7'])           : "";
$mb_8           = isset($_POST['mb_8'])             ? trim($_POST['mb_8'])           : "";
$mb_9           = isset($_POST['mb_9'])             ? trim($_POST['mb_9'])           : "";
$mb_10          = isset($_POST['mb_10'])            ? trim($_POST['mb_10'])          : "";

$account_name = isset($_POST['account_name']) ? trim($_POST['account_name']) : "";

$mb_name        = clean_xss_tags($mb_name);
$mb_email       = get_email_address($mb_email);
$mb_homepage    = clean_xss_tags($mb_homepage);
$mb_tel         = clean_xss_tags($mb_tel);
$mb_zip1        = preg_replace('/[^0-9]/', '', $mb_zip1);
$mb_zip2        = preg_replace('/[^0-9]/', '', $mb_zip2);
$mb_addr1       = clean_xss_tags($mb_addr1);
$mb_addr2       = clean_xss_tags($mb_addr2);
$mb_addr3       = clean_xss_tags($mb_addr3);
$mb_addr_jibeon = preg_match("/^(N|R)$/", $mb_addr_jibeon) ? $mb_addr_jibeon : '';

$last_name        = trim($_POST['last_name']);
$first_name       = trim($_POST['first_name']);
$mb_mprecommend       = trim($_POST['mb_mprecommend']);
$nation_number    = isset($_POST['nation_number'])            ? trim($_POST['nation_number'])          : 0;
$reg_tr_password    = trim($_POST['reg_tr_password']);

/* if($debug){
	$mb_recommend = 'test77';
	$mb_brecommend = 'test77';
	$mb_name = 'test555';
	$mb_id = 'test111';
	$mb_email = 'arcthan@naver.com';
	$mb_hp = '01088889999';
	$mb_password = 'zx235689';
	$mb_password_re = 'zx235689';
	$reg_tr_password = '235689';
	$reg_tr_password_re = '235689';
	$term = 'on';
} */

$pre_sql = "SELECT mb_no as recom_no, depth+1 as mb_depth FROM g5_member WHERE mb_id ='".$mb_recommend."'";
$result  = sql_fetch($pre_sql);
$mb_recommend_no = $result['recom_no'];

// 1007 추천인과 후원인 동일하게 
$mb_brecommend = $mb_recommend;

$depth = $result['mb_depth'];

if($_POST['mb_center_nick']){
	$mb_center = $_POST['mb_center_nick']; //센터닉네임 사용 by arcthan 2021-09-14
}else{
	$mb_center = 'tanso1';
}


if ($w == '' || $w == 'u') {

	if ($msg = empty_mb_id($mb_id))         alert($msg, "", true, true); // alert($msg, $url, $error, $post);
	if ($msg = valid_mb_id($mb_id))         alert($msg, "", true, true);
	if ($msg = count_mb_id($mb_id))         alert($msg, "", true, true);

	// 이름, 닉네임에 utf-8 이외의 문자가 포함됐다면 오류
	// 서버환경에 따라 정상적으로 체크되지 않을 수 있음.
	$tmp_mb_name = iconv('UTF-8', 'UTF-8//IGNORE', $mb_name);
	if($tmp_mb_name != $mb_name) {
		alert('이름을 올바르게 입력해 주십시오.');
	}

	/* $tmp_mb_nick = iconv('UTF-8', 'UTF-8//IGNORE', $mb_nick);
	if($tmp_mb_nick != $mb_nick) {
		alert('닉네임을 올바르게 입력해 주십시오.');
	} */

	if ($w == '' && !$mb_password)
		alert('Retry Check your E-mail Authentication Code and Input');

	if($w == '' && $mb_password != $mb_password_re)
		alert('비밀번호가 일치하지 않습니다.');


	/*핀코드 패스워드*/
	if($w == '' && $reg_tr_password != $reg_tr_password_re)
		alert('핀코드가 일치하지 않습니다.');


	if ($msg = empty_mb_name($mb_name))       alert($msg, "", true, true);
	//if ($msg = empty_mb_nick($mb_nick))     alert($msg, "", true, true);
	if ($msg = empty_mb_email($mb_email))   alert($msg, "", true, true);
	if ($msg = reserve_mb_id($mb_id))       alert($msg, "", true, true);
	//if ($msg = reserve_mb_nick($mb_nick))   alert($msg, "", true, true);

	// 이름에 한글명 체크를 하지 않는다.
	//if ($msg = valid_mb_name($mb_name))     alert($msg, "", true, true);
	// if ($msg = valid_mb_nick($mb_nick))     alert($msg, "", true, true);
	if ($msg = valid_mb_email($mb_email))    alert($msg, "", true, true);
	if ($msg = prohibit_mb_email($mb_email)) alert($msg, "", true, true);
	//if ($msg = empty_mb_recommend($mb_recommend)) alert($msg, "", true, true);


	// 휴대폰 필수입력일 경우 휴대폰번호 유효성 체크
	/*
	if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {
		if ($msg = valid_mb_hp($mb_hp))     alert($msg, "", true, true);
	}
	*/
	if ($w=='') {
		if ($msg = exist_mb_id($mb_id))     alert($msg);

		if ($wx=="Y"){
			/*
			if ($mb_mprecommend) {
				if (!exist_mp_mb_id($mb_mprecommend))
					alert("MP가 존재하지 않습니다.");
			}
			if ($mb_recommend) {
				if (!exist_mb_id($mb_recommend))
					alert("추천인이 존재하지 않습니다.");
			}
			*/
		}else{
			// 본인확인 체크
			if($config['cf_cert_use'] && $config['cf_cert_req']) {
				if(trim($_POST['cert_no']) != $_SESSION['ss_cert_no'] || !$_SESSION['ss_cert_no'])
					alert("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
			}
			
			if ($mb_brecommend) {
				if (!exist_mb_id($mb_brecommend))
					alert("후원인이 존재하지 않습니다.");
			}
			
			if ($mb_recommend) {
				if (!exist_mb_id($mb_recommend))
					alert("추천인이 존재하지 않습니다.");
			}

			if (strtolower($mb_id) == strtolower($mb_recommend)) {
				alert('본인을 추천할 수 없습니다.');
			}

			if (strtolower($mb_id) == strtolower($mb_center)) {
				alert('본인을 센터멤버로 지정할 수 없습니다.');
			}
		}
	} else {
		// 자바스크립트로 정보변경이 가능한 버그 수정
		// 닉네임수정일이 지나지 않았다면
		/* if ($member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400)))
			$mb_nick = $member['mb_nick']; */
		// 회원정보의 메일을 이전 메일로 옮기고 아래에서 비교함
		$old_email = $member['mb_email'];
	}

	//if ($msg = exist_mb_nick($mb_nick, $mb_id))     alert($msg, "", true, true);
	//if ($msg = exist_mb_email($mb_email, $mb_id))   alert($msg, "", true, true);
}

// 사용자 코드 실행
@include_once($member_skin_path.'/register_form_update.head.skin.php');

//===============================================================
//  본인확인
//---------------------------------------------------------------
// $mb_hp = hyphen_hp_number($mb_hp);
if($config['cf_cert_use'] && $_SESSION['ss_cert_type'] && $_SESSION['ss_cert_dupinfo']) {
	// 중복체크
	$sql = " select mb_id from {$g5['member_table']} where mb_id <> '{$member['mb_id']}' and mb_dupinfo = '{$_SESSION['ss_cert_dupinfo']}' ";
	$row = sql_fetch($sql);
	if ($row['mb_id']) {
		alert("입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원아이디 : ".$row['mb_id']);
	}
}

/*비번찾기??*/
$sql_certify = '';
$md5_cert_no = $_SESSION['ss_cert_no'];
$cert_type = $_SESSION['ss_cert_type'];

if ($config['cf_cert_use'] && $cert_type && $md5_cert_no) {
	// 해시값이 같은 경우에만 본인확인 값을 저장한다.
	if ($_SESSION['ss_cert_hash'] == md5($mb_name.$cert_type.$_SESSION['ss_cert_birth'].$md5_cert_no)) {
		$sql_certify .= " , mb_hp = '{$mb_hp}' ";
		$sql_certify .= " , mb_certify  = '{$cert_type}' ";
		$sql_certify .= " , mb_adult = '{$_SESSION['ss_cert_adult']}' ";
		$sql_certify .= " , mb_birth = '{$_SESSION['ss_cert_birth']}' ";
		$sql_certify .= " , mb_sex = '{$_SESSION['ss_cert_sex']}' ";
		$sql_certify .= " , mb_dupinfo = '{$_SESSION['ss_cert_dupinfo']}' ";
		if($w == 'u')
			$sql_certify .= " , mb_name = '{$mb_name}' ";
	} else {
		$sql_certify .= " , mb_hp = '{$mb_hp}' ";
		$sql_certify .= " , mb_certify  = '' ";
		$sql_certify .= " , mb_adult = 0 ";
		$sql_certify .= " , mb_birth = '' ";
		$sql_certify .= " , mb_sex = '' ";
	}
} else {
	if (get_session("ss_reg_mb_name") != $mb_name || get_session("ss_reg_mb_hp") != $mb_hp) {
		$sql_certify .= " , mb_hp = '{$mb_hp}' ";
		$sql_certify .= " , mb_certify = '' ";
		$sql_certify .= " , mb_adult = 0 ";
		$sql_certify .= " , mb_birth = '' ";
		$sql_certify .= " , mb_sex = '' ";
	}
}


/* Dfine  추천스폰서 */
// $sponsor =return_up_manager($mb_id);
$sponsor = '';


/* OTP
$Base32 = new Base32();
$encoded = $Base32->encode(str_pad($mb_id, 20 , "!&%"));

$sql_otp = ", otp_key = '{$encoded}'";
$sql_otp .= ", otp_flag = 'Y'";
*/

if ($w == '') {
	$sql = " insert into {$g5['member_table']}
				set mb_id = '{$mb_id}',
					 mb_password = '".get_encrypt_string($mb_password)."',
					 mb_name = '{$mb_name}',
					 mb_nick = '{$mb_name}',
					 mb_nick_date = '".G5_TIME_YMD."',
					 mb_email = '{$mb_email}',
					 mb_homepage = '{$mb_homepage}',
					 mb_tel = '{$mb_tel}',
					 mb_zip1 = '{$mb_zip1}',
					 mb_zip2 = '{$mb_zip2}',
					 mb_addr1 = '{$mb_addr1}',
					 mb_addr2 = '{$mb_addr2}',
					 mb_addr3 = '{$mb_addr3}',
					 mb_addr_jibeon = '{$mb_addr_jibeon}',
					 mb_signature = '{$mb_signature}',
					 mb_profile = '{$mb_profile}',
					 mb_today_login = '".G5_TIME_YMDHIS."',
					 mb_datetime = '".G5_TIME_YMDHIS."',
					 mb_ip = '{$_SERVER['REMOTE_ADDR']}',
					 mb_level = '{$config['cf_register_level']}',
					 mb_recommend = '{$mb_recommend}',
					 mb_brecommend = '{$mb_brecommend}',
					 mb_brecommend_type = '{$mb_brecommend_type}',
					 mb_center = '{$mb_center}',
					 mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
					 mb_mailling = '{$mb_mailling}',
					 mb_sms = '{$mb_sms}',
					 mb_open = '{$mb_open}',
					 mb_open_date = '".G5_TIME_YMD."',
					 mb_1 = '{$mb_1}',
					 mb_2 = '{$mb_2}',
					 mb_3 = '{$mb_3}',
					 mb_4 = '{$mb_4}',
					 mb_5 = '{$mb_5}',
					 mb_6 = '{$mb_6}',
					 mb_7 = '{$mb_7}',
					 mb_8 = '{$mb_8}',
					 mb_9 = '{$mb_9}',
					 reg_tr_password  = '".get_encrypt_string($reg_tr_password)."',
					 mb_recommend_no = '{$mb_recommend_no}',
					 depth = '{$depth}',
					 last_name = '{$last_name}',
					 first_name = '{$first_name}',
					 mb_mprecommend = '{$mb_mprecommend}',
					 nation_number = '{$nation_number}',
					 account_name = '{$account_name}',
					 swaped = 2,
					 mb_week_dividend = 1
					 {$sql_certify} ";

	// 이 Email Verification을 사용하지 않는다면 이Email Verification시간을 바로 넣는다
	if (!$config['cf_use_email_certify'])
		$sql .= " , mb_email_certify = '".G5_TIME_YMDHIS."' ";

	//print_r($sql);
	$result = sql_query($sql);


	// if($result){
	// 	$recent_sql = "SELECT id FROM auth_email WHERE email='{$mb_email}' ORDER BY id DESC LIMIT 0,1";
	// 	$row = sql_fetch($recent_sql);
	// 	$update_sql = "UPDATE auth_email set auth_check = '2' WHERE email = '{$mb_email}' AND id = {$row['id']}";
	// 	sql_query($update_sql);
	// }
	
	/////////////////////////////////////////////////////////////////// 아바타 생성 코드
	/*
	$now_date_time = date('Y-m-d H:i:s');
	$avatar_no = 1;
	$avatar_target ='3000';
	$avatar_rate ='10';
	$status = '0';

	$char = generateRandomCharString(2);
    $avatar_id = $mb_id."_".$char.$avatar_no;


	$avatar_sql = "INSERT avatar_savings set
	mb_id             = '".$mb_id."'
	, avatar_no     = '".$avatar_no."'
	, avatar_id     = '".$avatar_id."'
	, saving_target = '".$avatar_target."'
	, saving_rate           = '".$avatar_rate."'
	, current_saving   = '0'
	, status         = '{$status}'
	, setting_date    = '".$now_date_time."'
	, update_date    = '".$now_date_time."'
	, avatar_character    = '".$char."' ";

	sql_query($avatar_sql);
	*/
	///////////////////////////////////////////////////////////////////


	$mb_idx = sql_insert_id();
	/*
	if($mb_mprecommend){ // 마케터가 있는 경우
		$sql = " INSERT INTO mp_soodang ( create_dt, mb_id, mb_mprecommend, commission, usdbtc) VALUES (now(), ";
		$sql .= " '".$mb_id."', ";
		$sql .= " '".$mb_mprecommend."', ";
		$sql .= " 30, ";
		$sql .= " (select btc_cost from coin_cost ) ) ";
		sql_query($sql);
	}
	*/

	if ($wx=="Y"){

	}else{

		// 회원가입 포인트 부여
		insert_point($mb_id, $config['cf_register_point'], '회원가입 축하', '@member', $mb_id, '회원가입');

		// 추천인에게 포인트 부여
		if ($config['cf_use_recommend'] && $mb_recommend)
			insert_point($mb_recommend, $config['cf_recommend_point'], $mb_id.'의 추천인', '@member', $mb_recommend, $mb_id.' 추천');

		// 회원님께 메일 발송
		if ($config['cf_email_mb_member']) {
			$subject = '['.$config['cf_title'].'] Membership Email.';

			$mb_md5 = md5($mb_id.$mb_email.G5_TIME_YMDHIS);

			sql_query(" update {$g5['member_table']} set mb_email_certify2 = '$mb_md5' where mb_id = '$mb_id' ");

			$certify_href = G5_BBS_URL.'/email_certify.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5;
			$toadmin_href = G5_BBS_URL.'/email_toadmin.php?mb_id='.$mb_id.'&amp;mb_email='.$mb_email;

			/* 가입메일 미발송
			ob_start();
			include_once ('./register_form_update_mail1.php');
			$content = ob_get_contents();
			ob_end_clean();

			mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $mb_email, $subject, $content, 1);
			*/

			// 메일인증을 사용하는 경우 가입메일에 인증 url이 있으므로 인증메일을 다시 발송되지 않도록 함
			if($config['cf_use_email_certify'])
				$old_email = $mb_email;
		}

		// 최고관리자님께 메일 발송
		if ($config['cf_email_mb_super_admin']) {
			$subject = '['.$config['cf_title'].'] '.$mb_id .' 님께서 회원으로 가입하셨습니다.';

			ob_start();
			include_once ('./register_form_update_mail2.php');
			$content = ob_get_contents();
			ob_end_clean();

			//mailer($mb_nick, $mb_email, $config['cf_admin_email'], $subject, $content, 1);
		}

		// 메일인증 사용하지 않는 경우에만 로그인
		if (!$config['cf_use_email_certify']){
			//set_session('ss_mb_id', $mb_id);
			}

	   set_session('ss_mb_reg', $mb_id);
	}

	//shell_exec("php ".G5_BBS_PATH."/register.mail.background.php ".$mb_idx." > /dev/null &");

} else if ($w == 'u') {
	//alert('로그인 되어 있지 않습니다.');
	if (!trim($_SESSION['ss_mb_id']))
		alert('로그인 되어 있지 않습니다.');

	if (trim($_POST['mb_id']) != $mb_id)
		alert("로그인된 정보와 수정하려는 정보가 틀리므로 수정할 수 없습니다.\\n만약 올바르지 않은 방법을 사용하신다면 바로 중지하여 주십시오.");

	$sql_password = "";
	if ($mb_password)
		$sql_password = " , mb_password = '".get_encrypt_string($mb_password)."' ";

	$sql_nick_date = "";
	/* if ($mb_nick_default != $mb_nick)
		$sql_nick_date =  " , mb_nick_date = '".G5_TIME_YMD."' ";
 */
	$sql_open_date = "";
	if ($mb_open_default != $mb_open)
		$sql_open_date =  " , mb_open_date = '".G5_TIME_YMD."' ";

	// 이전 메일주소와 수정한 메일주소가 틀리다면 인증을 다시 해야하므로 값을 삭제
	$sql_email_certify = '';
	if ($old_email != $mb_email && $config['cf_use_email_certify'])
		$sql_email_certify = " , mb_email_certify = '' ";

	$sql = " update {$g5['member_table']}
				set mb_nick = '{$mb_nick}',
					mb_mailling = '{$mb_mailling}',
					mb_sms = '{$mb_sms}',
					mb_open = '{$mb_open}',
					mb_email = '{$mb_email}',
					mb_homepage = '{$mb_homepage}',
					mb_tel = '{$mb_tel}',
					mb_zip1 = '{$mb_zip1}',
					mb_zip2 = '{$mb_zip2}',
					mb_addr1 = '{$mb_addr1}',
					mb_addr2 = '{$mb_addr2}',
					mb_addr3 = '{$mb_addr3}',
					mb_addr_jibeon = '{$mb_addr_jibeon}',
					mb_signature = '{$mb_signature}',
					mb_profile = '{$mb_profile}',
					mb_1 = '{$mb_1}',
					mb_2 = '{$mb_2}',
					mb_3 = '{$mb_3}',
					mb_4 = '{$mb_4}',
					mb_5 = '{$mb_5}',
					mb_6 = '{$mb_6}',
					mb_7 = '{$mb_7}',
					mb_8 = '{$mb_8}',
					mb_9 = '{$mb_9}',
					mb_10 = '{$mb_10}',
					last_name = '{$last_name}',
					first_name = '{$first_name}',
					mb_recommend_no = '{$mb_recommend_no}',
					depth = '{$mb_depth}',
					nation_number = '{$nation_number}'
					mb_center = '{$mb_center}',
					{$sql_password}
					{$sql_nick_date}
					{$sql_open_date}
					{$sql_email_certify}
					{$sql_certify}
			  where mb_id = '$mb_id' ";
	sql_query($sql);
}

// 회원 아이콘
$mb_dir = G5_DATA_PATH.'/member/'.substr($mb_id,0,2);

// 아이콘 삭제
if (isset($_POST['del_mb_icon'])) {
	@unlink($mb_dir.'/'.$mb_id.'.gif');
}

$msg = "";

// 아이콘 업로드
$mb_icon = '';
if (isset($_FILES['mb_icon']) && is_uploaded_file($_FILES['mb_icon']['tmp_name'])) {
	if (preg_match("/(\.gif)$/i", $_FILES['mb_icon']['name'])) {
		// 아이콘 용량이 설정값보다 이하만 업로드 가능
		if ($_FILES['mb_icon']['size'] <= $config['cf_member_icon_size']) {
			@mkdir($mb_dir, G5_DIR_PERMISSION);
			@chmod($mb_dir, G5_DIR_PERMISSION);
			$dest_path = $mb_dir.'/'.$mb_id.'.gif';
			move_uploaded_file($_FILES['mb_icon']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);
			if (file_exists($dest_path)) {
				//=================================================================\
				// 090714
				// gif 파일에 악성코드를 심어 업로드 하는 경우를 방지
				// 에러메세지는 출력하지 않는다.
				//-----------------------------------------------------------------
				$size = getimagesize($dest_path);
				if ($size[2] != 1) // gif 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				else
				// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
				if ($size[0] > $config['cf_member_icon_width'] || $size[1] > $config['cf_member_icon_height'])
					@unlink($dest_path);
				//=================================================================\
			}
		} else {
			$msg .= '회원아이콘을 '.number_format($config['cf_member_icon_size']).'바이트 이하로 업로드 해주십시오.';
		}

	} else {
		$msg .= $_FILES['mb_icon']['name'].'은(는) gif 파일이 아닙니다.';
	}
}

// 인증메일 발송

if ($config['cf_use_email_certify'] && $old_email != $mb_email) {
	$subject = '['.$config['cf_title'].'] 인증확인 메일입니다.';

	// 어떠한 회원정보도 포함되지 않은 일회용 난수를 생성하여 인증에 사용
	$mb_md5 = md5(pack('V*', rand(), rand(), rand(), rand()));
	//echo " update {$g5['member_table']} set mb_email_certify2 = '$mb_md5' where mb_id = '$mb_id' ";
	sql_query(" update {$g5['member_table']} set mb_email_certify2 = '$mb_md5' where mb_id = '$mb_id' ");

	$certify_href = G5_BBS_URL.'/email_certify.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5;

	ob_start();
	include_once ('./register_form_update_mail3.php');
	$content = ob_get_contents();
	ob_end_clean();

	//mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $mb_email, $subject, $content, 1);
}

if ($wx=="Y"){

		echo '
		<!doctype html>
		<html lang="ko">
		<head>
		<meta charset="utf-8">
		<title>회원정보등록</title>
		<body>
		<script>
		alert("회원 정보가 정상적으로 등록 되었습니다.");
		';
	if ($gp=="ao"){
			echo 'opener.location.href="/adm/member_org.php?reset=1&now_id='.$mb_recommend.'";';
	}else if ($gp=="at"){
			echo 'opener.location.href="/adm/member_tree.php?reset=1&now_id='.$mb_recommend.'";';
	}else if ($gp=="mt"){
			echo 'opener.location.href="/shop/mypage_tree.php?reset=1&now_id='.$mb_recommend.'";';
	}else if ($gp=="mp"){
		echo 'opener.location.href="/";';
	}
	else{
		echo 'opener.location.href="/";';
	}
		echo '
		self.close();
		</script>
		</body>
		</html>';
	exit;
}

// 신규회원 쿠폰발생
if($w == '' && $default['de_member_reg_coupon_use'] && $default['de_member_reg_coupon_term'] > 0 && $default['de_member_reg_coupon_price'] > 0) {
	$j = 0;
	$create_coupon = false;

	do {
		$cp_id = get_coupon_id();

		$sql3 = " select count(*) as cnt from {$g5['g5_shop_coupon_table']} where cp_id = '$cp_id' ";
		$row3 = sql_fetch($sql3);

		if(!$row3['cnt']) {
			$create_coupon = true;
			break;
		} else {
			if($j > 20)
				break;
		}
	} while(1);

	if($create_coupon) {
		$cp_subject = '신규 회원가입 축하 쿠폰';
		$cp_method = 2;
		$cp_target = '';
		$cp_start = G5_TIME_YMD;
		$cp_end = date("Y-m-d", (G5_SERVER_TIME + (86400 * ((int)$default['de_member_reg_coupon_term'] - 1))));
		$cp_type = 0;
		$cp_price = $default['de_member_reg_coupon_price'];
		$cp_trunc = 1;
		$cp_minimum = $default['de_member_reg_coupon_minimum'];
		$cp_maximum = 0;

		$sql = " INSERT INTO {$g5['g5_shop_coupon_table']}
					( cp_id, cp_subject, cp_method, cp_target, mb_id, cp_start, cp_end, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum, cp_datetime )
				VALUES
					( '$cp_id', '$cp_subject', '$cp_method', '$cp_target', '$mb_id', '$cp_start', '$cp_end', '$cp_type', '$cp_price', '$cp_trunc', '$cp_minimum', '$cp_maximum', '".G5_TIME_YMDHIS."' ) ";

		$res = sql_query($sql, false);

		if($res)
			set_session('ss_member_reg_coupon', 1);
	}
}


// 사용자 코드 실행
@include_once ($member_skin_path.'/register_form_update.tail.skin.php');

unset($_SESSION['ss_cert_type']);
unset($_SESSION['ss_cert_no']);
unset($_SESSION['ss_cert_hash']);
unset($_SESSION['ss_cert_birth']);
unset($_SESSION['ss_cert_adult']);

if ($msg)
	echo '<script>alert(\''.$msg.'\');</script>';

if ($w == '') {
	// goto_url(G5_THEME_URL.'/register_result.php');

	// echo "<script>
	// 	function enroll_result(){
	// 		$('.enroll_ok_pop').css('display', 'block');
	// 	}

	// 	$('.pop_close').click(function() {
	// 		document.location.href= '/index.php';
	// 	});

	// 	enroll_result();
	// 	</script>";
	
	$url = G5_URL.'/util/ajax_get_org_load.php?reset=1';
	//$header_data = array('Authorization: Bearer access_token_value'); //에러 발생

	$ch = curl_init(); //curl 사용 전 초기화 필수(curl handle)

	curl_setopt($ch, CURLOPT_URL, $url); //URL 지정하기
	curl_setopt($ch, CURLOPT_POST, 0); //0이 default 값이며 POST 통신을 위해 1로 설정해야 함
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data); //POST로 보낼 데이터 지정하기
	curl_setopt($ch, CURLOPT_ENCODING ,"");

	curl_setopt($ch, CURLOPT_HEADER, true);//헤더 정보를 보내도록 함(*필수)
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //이 옵션이 0으로 지정되면 curl_exec의 결과값을 브라우저에 바로 보여줌. 이 값을 1로 하면 결과값을 return하게 되어 변수에 저장 가능(테스트 시 기본값은 1인듯?)
	$res = curl_exec ($ch);

	curl_close($ch);

	goto_url(G5_THEME_URL.'/register_result.php');
	
} else if ($w == 'u') {
	$row  = sql_fetch(" select mb_password from {$g5['member_table']} where mb_id = '{$member['mb_id']}' ");
	$tmp_password = $row['mb_password'];

	if ($old_email != $mb_email && $config['cf_use_email_certify']) {
		set_session('ss_mb_id', '');
		alert('회원 정보가 수정 되었습니다.\n\nE-mail 주소가 변경되었으므로 다시 인증하셔야 합니다.', G5_URL);
	} else {
		echo '
		<!doctype html>
		<html lang="ko">
		<head>
		<meta charset="utf-8">
		<title>회원정보수정</title>
		<body>
		<form name="fregisterupdate" method="post" action="'.G5_HTTP_BBS_URL.'/register_form.php">
		<input type="hidden" name="w" value="u">
		<input type="hidden" name="mb_id" value="'.$mb_id.'">
		<input type="hidden" name="mb_password" value="'.$tmp_password.'">
		<input type="hidden" name="is_update" value="1">
		</form>
		<script>
		alert("회원 정보가 수정 되었습니다.");
		document.fregisterupdate.submit();
		</script>
		</body>
		</html>';
	}
}
?>
