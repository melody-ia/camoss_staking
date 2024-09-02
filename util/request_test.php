<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/Telegram/telegram_api.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
// 입금처리 PROCESS
// $debug = 1;

/*현재시간*/
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

$mb_id = 'admin';
$txhash = '입금테스트 : ';
$coin = 'usdt';
$d_price = '30000';


  // 입금알림 텔레그램 API
  $msg = '['.CONFIG_TITLE.'][입금요청] '.$mb_id.'('.$mb_name.') 님의 '.shift_auto($d_price, $curencys[0]).' '.$curencys[0].' 입금요청이 있습니다.';
  curl_tele_sent($msg);

?>
