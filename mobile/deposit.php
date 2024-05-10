<?
include_once('./_common.php');
include_once(G5_MOBILE_PATH.'/head.php');


if($wallet_addr == null || $wallet_addr ==''){
	$make_wallet = "1";
}else {
	$make_wallet = "2";
}

function short_code($string, $char = 8){
	return substr($string,0,$char)." ... ".substr($string,$char*-1);
}
?>

<script src="/lib/qrcode/qrcode.js"></script>
<link rel="stylesheet" href="<?=G5_MOBILE_URL?>/css/deposit.css">

<input type="text" id="token_addr" value="" />


<header class="header">
	<a href="javascript:history.back()"><img class="left" src="img/icon_back_bk.png" alt="back_arrow"></a>
	<p class="hd_title">입금하기</p>
</header>



<div class="coin_balance">
	<div class="cb_left">
		<div class="coin_img"><img src="<?= $token_img ?>" alt="<?= $token_symbol ?>"></div>
		<p class="coin_name"><?= $token_symbol ?></p>
	</div>

	<div class="cb_right">
		<p class="token_balance"></p>
		<p class="eth_balance"></p>
	</div>
</div>



<section class="account">
	<div class="account_inner">
		<p class="ac_title">나의 <?= $token_symbol ?> 지갑 주소</p>
		<p class="token_account" id="token_account"><?= short_code($wallet_addr, 12); ?></p>
		<p class="account_cp" id="accountCopy" onclick='copyURL()'>복사하기</p>

		<p class="qr_title">QR CODE</p>
		<div align="center" class="qr_img" id="qr_img"></div>
	</div>
</section>






<script type="text/javascript">
	$(function() {
		var is_make = "<?= $make_wallet ?>";
		var mb_id = "<?= $member['mb_id'] ?>";

		if (is_make == 1) {
			var web3 = new Web3(new Web3.providers.HttpProvider("https://<?= NETWORK ?>.infura.io/"));
			var acc = web3.eth.accounts.create();

			new QRCode(document.getElementById("qr_img"), {
				text: acc.address,
				width: 125,
				height: 125,
				colorDark: "#000000",
				colorLight: "#ffffff",
				correctLevel: QRCode.CorrectLevel.H
			});

			$('#token_addr').val(acc.address);
			var str = acc.address;
			var str_short = str.substring(0, 12) + " ... " + str.substr(str.length - 12, 12);
			$('.token_account').text(str_short);

			$.ajax({
				type: "POST",
				url: "../util/save_wallet.php",
				dataType: "json",
				data: {
					"network": "<?= NETWORK ?>",
					"mb_id": mb_id,
					"address": acc.address,
					"privateKey": acc.privateKey,
					"encrypt": "<?= $encrypt ?>"
				},
				success: function(res) {

					if (res.code == "0001") {
						alert('새로운지갑이 생성되었습니다.');
						location.reload();
					} else {
						alert("문제가 발생하였습니다. 나중에 다시 시도해주세요.");
					}
				}

			});

		} else {
			var wallet_addr = "<?= $wallet_addr ?>";
			$('#token_addr').val(wallet_addr);

			new QRCode(document.getElementById("qr_img"), {
				text: wallet_addr,
				width: 125,
				height: 125,
				colorDark: "#000000",
				colorLight: "#ffffff",
				correctLevel: QRCode.CorrectLevel.H
			});
		}
	});

	function copyURL() {
		alert($('#token_addr').val() + " 지갑 주소가 복사 되었습니다");
		document.getElementById("token_addr").select();
		document.execCommand("copy");
	}
</script>

<?php
include_once(G5_MOBILE_PATH.'/tail.php');