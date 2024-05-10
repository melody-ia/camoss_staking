<?
include_once('./_common.php');

$data	= $_POST;

$sender_info_sql  = "select * from g5_member where mb_id= '{$data['sender']}'";
$sender_info = sql_fetch($sender_info_sql);

$fee = 0;

$sql = "insert into sh_shop_order
(`order_code`, 
`mask_type`,
`tot_state`,
`mb_id`,
`goods_order_total`,
`goods_order_delivery`,
`order_total`,
`coupon_money`,
`order_name`,
`order_hp1`,
`delivery_name`,
`delivery_hp1`,
`delivery_addr1`,
`delivery_addr2`,
`delivery_addr3`,
`datetime`) values
('{$data['hash']}',
'{$data['checked_mask']}',
'0',
'{$sender_info['mb_id']}',
'{$data['trade_money']}',
'{$fee}',
'1',
'{$data['cell_point']}',
'{$sender_info['mb_name']}',
'{$sender_info['mb_hp']}',
'{$data['name']}',
'{$data['phone']}',
'{$data['street']}',
'{$data['country']}',
'{$data['country_code']}',
now())";

sql_query($sql);
// $data['order_code'] = $data['hash']; // 거래 해쉬값
// $data['mask_type'] = $data['checked_mask'];
// $data['tot_state'] = 2;//처리상태
// $data['mem_id'] = $sender_info['mb_id']; // 주문자 아이디
// $data['goods_order_total'] = $data['trade_money']; //교환할토큰수량
// $data['goods_order_delivery'] = $fee; // 배달수수료
// $data['order_total'] = $data['cell_point']+$fee; //교환할토큰수량+배달수수료
// $data['coupon_money'] = $data['cell_point']; // 교환요청한 토큰수량

// $data['order_name']   = $sender_info['mb_name']; // 주문자 이름
// $data['order_hp1'] = $sender_info['mb_hp']; // 주문자 폰번호


// $data['delivery_name']   = $data['name']; // 수령인 이름
// $data['delivery_hp1'] = $data['phone']; // 수령인 폰번호
// $data['delivery_addr1'] = $data['street']; // 수령인 주소
// $data['delivery_addr2'] = $data['country']; // 수령인 국가
// $data['delivery_addr3'] = $data['country_code'];

// $data['mode'] = 1;//거래형태
// $data['datetime'] = $date['totime']; // 주문 날짜

// $db_id	= $DB->insertTable(SHOP_ORDER_TABLE, $data);


echo json_encode(array("result" => "success",  "code" => "0001", "sql" => $sql));
?>
