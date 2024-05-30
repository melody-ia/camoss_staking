<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_PATH . '/util/package.php');

login_check($member['mb_id']);

if ($nw['nw_purchase'] == 'Y') {
	$nw_purchase = 'Y';
	// include_once(G5_PATH.'/service_pop.php');
} else {
	$nw_purchase = 'N';
	alert("현재 서비스를 이용할수없습니다.");
}

$goods_price = wallet_config("re_upstairs")['amt_minimum'];


$title = 'reupstairs';

// $pack_sql = "SELECT it_id, it_name,it_price,it_point,it_supply_point,it_use,it_option_subject, ca_id,ca_id3, it_maker FROM g5_shop_item WHERE it_use > 0 order by it_order asc ";
// $pack_result = sql_query($pack_sql);

$qstr = "stx=" . $stx . "&fr_date=" . $fr_date . "&amp;to_date=" . $to_date;
$query_string = $qstr ? '?' . $qstr : '';

$sql_common = "FROM g5_order";
$sql_search = " WHERE mb_id = '{$member['mb_id']}' and od_cash_no not like 'P%' ";
// $sql_search .= " AND od_date between '{$fr_date}' and '{$to_date}' ";

$sql = " select count(*) as cnt
{$sql_common}
{$sql_search} ";

$row = sql_fetch($sql);

$total_count = $row['cnt'] + $reset_count;

$rows = 15; //한페이지 목록수
$total_page  = ceil($total_count / $rows);
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; // 시작 열

$sql = "SELECT mb_id, od_id, od_cart_price, od_receipt_time, od_name, od_cash, od_settle_case, upstair, od_status,od_date,pv,od_cash_no
{$sql_common}
{$sql_search} ";

$sql .= "order by od_receipt_time desc limit {$from_record}, {$rows} ";

$result = sql_query($sql);
?>

<link rel="stylesheet" href="<?= G5_THEME_URL ?>/css/default.css">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<style>
	.product_buy_wrap .title {
		padding-right: 0;
	}

	.mining_ico {
		vertical-align: middle;
	}

	.mining_ico,
	.mining_ico img {
		margin-left: 5px;
		height: 22px;
	}

	.hash {
		color: white;
		font-weight: 300;
		font-size: 18px;
		letter-spacing: -0.25px;
		margin: 5px 0 -10px;
		font-family: "Helvetica Neue", "Apple SD Gothic Neo", sans-serif;
		font-family: "Helvetica Neue", "Apple SD Gothic Neo", sans-serif;
		background: rgba(0, 0, 0, 0.2);
		padding: 0px 15px 2px 20px;
		border-radius: 10px;
		box-shadow: inset 1px 1px 2px rgb(0 0 0 / 80%), 1px 1px 1px rgb(255 255 255 / 30%);
		text-align: center;
	}

	.dark .pack_name{
    	padding: 2px 10px;
    	display: block;
    	border-radius: 8px;
		width:auto;
	}

	.flex-basis-min{
		flex-basis: min-content;
	}
</style>

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>

<main>
	<div class="container upstairs">
		<div class="upstairs_buy_wrap">
			<div class="package_wrap mt20">
				<div class="box-header">
					<div class="col-9">
						<h3 class="title upper">재구매</h3>
					</div>
				</div>

				<div class="pakage_sale content-box round mt20" id="pakage_sale">
					<div class='row' style="align-items: center;">
						<div class='col-5 current_currency coin'>재구매 금액 </div>
						
						<div class='col-2 shift_usd' style="margin:0;display:inline-flex">
							<i class="ri-indeterminate-circle-line exchange" id="minus"></i>
							<i class="ri-add-circle-line exchange" id="plus"></i>
						</div>

						<div class='col-5'>
							<input type="text" id="trade_total" style="padding-bottom:3px;margin:0;" class="trade_money input_price" placeholder="0" min=5 readonly>
							<!-- <span class='currency-right coin'><?= BALANCE_CURENCY ?></span> -->
							<div id='shift_won'></div>
						</div>
					</div>

					<div class='row select_box' id='usd' style='margin-top:30px'>
						<div class='col-12'>
							<h3 class='tit'> 구매가능잔고</h3>
						</div>

						<div class='col-5 my_cash_wrap'>
							<!-- <input type='radio' value='eth' class='radio_btn' name='currency'><input type="text" id="trade_money_eth" class="trade_money" placeholder="0" min=5 data-currency='eth' readonly> -->
							<div>
								<input type="text" id="total_coin_val" class='input_price' value="<?= shift_auto($total_withraw, $curencys[0]) ?>" readonly>
								<span class="currency-right coin"><?= $curencys[0] ?></span>
							</div>
						</div>

						<div class='col-1 shift_usd'>
							<div class='ex_dollor'><i class="ri-arrow-right-fill"></i></div>
						</div>

						<div class='col-6'>
							<input type="text" id='shift_dollor' class='input_price red' style='text-align:right' readOnly>
							<span class="currency-right coin "><?= $curencys[0] ?></span>
						</div>
					</div>

					<div class="mt20">
						<button id="purchase" class="btn wd main_btn b_blue b_darkblue round">구매</button>
					</div>

				</div>
			</div>

		
			
			

			<!-- <div class="col-sm-12 col-12 content-box round secondary mt20" > -->
				
			<div class="history_box content-box mt40">
				<h3 class="hist_tit title" style="margin-top: 0;">재구매 내역</h3>

				<? if (sql_num_rows($result) == 0) { ?>
					<div class="no_data">재구매 내역이 존재하지 않습니다</div>
				<? } ?>

				<? while ($row = sql_fetch_array($result)) {
					
					$od_name = $row['od_cash_no'];
					$od_settle_case = $row['od_settle_case'];
				?>

					<div class="hist_con">
						<div class="hist_con_row1">
							<div class="row">
								<span class="hist_date"><?= $row['od_receipt_time'] ?></span>
								<span class="hist_value"><?= shift_auto($row['od_cart_price'], $od_settle_case) ?> <?= $od_settle_case ?></span>
							</div>

							<div class="row">
								<h2 class="pack_name pack_f_<?= substr($od_name, 1, 1) ?>"><?= strtoupper($row['od_name']) ?> <?=$od_name?></h2>
								<!-- <span class='hist_sub_price'><?= shift_auto($row['od_cash'], $od_settle_case) ?> <?= $od_settle_case ?></span> -->

					
							</div>
						</div>
					</div>
				<? } ?>

				<?php
				$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=reupstairs&$qstr");
				echo $pagelist;
				?>
			</div>
		</div>
	</div>
</main>

<?php include_once(G5_THEME_PATH . '/_include/tail.php'); ?>

<div class="gnb_dim"></div>

</section>


<!-- <script src="<?= G5_THEME_URL ?>/_common/js/timer.js"></script> -->
<script>
	$(function() {
		$(".top_title h3").html("<span >재구매</span>")
	});

	$(function() {

		let processing = true,
		mb_no = "<?= $member['mb_no'] ?>",
		mb_id = "<?= $member['mb_id'] ?>",
		total_withdraw='<?= shift_auto($total_withraw, $curencys[0]) ?>'.replace(/,/g, ''),
		left_total_withdraw=0,
		goods_price=0,
		tmp_goods_price=0;
		

		// 재구매 +
		$('#plus').on('click',function(){

			tmp_goods_price = goods_price + <?=$goods_price?>;

			if(tmp_goods_price > total_withdraw){
				dialogModal('구매 처리 실패', '<strong>구매가능 잔고가 부족합니다.</strong>', 'warning');
				return false;
			}

			goods_price += <?=$goods_price?>;
			left_total_withdraw = total_withdraw - goods_price;

			$('#trade_total').val(Price(goods_price));
			$('#shift_dollor').val(Price(left_total_withdraw));
		})

		// 재구매 -
		$('#minus').on('click',function(){

			if(goods_price <= 0){
				return false;
			}

			goods_price -= <?=$goods_price?>;
			left_total_withdraw = total_withdraw - goods_price;

			$('#trade_total').val(Price(goods_price));
			$('#shift_dollor').val(Price(left_total_withdraw));
		})

		
		// 패키지구매
		$('#purchase').on('click', function() {
			var nw_purchase = '<?= $nw_purchase ?>'; // 점검코드
			console.log(goods_price);

			// 부분시스템 점검
			if (nw_purchase == 'N') {
				dialogModal('구매 처리 실패', '<strong>현재 이용 가능 시간이 아닙니다.</strong>', 'warning');
				if (debug) console.log('error : 1');
				return false;
			}

			if(goods_price  < 1){
				dialogModal('구매 처리 실패', '<strong>재구매 수량을 입력해주세요</strong>', 'warning');
				return false;
			}

			if(goods_price > total_withdraw){
				dialogModal('구매 처리 실패', '<strong>구매가능 잔고가 부족합니다.</strong>', 'warning');
				return false;
			}

			dialogModal('재구매 확인', '<strong>' + Price(goods_price) + '<?=$curencys[0]?>'+'을 구매 하시겠습니까?</strong>', 'confirm');

			$('#modal_confirm').on('click', function() {
				dimHide();

				if (processing) {
					$.ajax({
						type: "POST",
						url: "/util/reupstairs_proc.php",
						dataType: "json",
						async: false,
						data: {
							goods_price,
							"curencys" : '<?=$curencys[0]?>'
						},
						success: function(data) {

							// 중복클릭방지
							processing = false;
							$('#purchase').attr("disabled", true);

							let alert = "재구매가 정상 처리되었습니다.";
							let state = "success";
							if(data.code == "0001"){
								alert = data.sql;
								state = "failed";
							}
							
							dialogModal('재구매', `<strong>${alert}</strong>`, state);

							$('.closed').on('click', function() {
								location.href = "<?= G5_URL ?>/page.php?id=reupstairs";
							});
						},
						error: function(e) {
							commonModal('재구매 실패!', '<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>', 100);
						}
					});
				} else {
					commonModal('재구매', '<strong> 구매 처리 진행중입니다. 잠시 기다려주세요.</strong>', 80);
				}
			});
		});	
	});

	collapseClosed();
</script>