<?
//매출액
$mysales = $member['mb_deposit_point'];

//보너스/예치금 퍼센트
// $bonus_per = bonus_state($member['mb_id']);
$bonus_per = bonus_per();

$boner_per_tx = ($limited_per*($bonus_per))*100;

//시세 업데이트 시간
$next_rate_time = next_exchange_rate_time();

//내 직추천인
$direct_reffer_sql = "SELECT count(mb_id) as cnt from g5_member WHERE mb_recommend = '{$member['mb_id']}' AND mb_level = 1";
$direct_reffer_result = sql_fetch($direct_reffer_sql);
$direct_reffer = $direct_reffer_result['cnt'];

//추천산하매출 
$recom_sale =  refferer_habu_sales($member['mb_id']);

//후원산하매출
$brecom_sale =  refferer_habu_sales($member['mb_id'],'b');
// $recom_sale_power = refferer_habu_sales_power($member['mb_id']);
// $recom_sale_weak = ($recom_sale - $recom_sale_power);

// 총보너스 
$total_hash = $member['recom_mining'] + $member['brecom_mining'] + $member['brecom2_mining'] + $member['super_mining'];

// 공지사항
$notice_sql = "select * from g5_write_notice where wr_1 = '1' order by wr_id desc";
$notice_result = sql_query($notice_sql);
$notice_result_num = sql_num_rows($notice_result);



function check_value($val){
	if($val == 1){
		$icon = "<i class='ri-check-line icon value_yes'></i>";
	}else{
		$icon = "<i class='ri-close-line icon value_no'></i>";
	}
	return $icon;
}

function percent_value($value){
	global $mining_hash;
	
	if($value < 1){
		$return  = '-';
	}else{
		$return  = sprintf('%0.2f', ($value/100) );
		$return .= " ".side_exp($mining_hash[0]);
	} 
	return $return;
}

function percent_value_number($val,$rate){
	
	if($val > 0 && $rate > 0){
		$percent_value = Number_format($val/$rate);
	}else{
		$percent_value = 0;
	}
	return $percent_value;
}

function side_exp($val){
	return "<span class='sideexp'>".$val."</span>";
}

function remain_hash($val,$rate,$exp = true){
	global $member;

	if($val > 0){
		$remain_value = Number_format($val/$member['mb_rate']);
		
		if($remain_value > (100*$rate) ){
			$color_code = 'red';
		}else{
			$color_code = '';
		}

		if(!$exp){
			$remain = $remain_value;
		}else{
			$remain = "<span class='remain_mining'><i class='ri-compass-2-fill ".$color_code."'></i>";
			$remain .= "<span class='".$color_code."'>".$remain_value." %</span>";
			$remain .= "</span>";
		}
	}else{
		$remain = '';
	}

	return $remain;
}

$rank_note_price = sql_fetch("SELECT it_price FROM {$g5['g5_item_table']} a JOIN {$g5['member_table']} b ON a.it_name = b.rank_note WHERE b.mb_id = '{$member['mb_id']}'");


$title = 'Dashboard';
?>

<section class='breadcrumb'>
		
		<!-- 공지사항 슬라이드 // 대쉬보드에서만 노출-->
		<?php if($notice_result_num > 0 && $dashboard_page > -1){?>

		<link href="/js/swiper/swiper.min.css" rel="stylesheet">
		<script src="/js/swiper/swiper.min.js"></script>
		<style>
			.swiper-container{width:100%;height:40px;}
		</style>
		
		<section class="notice_wrap_top">
			<a href="/page.php?id=news">
			<div class="swiper-container notice_box">
				<div class="swiper-wrapper">
					<?while($notice_row = sql_fetch_array($notice_result)){?>
						<div class="swiper-slide"><span class='caret'>새소식</span><?=$notice_row['wr_subject']?></div>
					<?}?>
				</div>
			</div>
			</a>
		</section>


		<script>
			var mySwiper = new Swiper('.swiper-container',{
				direction : 'vertical',
				slidesPerView :1,
				autoHeight : true,
				loop:true,
				autoplay: {   
					delay: 4000, 
					disableOnInteraction: false, 
				},
			});
		</script>
		<?php } ?>
		
		
		

		<div class="user-info">
			<!-- 회원기본정보 -->
			
			<div class='user-content' style='border-radius:20px;line-height:40px;'>
				<span class='userid user_level'><?=$user_icon?></span>
				<h4 class='bold'><?=$member['mb_id']?>님</h4>
				<h4 class='mygrade badge color<?=user_grade($member['mb_id'])?>'><?=_user_grade($member['grade'])?></h4>
				<h4 class='mygrade badge' style="margin-left:0;"><?=$user_level?></h4>

				<!-- <?if($notice_result_num > 0){ ?>
					<button class="btn notice_open">
					<i class="ri-broadcast-line"></i>
					</button>
				<?}?> -->
			</div>
			<style>
				.total_view_wrap .currency{font-size:12px;padding-left:3px;}
			</style>
			<!-- 회원상세정보 -->
			<div class="total_view_wrap">
				<div class="total_view_top">
				
					<ul class="row top">
						<li class="col-4">
							<dt class="title" >총 누적 보너스(+CP)</dt>
							<dd class="value" style='font-size:15px;'><?=shift_auto($total_fund,$curencys[0])?><span class='currency'></span></dd>
						</li>
						<li class="col-4">
							<dt class="title" >구매 가능액</dt>
							<dd class="value" style='font-size:15px;'><?=shift_auto($available_fund,$curencys[0])?><span class='currency'></span></dd>
						</li>
						<li class="col-4">
							<dt class="title" >출금 가능액 </dt>
							<dd class="value" style='font-size:15px;'><?=shift_auto($total_withraw,$curencys[0])?><span class='currency'></span></dd>
						</li>
						
					</ul> 
				</div>

				<!-- <div class="total_view_top" id="collapseExample">
					
					<ul class="row">
					<li class="col-4">
							<dt class="title" >직추천인</dt>
							<dd class="value"><?=$direct_reffer?></dd>
						</li>

						<li class="col-4">
							<dt class="title" >구매등급(PV)</dt>
							<dd class="value" ><?=shift_auto($member['pv'], $curencys[0])?> <?=$curencys[0]?></dd>
						</li>

						<li class="col-4">
							<dt class="title">쇼핑몰 포인트</dt>
							<dd class="value"><?=shift_auto($shop_balance,$curencys[0])?><span class='currency'><?=$curencys[0]?></span></dd>
						</li>

					</ul>

					<ul class="row" style="margin:0 10px;">
						<li class="col-12">
							
							<dd class="value">
								<div class='bonus_state_bg' data-per='<?=$bonus_per?>'>
									<div class='bonus_state_bar' id='total_B_bar'>
										<p><?=($boner_per_tx/$limited)*100?></p>
									</div>
								</div>
								
								<div class='exp_per'>
									<p class='start'>0 %</p>
									<p class='end'><?=$limited?> %</p>
								</div>
							</dd>
							<dt class="title" style="font-size:10px;" >수당(순환)한계 (<?=$boner_per_tx?>%)</dt>
						</li>
					</ul>

					<ul class="row mb10">
						<li class="rank_title">다음승급조건달성</li>

						<li class="col-6">
							<dt class="title">구매등급</dt>
							<dd class="value">
								<?=check_value($member['mb_5'])?>
							</dd>
						</li>


						<li class="col-6">
							<dt class="title">승급기준</dt>
							<dd class="value">
								<?=check_value($member['mb_9'])?>
							</dd>
						</li>
					</ul>
					</div>
				</div>
				 -->
				 <div class="fold_wrap">
					<!-- <a href="javascript:collapse('#collapseExample',thisTheme);">
						<div class="collap"><p class='txt'>접기</p></div>
							<div class="fold_img_wrap">
								<img class="updown" src="<?=G5_THEME_URL?>/img/arrow_up.png">
							</div>
						</div>
					</a> -->
				</div>
			</div>
		</div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/egjs-jquery-transform/2.0.0/transform.min.js" integrity="sha512-vOc3jz0QulHRiyMXfp676lHxeSuzUhfuw//VUX12odAmlUbnKiXH4GQxBRqwKhF3Mkswqr5ILY9MtEM4ZwcS2A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- 펼쳐보기 -->
<script>
	
		function collapse(id, mode) {
			console.log("Theme :" + Theme);
			console.log(getCookie('mode'));
			
			$('.fold_img_wrap img').attr('src','<?=G5_THEME_URL?>/img/arrow_up_'+Theme+'.png');
			
			var user_height = $('#collapseExample').height();
			
			if ($(id).css("display") == "none") {
				$(id).css("display", "block");
				$(id).animate({
					height: user_height + 210
				}, 500, function() {
					$('.fold_wrap p').text('접기');
				});
				animateRotate2(0)
			} else {
				$(id).animate({
					height: "0px",
				}, 500, function() {
					$(id).css("display", "none");
					$('.fold_wrap p').text('펼쳐보기');
				});
				animateRotate(180);
			}
		}

		function collapseClosed(){
			target = $('#collapseExample');

			target.animate({
				height: "0px",
			}, 0, function() {
				target.css("display", "none");
				$('.fold_wrap p').text('펼쳐보기');
			});
			animateRotate(180);
		}

		function animateRotate(d) {
			$('.fold_img_wrap').animate({
				'-moz-transform':'rotateX('+d+'deg)',
				'-webkit-transform':'rotateX('+d+'deg)',
				'-o-transform':'rotateX('+d+'deg)',
				'-ms-transform':'rotateX('+d+'deg)',
				'transform':'rotateX('+d+'deg)'
			});
		}

		function animateRotate2(d) {
			$('.fold_img_wrap').animate({
				'-moz-transform':'rotateX('+d+'deg)',
				'-webkit-transform':'rotateX('+d+'deg)',
				'-o-transform':'rotateX('+d+'deg)',
				'-ms-transform':'rotateX('+d+'deg)',
				'transform':'rotateX('+d+'deg)'
			});
		}

	$(document).ready(function(){
		// move(<?=$bonus_per?>,1);

		// 공지사항 - 하단공지로 사용안함
		var notice_open = getCookie('notice');

		if(notice_open == '1'){
			$('.dash_news').css("display","none");
		}else{
			$('.dash_news').css("display","block");
		}

		
		$('.close_news').click(function(){
			$('.dash_news').css("display","none");
			$('.notice_open').css("display","block");
		});

		$('.close_today').click(function(){
			setCookie('notice', '1', 1);
			$('.dash_news').css("display","none");
			$('.notice_open').css("display","block");
		});


		$('.notice_open').click(function(){
			$('.dash_news').css("display","block");
			$(this).css("display","none");
		});

		$(".extra").on('click',function(){
			location.href= g5_url + '/page.php?id=mypool&stage=super#minings'
		})
	});
</script>

