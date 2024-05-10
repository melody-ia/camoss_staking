<?php
/*

이 클래스는 한 지갑에 있는 모든 ERC20 TOKEN,이더리움의 잔액을 가져옵니다

1. scan_address_list 함수
1)지정된 토큰의 거래내역을 조회

2.비동기방식으로 web3를 지정된 컨트랙트 주소로 초기화 시키고 요청을 보냄  (asyncCall 함수)
1) 토큰 잔액 조회
2) 이더리움 잔액조회

*/
if(NETWORK == 'mainnet'){
	$wallet_addr = $member['erc20w'];
	$wallet_key = $member['erc20pk'];
}else{
	$wallet_addr = $member['erc20w_test'];
	$wallet_key = $member['erc20pk_test'];
}
?>

<script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.35/dist/web3.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script> -->

<script>
$(function(){

  var web3;
  var erc20_contract;
  var debug = "<?=$is_debug?>";
  var web3_endpoint = "<?=WEB3_ENDPOINT?>";
  var address = "<?=$wallet_addr?>"; // 지갑 주소
  var etherApiKey = "<?=$Ether_API_KEY?>"; // 이더스캔 api 키
  var contract = "<?=$token_address?>";
  var tokenSymbol = "<?=$token_symbol?>";
  var tokenDecimal = "<?=$token_decimal?>";


   var erc20_abi = [{"constant":false,"inputs":[{"name":"newSellPrice","type":"uint256"},{"name":"newBuyPrice","type":"uint256"}],"name":"setPrices","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_value","type":"uint256"}],"name":"burn","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"sellPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"target","type":"address"},{"name":"mintedAmount","type":"uint256"}],"name":"mintToken","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_value","type":"uint256"}],"name":"burnFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"buyPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"buy","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"frozenAccount","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"},{"name":"_extraData","type":"bytes"}],"name":"approveAndCall","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"amount","type":"uint256"}],"name":"sell","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"target","type":"address"},{"name":"freeze","type":"bool"}],"name":"freezeAccount","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"initialSupply","type":"uint256"},{"name":"tokenName","type":"string"},{"name":"tokenSymbol","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"name":"target","type":"address"},{"indexed":false,"name":"frozen","type":"bool"}],"name":"FrozenFunds","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_owner","type":"address"},{"indexed":true,"name":"_spender","type":"address"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Burn","type":"event"}]

  if(debug == 1)
  console.log(`[Debug mode] \n\nNetwork: <?=NETWORK?>\n지갑주소 : ${address}\n토큰컨트렉트: ${contract}`);


  asyncCall(erc20_abi,web3_endpoint,address,contract,tokenSymbol,tokenDecimal,1*100);

  async function asyncCall(erc20_abi,endpoint,address,contract,symbol,decimal,time){
    await call(erc20_abi,endpoint,address,contract,symbol,decimal,time);
  }

  function call(erc20_abi,endpoint,address,contract,symbol,decimal,time){

    return new Promise(resolve =>{

      setTimeout(()=>{

        var token_symbol = symbol;
        var ethereum_endpoint = endpoint;
        var contract_address = contract;

        // if (typeof web3 !== 'undefined') {
        //   web3 = new Web3(web3.currentProvider);
        // }else{
          web3 = new Web3(new Web3.providers.HttpProvider(ethereum_endpoint));
          erc20_contract = new web3.eth.Contract(erc20_abi, contract_address);
        // }

        erc20_contract.methods.balanceOf(address).call().then(res => {
          var num = "1";
          for(var i = 0; i < decimal; i++){ // 모든 토큰의 decimal이 틀리기 때문에 그 토큰의 decimal 크기만큼 0 갯수를 늘림
            num +="0";
          }
          num *= 1; // String -> int 로 변경
          var bal = ((Number(res)/num).toFixed(2)).toLocaleString();
          var parts=bal.toString().split(".");

          console.log((parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "")+" "+token_symbol));

           $(".token_balance").text((parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "")+" "+tokenSymbol));
           $("#balData").val(bal);
        });

        web3.eth.getBalance(address, function(err, res) {  // 이더리움 조회
          var eth = (res/1000000000000000000).toFixed(8);
          console.log(eth+" ETH")
          $(".eth_balance").text(eth+" ETH");
        });

      },time);
    });
  }
});


</script>
