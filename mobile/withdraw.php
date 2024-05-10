<?
include_once('./_common.php');
include_once(G5_MOBILE_PATH.'/head.php');
include_once(G5_LIB_PATH."/bootbox/bootbox.php");
if($wallet_addr == ""){
	alert('입금페이지에서 '.$token_symbol.' 지갑생성후 이용해주세요.',G5_URL);
	return false;
  }

$mb_id = $member['mb_id'];
$mylimit = $member['mb_1']; // 제한량
$myamt = $member['mb_2']; // 기존출금량

if($encrypt == "N"){
	$wallet_key_decrypt = $wallet_key;

}else{

	$key = $mb_id.'@willsoft@';
	$sql_decrypt = "SELECT CAST(AES_DECRYPT(UNHEX('$wallet_key'), '$key') AS CHAR) AS wallet_key FROM g5_member WHERE mb_id='$mb_id'";
	$result = sql_query($sql_decrypt);
	if(sql_num_rows($result) > 0){
		$row = sql_fetch_array($result);
		$wallet_key_decrypt = $row['wallet_key'];
	}
}

?>
<script>
	
</script>
<script src="/lib/wallet/erc_wallet.js"></script>
<link rel="stylesheet" href="<?=G5_MOBILE_URL?>/css/withdraw.css">

<body>
	<header class="header">
		<a href="javascript:history.back()"><img class="left" src="img/icon_back_bk.png" alt="back_arrow"></a>
		<p class="hd_title">보내기 / 출금하기</p>
	</header>



	<div class="coin_balance">
		<div class="cb_left">
			<div class="coin_img"><img src="<?=$token_img?>" alt="<?=$token_symbol?>"></div>
			<p class="coin_name"><?=$token_symbol?></p>
		</div>

		<div class="cb_right">
			<p class="token_balance"></p>
			<p class="eth_balance"></p>
		</div>
	</div>



	<form id="sendcoinForm" name="sendcoinForm"  method="post" >
		<section class="account">
			<div class="account_inner">

				<p class="ac_title">받을 지갑 주소</p>
				<input type="text" id="sendAccount" class="wallet_account" name="from_addr" placeholder="주소를 입력해 주세요.">

				<p class="ac_title send_title">보낼 코인 수량</p>
				<input type="text" id="sendCoin" class="send_coin" name="coin_balance" placeholder="보낼 수량을 입력해 주세요.">

				<span style="font-family: 'Noto Sans KR', sans-serif;font-size: 0.8em;color: #353535;"><?=$token_symbol?></span>

			</div>
		</section>


		<div class='title'>전송 수수료</div>
		<div id='select_checkbox'>
			<section>
				<input type="checkbox" id="cb" class="checkbox__native" name="check" onclick="onlyOne(this)" checked/>
				<label for="cb" class="checkbox">
					<div id="slow" class='gas_price'></div><span class='desc'>(느림)</span>
				</label>
			</section>

			<section>
				<input type="checkbox" id="cb1" class="checkbox__native" name="check" onclick="onlyOne(this)"/>
				<label for="cb1" class="checkbox">
					<div id="middle" class='gas_price'></div><span class='desc'>(보통)</span>
				</label>
			</section>

			<section>
				<input type="checkbox" id="cb2" class="checkbox__native" name="check" onclick="onlyOne(this)"/>
				<label for="cb2" class="checkbox">
					<div id="fast" class='gas_price'></div><span class='desc'>(빠름)</span>
				</label>
			</section>
		</div>

		<div class="btn_wrap">
			<button type="button" class="btn account_cp btn_secondary" onclick="javascript:callQRScanner();" >QR코드 스캔하기</button>
			<button type="button" class="btn account_cp" id="exchange">보내기</button>
		</div>

	</form>
	</body>
	<!-- callOneCoin.php 의 자바스크립트 변수 받아오려고 적어놓은것  -->
	<div id ="balData" style="visibility:hidden;"></div>


	<script>


	$(function(){

		initial_web3();

		var contract_address = "<?=$token_address?>";
		var decimal = "<?=$token_decimal_numeric?>";
		var address	= "<?echo $wallet_addr;?>";
		var high_gas_fee = "";
		var normal_gas_fee = "";
		var low_gas_fee = "";
 
		// console.log(`토큰컨트렉트: ${contract_address}\ndecimal: ${decimal}\naddress: ${address}`);
		$.ajax({
   			type: "GET",
      		url: "https://<?=ETHERSCAN_ENDPOINT?>.etherscan.io/api?module=gastracker&action=gasoracle",
     	 	cache: false,
     	 	dataType: "json",
     		data:  {
     	    apikey : "<?=$Ether_API_KEY?>"
   		   },
     		 success : function(res){
				high_gas_fee = res.result.FastGasPrice
				normal_gas_fee= res.result.ProposeGasPrice
				low_gas_fee = res.result.SafeGasPrice 

				erc20_contract.methods.balanceOf(address).call().then(bal => {
					if(bal/decimal <= 0){
						alert("<?=$token_symbol?>"+" 잔고를 확인해주세요.(서비스 이용불가)")
						$('#exchange').prop("disabled",true)
						return false
					}else{
						estimate_pre_gas(address,contract_address,decimal);
					}

				});	
      		}
		});

		$('#exchange').click(function() {

			var mylimit = '<?=$mylimit?>';
			var myamt = '<?=$myamt?>';
			var contract_address = "<?=$token_address?>";
			var decimal = "<?=$token_decimal_numeric?>";
			var balanced = $("#balData").val()*1;
			var send_coin = Number( $("#sendCoin").val() );
			// var to_wallet =  $('#sendAccount').val().trim()
			var address	= "<?echo $wallet_addr;?>";
			// var key			= "<?echo $wallet_key_decrypt; ?>";
			// var mb_id		= "<?echo $mb_id;?>";
			// var limit_coin = Number(balanced)/10;

			// console.log('보유량 :'+ balanced + '/ 출금량 : ' + send_coin+ '/ 제한량 : ' + limit_coin + '/ 기출금량 : ' +myamt + '/ 출금 기준 limit : ' + mylimit);

			// if(to_wallet.length < 14){
			// 	alert("받을 지갑 주소를 확인해주세요");
			// 	return false;
			// }

			//십프로만 출금인데 1000이하는 제한 없이 출금되는걸로

			// 출금하려는 양이 보유량보다 많은경우
			if(send_coin > balanced ){
				console.log('보유량 초과 입력 :' + send_coin + ' / ' + balanced);
				alert("출금액은 보유량을 넘을수 없습니다");
				return false;
			}
			


			// // 보유량 1000개 이상일때
			// if( balanced > 1000){
			//
			// 	// 출금하려는 량이 보유량 기준10%를 넘는경우
			// 	if(send_coin > limit_coin){
			//
			// 	}
			//
			// 	// 기 출금했던 량이 기준10% 보다 많은경우
			// 	if( myamt > (mylimit/10)  ){
			// 		alert("총 출금액은 보유량의 10% 이내만 가능합니다.");
			// 		console.log('리미트 초과');
			// 		return false;
			// 	}
			// }


			// 보유량 1000개 이하일때 전송가능
			if(send_coin>0){

				<?php if($is_webview){ ?>
					callFingerPrint();
					<?php }else{ ?>
						getFingerPrintResult("OK");
						<?php } ?>

					}else{
						alert('받을지갑주소와 수량을 다시한번 체크해주세요');
					}


				

				});


				function estimate_pre_gas(address,contract_address,decimal){
					var to_wallet =  "<?=TOKEN_COMPANY_ADDR?>";
					var send_coin = 0.000000001;
					
					estimate_gas(address,to_wallet,contract_address,decimal,send_coin, (estimateGas,estimateData) => { // 추가


						var estimate_bal = $('.eth_balance').text().split(" ")
					
						if(estimateGas*web3.utils.toWei(high_gas_fee.toString(), 'gwei') / 1000000000000000000 > Number(estimate_bal[0])){
							alert("수수료(ETH)가 부족하여 전송 수수료를 불러올수 없습니다.(서비스 이용불가)")
						return false;
						}
				
						$("#cb").val(low_gas_fee.toString());
						$("#cb1").val(normal_gas_fee.toString());
						$("#cb2").val(high_gas_fee.toString());
						$('#slow').text(estimateGas*web3.utils.toWei(low_gas_fee.toString(), 'gwei') / 1000000000000000000+" ETH");
						$('#middle').text(estimateGas*web3.utils.toWei(normal_gas_fee.toString(), 'gwei') / 1000000000000000000+" ETH");
						$('#fast').text(estimateGas*web3.utils.toWei(high_gas_fee.toString(), 'gwei') / 1000000000000000000+" ETH");
						$('#select_checkbox').show();
					});
				}

			});


			function callQRScanner(){
				App.callQRScanner();
			}

			function getQRScannerResult(param){
				$('#sendAccount').val(param);
			}


			function callFingerPrint(){
				App.callFingerPrint(true);
			}

			function getFingerPrintResult(param){
				if(param === "OK"){

					var contract_address = "<?=$token_address?>";
					var decimal = "<?=$token_decimal_numeric?>";
					var balanced = $("#balData").val()*1;
					var send_coin = Number( $("#sendCoin").val() );
					var to_wallet =  $('#sendAccount').val().trim();
					var address	= "<?echo $wallet_addr;?>";
					var key			= "<?echo $wallet_key_decrypt; ?>";
					var mb_id		= "<?echo $mb_id;?>";
					var transfer_coin = Number(send_coin*decimal);


					var check_address = web3.utils.isAddress(to_wallet)

					if(!check_address){
						alert("유효하지않은 지갑주소입니다. 다시확인해주세요.")
						return;	
					}


					if($("#cb").prop("checked")){
						checked_value = $("#cb").val();
					}

					if($("#cb1").prop("checked")){
						checked_value = $("#cb1").val();
					}

					if($("#cb2").prop("checked")){
						checked_value = $("#cb2").val();
					}

					var dialog = bootbox.dialog({
                    message: "<img src='<?php echo G5_MOBILE_URL; ?>/shop/img/loading.gif'><span>전송중 입니다. 잠시만 기다려 주십시오.</span>",
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

			

					send_token(address, to_wallet, contract_address, decimal, send_coin, key, checked_value, (error, res) => {

						console.log("ERROR :: "+ error);
						console.log("RESULT :: " + res);
						var after_res = res.split(':');
						dialog.modal('hide')
						
						if(after_res[0] == 'success'){
							alert("처리결과 반영은 일정시간이 소요될수있습니다.");
							$.ajax({
								type: "POST",
								url: "../util/withdrawal_proc.php",
								cache: false,
								async: false,
								dataType: "json",
								data:  {
									"send_coin" : send_coin,
									"amt" : transfer_coin,
									"receiver" : to_wallet,
									"eth_addr" : address,
									"sender" : mb_id,
									"balanced" : balanced
								},
								success: function(data) {
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

				}
			}

			function onlyOne(checkbox) {        
				var checkboxes = document.getElementsByName('check')
				checkboxes.forEach((item) => {
					if (item !== checkbox) item.checked = false
				})
			}
			</script>

		
<?php
include_once(G5_MOBILE_PATH.'/tail.php');