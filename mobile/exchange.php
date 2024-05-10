<?php
include_once('./_common.php');
include_once(G5_MOBILE_PATH.'/head.php');
include_once(G5_LIB_PATH.'/bootbox/bootbox.php');
if($wallet_addr == ""){
	alert('입금페이지에서 '. $token_symbol .' 지갑생성후 이용해주세요.',G5_URL);
	return false;
  }

  
$mask_sql = "SELECT SUM(order_total) as mask_total FROM sh_shop_order WHERE mb_id='{$member['mb_id']}'";
$mask_row = sql_fetch($mask_sql);

$sql = "SELECT delivery_name, delivery_hp1, delivery_addr1, delivery_addr2, delivery_addr3 FROM sh_shop_order WHERE mb_id = '{$member['mb_id']}' ORDER BY no DESC limit 0, 1";
$result = sql_query($sql);
$row = sql_fetch_array($result);
$name = "";
$tel = "";
$address = "";
$country = "";
if(sql_num_rows($result) > 0){
	$name = $row['delivery_name'];
	$tel = $row['delivery_hp1'];
	$address = $row['delivery_addr1'];
	$country = $row['delivery_addr2'];
	$country_code = $row['delivery_addr3'];
}

if($encrypt == "N"){
	$wallet_key_decrypt = $wallet_key;

}else{
	$key = $member['mb_id'].'@willsoft@';
	$sql_decrypt = "SELECT CAST(AES_DECRYPT(UNHEX('$wallet_key'), '$key') AS CHAR) AS wallet_key FROM g5_member WHERE mb_id='{$member['mb_id']}'";
	$result = sql_query($sql_decrypt);
	if(sql_num_rows($result) > 0){
		$row = sql_fetch_array($result);
		$wallet_key_decrypt = $row['wallet_key'];
	}
}
?>


<script src="/lib/wallet/erc_wallet.js"></script>
<link rel="stylesheet" href="<?=G5_MOBILE_URL?>/css/exchange.css">




	<header class="header">
        
        <a href="javascript:history.back()"><img class="left" src="img/icon_back_bk.png" alt="back_arrow"></a>
		<p class="hd_title">교환/주문하기</p>
		<!-- <a onclick="exitApp();"><img class="home" src="images/icon_home.png" alt="home_btn"></a> -->
	</header>

	<div class="coin_balance">
		<div class="coin_1">
			<div class="cb_left">
				<div class="coin_img">
					<img src="<?=$token_img?>" alt="<?=$token_symbol?>">
				</div>
				<p class="coin_name"><?=$token_symbol?></p>
			</div>
			<div class="cb_right">
				<p class="token_balance"></p>
				<p class="eth_balance"></p>
			</div>
		</div>


		<div class="coin_2">
			<div class="cb_left">
				<div class="coin_img">
					<img src="<?=$point_img?>" alt="<?=$point_symbol?>">
				</div>
				<p class="coin_name"><?=$point_symbol?></p>
			</div>
			<div class="cb_right">
				<p id="cell_cell"><?echo number_format($mask_row['mask_total'])." PACKAGE(S)"?></p>
				<!-- <p class="cell_won"><span class="won_symbol">\</span> 0</p> -->
			</div>
		</div>
	</div>


	<section class="trade">
		<!-- <div class='service_notyet'>
		<div><p>This Service Will be avaiable shortly</p></div>
	</div> -->

	<div class="trade_inner">

		<div class="trade_left">
			<div id="gio">
				<div class="coin_sym">
					<img src="<?=$token_symbol_img?>" alt="<?=$token_symbol?>">
				</div>
				<div class="trade_input_box">
					<input type="text" id="trade_money_1" class="trade_money" disabled>
					<div class="coin_title"><?=$token_symbol?></div>
				</div>
			</div>
		</div>

		<div class="trade_arrow">
			<img src="img/project/icon_arrow_right_gray.png" alt="">
		</div>

		<div class="trade_right">
			<div id="cell">
				<div class="coin_sym">
					<img src="<?=$point_symbol_img?>" alt="<?=$point_symbol?>">
				</div>
				<div class="trade_input_box">
					<input type="text" id="trade_money_2" class="trade_money" disabled>
					<div class="coin_title">MASK</div>
				</div>
			</div>
		</div>
	</div>
</section>



<section id='order_form'>
<h1>Exchange-Order information</h1>

	<form action="" class='trade'>
		<!--  General -->
		<div class="form-group">

			<!-- <div class='item_detail'>
				<h2>하늘숲 항균 마스크 </h2>
				<li>1. 부드러운 밀착감으로 오래 착용해도 귀가 아프지않으며  내구성이 좋다</li>
				<li>2. 뛰어난  통기성으로 편안한 숨쉬기. 습기발생 NO</li>
				<li>3. 물세탁이 가능한 제품으로 마스크 기능은 그대로 경제적. 위생적 제품!</li>
				<li>4. 야외 활동 시  직사광선을 차단해 주며 쿨링효과로 더 시원하게 착용한다.</li>
				<li>5. 3D 항균처리  FITI 시험성적서 및 KC 인증획득</li>
			</div> -->

			<div class='item_detail2'>
			<!-- <p>
				<li>원산지 : 한국 <br> 브랜드 : 하늘숲 <br> 소비자가 : 6,000원</li>

				<li>토큰 교환(구매)가 : 10 VCT-K </li>
				<li>최소 구매가능수량 : 600장 ( 6000 VCT-K )</li>
				<li>배송비 : 전세계무료</li>
				<li>배송소요시간 : </li>
				<li style="text-indent:15px;"> - 하늘숲마스크 주문 후 미국기준 : 10일</li>
				<li style="text-indent:15px;">- 국내 : 5일 (국가별로 다름)</li>
				<hr>
				<li>상담실 : 010 3283 3788</li>
			</p> -->

			<p>
				<li><span style="color:red">원산지 : 대한한국 (Country : Republic of Korea)</span> <br> <span style="color:blue">브랜드 : 하늘숲 (Brand : Sky forest) </span> </li>
				<li>수량: 1800장 (1800EA)</li>
				<li>판매가: 315,000 <?=$token_symbol?> (PRICE: 315,000 <?=$token_symbol?>)</li>		
				<li> 배송비: 무료 (Shipping cost : Domestic free, Overseas free)</li>
				<li>유아마스크 2~6세 (Infant mask 2 to 6 years old)</li>
				<li>어린이마스크 6~12세 (Children's mask 6 to 12 years old)</li>
			</p>
			</div>

			<!-- <div class='item_detail'>
				<h2>빅터 덴탈 마스크 </h2>
			</div>
			<div class='item_detail2'>
			<p>
				<li>품명 : 3중부직포 4 box 200매입</li>
				<li>원산지 : 한국 <br> 브랜드 : 365일3중마스크 <br> 소비자가 : 72,000원</li>
				<li>토큰 교환(구매)가 : 240 VCT-K </li>
				<li>배송비 : (국내)무료 (해외)유료</li>
				<li>배송소요시간 : </li>
				<li style="text-indent:15px;">- 국내 : 5일 (해외)미국기준 10일</li>
				<hr>
				<li>상담실 : 010 3283 3788</li>
			</p>
			</div> -->

			<div class='item_detail'>

			<pre>
국내전용입금
국민은행 (주)큰틀케이 693037-00-003146

외국전용입금
국민은행 (주)큰틀케이 814368-11-012259

이메일상담
Eㆍyeskntl@naver.com

소비자상담실
02-2634-6836
			   </pre>
			</div>

			<h2 style='border-bottom:1px solid #ccc;padding-bottom:10px;font-weight:600;color:#666'>SELECT ORDER ITEM</h2>
			
			<div class='item_box'>
				<div class='item'>
				<p class='item_img'>
			
				<img src='/img/project/item2.jpg'  style="width:99px; height:100px">
				</p>
				<p class=item_name>
					<input type="radio" id="mask1" name="radio-group" class='itm' value="A" checked>
					<label for="mask1">하늘숲<br>유아마스크<br>(2~6세)</label>
				</p>
				</div>

				<div class='item'> 
				<p class='item_img'>
		
				<img src='/img/project/item1.jpg' style="width:99px; height:100px">
				</p>
				<p class=item_name>
					<input type="radio" id="mask2" name="radio-group" class='itm' value="B">
					<label for="mask2">하늘숲<br>어린이마스크<br>(6~12세)</label>
				</p>
				</div>
			

				<!-- <div class='item_box'>
				<div class='item'>
				<p class='item_img' for="mask3"><img src='/wallet/images/victor/item3.jpg' ></p>
				<p class=item_name>
					<input type="radio" id="mask3" name="radio-group" class='itm' value="C">
					<label for="mask3">어린이 마스크</label>
				</p>
				</div>

				<div class='item'>
				<p class='item_img'><img src='/wallet/images/victor/item4.png' ></p>
				<p class=item_name>
					<input type="radio" id="mask4" name="radio-group" class='itm' value="D">
					<label for="mask4" style="font-size:10px;">비말 덴탈 마스크</label>
				</p>
				</div> -->

				</div>
				</div>

			
            <div>
			<p class="select_purchase">* 상품을 선택해주세요.</p>
            </div>
			<div>
				<select id='select_country'>

				</select>
			</div>

			<div class="controls">
				<input type="text" id="name" class="floatLabel" name="name" value='<?=$name?>'>
				<label for="name">받는사람</label>
			</div>
			<!-- <div class="controls">
			<input type="text" id="email" class="floatLabel" name="email">
			<label for="email">이메일</label>
		</div> -->
		<div class="controls">
			<input type="tel" id="phone" class="floatLabel" name="phone" value='<?=$tel?>'>
			<label for="phone">연락처</label>
		</div>
		<div class="grid">
			<div class="col-2-3">
				<div class="controls">
					<input type="text" id="street" class="floatLabel" name="street" value='<?=$address?>'>
					<label for="street">주소</label>
				</div>
			</div>
			<!-- <div class="col-1-3">
			<div class="controls">
			<input type="number" id="street-number" class="floatLabel" name="street-number">
			<label for="street-number">Number</label>
			</div>-->
		</div>
		</div>

	</form>
</section>











<div class="submit">
	<button class="btnCancle" onclick="javascript:history.back()">돌아가기</button>
	<button id="exchange" class="btnOut2" >교환하기</button>
</div>


<!-- callOneCoin.php 의 자바스크립트 변수 받아오려고 적어놓은것  -->
<div id ="balData" style="visibility:hidden;"></div>
<div id ="country_code"  style="visibility:hidden;"></div>
<script>
$(function(){
	initial_web3();
	floatLabel(".floatLabel");

	// $('#trade_money_1').on('keyup',function(e){
		// var won = <?=$exchange_rate?>; //  * 환전 비율
		// $('#trade_money_2').val(Number($('#trade_money_1').val()) * won);
		// if($('input[name="radio-group"]:checked').val() == "A" || $('input[name="radio-group"]:checked').val() == "B"){
			// $('#trade_money_2').val((Number($('#trade_money_1').val() ) / 175));
		// }

		// if($('input[name="radio-group"]:checked').val() == "C"){
		// 	$('#trade_money_2').val(Math.floor( Number($('#trade_money_1').val()/ 23.33333333)));
		// }

		// if($('input[name="radio-group"]:checked').val() == "D"){
		// 	$('#trade_money_2').val(Number($('#trade_money_1').val()) / 10);
		// }
	
	// });

	$('#trade_money_1').val(315000)
	$('#trade_money_2').val((Number($('#trade_money_1').val() ) / 175))

	$('.item').on('click',function(e){
		$(this).find('.itm').prop('checked', true);
	});


	function floatLabel(inputType){
		$(inputType).each(function(){
			var $this = $(this);
			// on focus add cladd active to label

			if($this.val() != ""){
				$this.next().addClass("active");
			}

			$this.focus(function(){
				$this.next().addClass("active");
			});
			//on blur check field and remove class if needed
			$this.blur(function(){
				if($this.val() === '' || $this.val() === 'blank'){
					$this.next().removeClass();
				}
			});
		});
	}

	$.ajax({
   			type: "GET",
               url: "https://<?=ETHERSCAN_ENDPOINT?>.etherscan.io/api?module=gastracker&action=gasoracle",
     	 	cache: false,
     	 	dataType: "json",
     		data:  {
     	    apikey : "<?=$Ether_API_KEY?>"
   		   },
     		 success : function(res){
			
				checked_value = res.result.FastGasPrice

				erc20_contract.methods.balanceOf('<?=$wallet_addr?>').call().then(bal => {
					if(bal/'<?=$token_decimal_numeric?>' <= 0){
						alert("<?=$token_symbol?>"+" 잔고를 확인해주세요.(서비스 이용불가)")
						$('#exchange').prop("disabled",true)
						return false
					}

				});	
				
      		}
		});


	$('#exchange').click(function(){
		//임시잠금

		var checked_mask = $('input[name="radio-group"]:checked').val();
		var name = $('#name').val();
		var phone = $('#phone').val();
		var street = $('#street').val();
		var country = $('.selecttext').text();
		var country_code = $('#country_code').val();
		var send_coin = Number($('#trade_money_1').val());
		var cell_point = Number($('#trade_money_2').val());
		// var to_wallet =  "0xf567B48c6eB8e389D374562923e0dBd5a056cBF7"; // 진짜지갑
		var to_wallet = '<?=TOKEN_COMPANY_ADDR?>';
		var address	= "<?=$wallet_addr?>";
		var key			= "<?=$wallet_key_decrypt?>";
		var mb_id		= "<?echo $member['mb_id'];?>";
		var contract_address = "<?=$token_address?>";
		var decimal = "<?=$token_decimal_numeric?>";
		

		// console.log(checked_mask);

		if(country == "국가를 선택해주세요"){
			alert("국가를 선택해주세요");
			return;
		}

		// if(send_coin == ""){
		// 	alert("수량을 입력해주세요");
		// 	return;
		// }

		// if(checked_mask == "A" || checked_mask == "B"){
		// 	if(send_coin % 100000 != 0){
		// 		alert("315,000 VCT-K 단위로 입력해주세요");
		// 		return;
		// 	}else{
		// 		cell_point = send_coin / 100;
		// 	}
		// }

		// if(checked_mask == "C"){
		// 	if(send_coin % 42000 != 0){
		// 		alert("42,000 VCT-K 단위로 입력해주세요");
		// 		return;
		// 	}else{
		// 		cell_point = Math.floor(send_coin / 23.33333333);
				
		// 	}
		// }
		
		// if(checked_mask == "D"){
		// 	if(send_coin % 100000 != 0){
		// 		alert("100,000 VCT-K 단위로 입력해주세요");
		// 		return;
		// 	}else{
		// 		cell_point = send_coin/10;
		// 	}
		// }

		// if(checked_mask == "D"){
			
		// 	if(send_coin % 240 != 0){
		// 		alert("240 VCT-K 단위로 입력해주세요");
		// 		return;
		// 	}else{
		// 		cell_point = send_coin / 240;
		// 	}

		// }else{

		// 	if(send_coin < 6000){
		// 		alert("최소 교환 가능 수량은 600개 입니다.");
		// 		return;
		// 	}else{
		// 		cell_point = send_coin / 10;
		// 	}
		// }
	
		if(name == "" || phone == "" || street == "" || country == ""){
			alert("주문자 정보가 올바르지 않습니다");
			return;
		}

		if(send_coin > Number($("#balData").val())){
			alert('<?=$token_symbol?>'+" (이)가 부족합니다.");
			return;
		}

	
		var confirm_result = confirm("주문 하시겠습니까?")

		if(confirm_result){
		
		


	estimate_gas('<?=$wallet_addr?>','<?=TOKEN_COMPANY_ADDR?>','<?=TOKEN_CONTRACT?>','<?=$token_decimal_numeric?>',send_coin, (estimateGas,estimateData) => { // 추가

var cal_gas =  estimateGas*web3.utils.toWei(checked_value.toString(), 'gwei') / 1000000000000000000

var user_eth = $(".eth_balance").text().split(" ")

if(cal_gas > Number(user_eth[0])){
alert("수수료(ETH) 가 부족합니다.")
return false
}


			var dialog = bootbox.dialog({
                    message: "<img src='<?php echo G5_MOBILE_URL; ?>/shop/img/loading.gif'><span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>",
                    closeButton: false
             	   });

                $('.modal-dialog').addClass('pay-dialog')
                $('.bootbox-body').addClass('pay-loading')

                $('.pay-dialog').css({
                    'display': 'flex',
                    'justify-content': 'center',
                    'align-items': 'center',
                    'height': '100%',
                    'margin': '0px'
                })
                $('.pay-loading').css({
                    'justify-content': 'space-between',
                    'align-items': 'center',
                    'height': '100px',
                    'flex-flow': 'column',
                    'display': 'flex'
                })



send_token_for_pay('<?=$wallet_addr?>', '<?=TOKEN_COMPANY_ADDR?>', '<?=TOKEN_CONTRACT?>', '<?=$token_decimal_numeric?>', send_coin, '<?=$wallet_key_decrypt?>', checked_value,estimateGas ,(error, res) => {

var after_res = res.split(':');
dialog.modal('hide')
if(after_res[0] == 'success'){


	alert("마스크 주문건이 정상 처리되었습니다. \n처리결과 반영은 일정시간이 소요될수있습니다.");

				$.ajax({
					type: "POST",
					url: "/util/exchange_proc.php",
					cache: false,
					async: false,
					dataType: "json",
					data:  {
						"trade_money" : send_coin,
						"cell_point" : cell_point,
						"sender" : mb_id,
						"hash" : after_res[1],
						"name" : name,
						"phone" : phone,
						"street" : street,
						"country" : country,
						"country_code" : country_code,
						"checked_mask" : checked_mask,
					},
					success: function(res) {
						window.location.reload();
					},
					error: function(error){
						window.location.reload();
					}


				});
 
}else{
alert("문제가 발생하였습니다. 나중에 다시 시도해주세요.");
}




});

	})

}

	})

	$.ajax({
		type: "GET",
		url: "https://restcountries.eu/rest/v2/all",
		cache: false,
		async: false,
		dataType: "json",
		success: function(data) {
			var html = "<option>국가를 선택해주세요</option>";

			for(var i=0; i<data.length; i++){

				html += "<option id='country' style='font-size:14px;line-heihgt:22px;' value="+data[i].callingCodes[0]+">"+data[i].name+"</option>";
			}

			$('#select_country').append(html);
		}
	});


	$("select").each(function(){
		$(this).wrap('<div class="selectbox"/>');
		$(this).after("<span class='selecttext'></span><span class='select-arrow'></span>");

		var val = $(this).children("option:selected").text();
		$(this).next(".selecttext").text(val);
		$(this).change(function(){
			var val = $(this).children("option:selected").text();
			var code = $(this).children("option:selected").val();

			$(this).next(".selecttext").text(val);
			$("#country_code").val(code);
			//$("#phone").val('+'+code);
			// console.log("Country Code : "+code);
		});
	});

	var get_country = "<?=$country?>";
	var get_country_code = "<?=$country_code?>";

	if(get_country != "" && get_country_code != ""){
		// console.log(get_country)
		$(".selecttext").text(get_country);
		$("#country_code").val(get_country_code);
	}
});
</script>

<?php
include_once(G5_MOBILE_PATH.'/tail.php');