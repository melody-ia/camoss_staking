<?php
include_once('./_common.php');
include_once(G5_MOBILE_PATH.'/head.php');

if($wallet_addr == ""){
  alert('입금페이지에서 '.$token_symbol.' 지갑생성후 이용해주세요.',G5_URL);
  return false;
}

include_once(G5_PATH."/util/callOneCoinHistory.php");



$mask_sql = "SELECT SUM(order_total) as mask_total FROM sh_shop_order WHERE mb_id='{$member['mb_id']}' ";
$mask_row = sql_fetch($mask_sql);

$mask_history_sql = "select * from sh_shop_order where mb_id='{$member['mb_id']}' order by no desc";
$mask_history_result = sql_query($mask_history_sql);

?>

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.4.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" href="<?=G5_MOBILE_URL?>/css/history.css?ver=20200507">


<!-- ===================================================================================BODY=====================================================================================-->

  <header class="header">
    <a href="javascript:history.back()"><img class="left" src="img/icon_back_bk.png" alt="back_arrow"></a>
    <p class="hd_title">내역보기</p>
  </header>



  <section class="balance" id="js-balance_1">


    <div class="coin_balance">
      <div class="cb_left">
        <div class="coin_img"><img src="<?=$token_img?>" alt="<?=$token_symbol?>"></div>
        <p class="coin_name"><?=$token_symbol?></p>
      </div>

      <div class="cb_right">
        <p class ="token_balance" ></p>
        <p class ="eth_balance"></p>
      </div>

      <p class="guide_1" >Touch!</p>
    </div>


    <!-- HISTORY -->
    <div class="token_table">
      <table class="hist_tab" id="js-hist_tab_1">
        <thead>
          <tr>
            <th>날짜</th>
            <th>내용</th>
            <th>상태</th>
            <th><?=$token_symbol?></th>
          </tr>
        </thead>

        <tbody></tbody>
      </table></div>
    </section>



    <section class="balance"  id="js-balance_2">
      <div class="coin_balance">
        <div class="cb_left">
          <div class="coin_img"><img src="<?=$point_img?>" alt="<?=$point_symbol?>"></div>
          <p class="coin_name"><?=$point_symbol?></p>
        </div>
        <div class="cb_right">
          <p id="point_balance" class="point_balance"> <? echo number_format($mask_row['mask_total'])." PACKAGE(S)"?></p>
        </div>
        <p class="guide_2" >Touch!</p>
      </div>

      <div>
        <table class="hist_tab" id="js-hist_tab_2">
          <thead>
            <tr>
              <th width="30%">날짜</th>
              <th width="20%">상품명</th>
              <th width="15%">상태</th>
              <th width="20%"><?=$token_symbol?></th>
              <th width="15%">수량</th>
            </tr>
          </thead>
          <tbody>
          <?php
              $arr = array("입금","입금확인","배송준비중","발송");
              while($history_row = sql_fetch_array($mask_history_result)){ 
                
                ?>
              
      <tr>
        <td width="30%" class="date"><?echo $history_row['datetime']?></td> <!-- 결제 시간 -->
        <td width="20%" class="goods_name"><?=$history_row['mask_type'] == "A" ? "유아마스크": "어린이마스크"?></td> <!-- 구매 상품명 -->
        <td width="15%" class="goods_name"><?=$arr[$history_row['tot_state']]?></td> <!-- 구매 상품명 -->
        <td width="20%"><?echo number_format($history_row['goods_order_total'])?> </td> <!-- 구매상품 갯수 -->
        <td width="15%" class="order_total"><?=$history_row['order_total']?></td>

        </tr>

             <?php } ?>
          </tbody>
        </table>
      </div>
    </section>




    

<!-- ===================================================================================BODY=====================================================================================-->

<script>

jQuery('#js-balance_1').click(function () {
  if($("#js-hist_tab_1").css("display") == "none"){
    jQuery('#js-hist_tab_1').css("display", "table");
    $(this).find('.guide_1').css('display','none');
  } else {
    jQuery('#js-hist_tab_1').css("display", "none");
    $(this).find('.guide_1').css('display','block');
  }
});



jQuery('#js-balance_2').click(function () {
  if($("#js-hist_tab_2").css("display") == "none"){
    jQuery('#js-hist_tab_2').css("display", "table");
    $(this).find('.guide_2').css('display','none');
  } else {
    jQuery('#js-hist_tab_2').css("display", "none");
    $(this).find('.guide_2').css('display','block');
  }
});

</script>

<?php
include_once(G5_MOBILE_PATH.'/tail.php');