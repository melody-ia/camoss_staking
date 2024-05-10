
<script>
$(function(){
var address = "<?=$wallet_addr?>";
var etherApiKey = "<?=$Ether_API_KEY?>";

  scan_address_list(address,etherApiKey);

  function scan_address_list(address,etherApiKey){ // 유저 지갑으로부터 모든 거래내역을 조회

    $.ajax({
      type: "GET",
      url: "https://<?=ETHERSCAN_ENDPOINT?>.etherscan.io/api?module=account&action=tokentx&startblock=0&endblock=999999999&sort=asc",

      cache: false,
      async: false,
      dataType: "json",
      data:  {
        address : address,
        contractaddress : '<?=TOKEN_CONTRACT?>',
        apikey : etherApiKey
      },
      success: function(data) {
        for(var i =0 ; i < data.result.length ; i++){

          var list = data.result[i];

          if( list.from == address.toLowerCase() ){
            calc = -1;
            math = ' - ';
          }else{
            calc = 1;
            math = ' + ';
          }
     
          var date = nowDate(list.timeStamp * 1000);
          var token_val = list.value / ('<?=$token_decimal_numeric?>' * 1);
          var gas_used = ((list.gasUsed/ 1000000000) * (list.gasPrice/ 1000000000)).toFixed(8);

          var append_txt = " <tr class='item'>";
          append_txt += "<td width='30%' class='date' rowspan='2'>"+date+"</td>";
          append_txt += "<td width='20%'>Transfer</td><td width='20%'>Done</td>";
          append_txt += "<td width='auto' class='coin'>"+ math +" "+ token_val.valueOf()+"</td>";
          append_txt += "</tr> <tr class='gas'>";
          append_txt += "<td colspan=2>Transaction Fee (Gas)</td>";
          append_txt += "<td class='gas_fee'>";

          if(calc == -1){
            append_txt += math + " " +gas_used + " Eth";
          }

          append_txt += "</td></tr> ";


          $('.token_table tbody').prepend(append_txt);


        }
      },
      error:function(e){
      }
    });
  }

});

function nowDate(timestamp){

  var newDate = new Date(timestamp);

  var dateString ='';
  dateString += newDate.getFullYear() + "/";
  dateString += ("0" + (newDate.getMonth() + 1)).slice(-2) + "/";
  dateString += ("0" + newDate.getDate()).slice(-2) + " ";
  dateString += ("0" + newDate.getHours()).slice(-2) + ":";
  dateString += ("0" + newDate.getMinutes()).slice(-2) + ":";
  dateString += ("0" + newDate.getSeconds()).slice(-2);
  console.log(dateString);
  return dateString;
}
</script>
