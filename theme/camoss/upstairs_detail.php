<?
    include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
    $menubar =1;
    include_once(G5_THEME_PATH.'/_include/gnb.php');
    
    $title = 'upstairs_history';

    $val = $_REQUEST;
    // $od_id = $val['od_id'];
    $od_id = '';

    if($od_id == ''){
        $sql = "SELECT SUM(upstair) as sum_pv from g5_order WHERE mb_id = '{$member['mb_id']}' ";
    }else{
        $sql = "SELECT * from g5_order WHERE od_id = '{$od_id}' order by od_id desc limit 0,1";
    }

    $goods_sql = "select it_price, it_supply_point from g5_item where it_maker <> 'P0' order by it_price asc";
    $goods_row = sql_query($goods_sql);
    $goods_array = [];
    for($i = 0; $i < $row = sql_fetch_array($goods_row); $i++){
        array_push($goods_array,$row);
    }
    

    $this_od = sql_fetch($sql);
    
    $sum_pv = $this_od['sum_pv'];

    /*
    $item_no = $this_od['od_app_no'];
    $staking_name = "스테이킹";
    $end_years = $this_od['od_invoice_time'];
    $handled_my_staking = shift_auto($this_od['od_cart_price'],ASSETS_CURENCY);
    $expiry_item = $this_od['od_refund_price'] > 0 ? "finish_staking" : "proceeding";

    $pay_acc = $this_od['pay_acc'];
    $od_settle_case = $this_od['od_settle_case'];
    $pay_count = $this_od['pay_count'];
    
    if($this_od['od_settle_case'] == "ETH"){
        $pay_acc = $this_od['pay_acc_eth'];
        $od_settle_case = WITHDRAW_CURENCY;
    } */

    function item_rank($val){
        if($val >= 50000){
            $result = 7;
        }else if($val >= 10000){
            $result = 6;
        }else if($val >= 7000){
            $result = 5;
        }else if($val >= 5000){
            $result = 4;
        }else if($val >= 3000){
            $result = 3;
        }else if($val >= 1000){
            $result = 2;
        }else if($val >= 500){
            $result = 1;
        }else{
            $result = 0;
        }

        return $result;
    } 

    function od_txt($val,$diff){
        

        if($val != $diff){
            $result = $val." ~ ".$val+$diff;
        }else{
            $result = $val;
        }
        return $result;
    }

    function getRandStr($length = 6) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    $item_rank = item_rank($sum_pv);
    $fund_rate = $goods_array[$item_rank-1]['it_supply_point'];
    $item_layer = $sum_pv / 500;
    $digital_aseet = "moss_Inherent_asset_".$item_rank.".jpg";

    /* $rand_num = getRandStr(4);
    $rand_digit = sprintf('%06d',rand(000000,999999));
    $digital_code = $rand_num.$rand_digit; */

    // echo $digital_code;
?>


<style>
    .notice_wrap {width: calc( 100% - 30px );}
</style>

<link href="<?= G5_THEME_URL ?>/css/scss/main.css" rel="stylesheet">
<link href="<?= G5_THEME_URL ?>/css/scss/nft.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Damion&family=Encode+Sans:wght@300;500;700&display=swap" rel="stylesheet">

<main>
    <div class='container pt20'>
            <H3 class="title">나의 자산</H3>

            <div class='nft_content'>
                <img style="overflow:visible;" width="100%" src="<?=G5_THEME_URL?>/img/nft/<?=$digital_aseet?>" >
                </svg>
                <div class="content_foot">
                    <div class='nft-content img'><i class="ri-nft-line icon" ></i> </div>
                    <div class='nft-content article'><ul>
                        <li><?=CONFIG_TITLE?></li>
                        <li class="title"><?=CONFIG_SUB_TITLE?></li>
                        <li>Inherent Stake & Right</li>
                        <!-- <li class="sn_no">SN. <span class="red"></span></li> -->
                    </ul>
                    </div>
                </div>
            </div>

            <H3 class="title mt10">권리 증명서</H3>
            <div class='nft_container'>
                <!-- <p class="esg_logo"><img src="<?=G5_THEME_URL?>/img/logo.svg"/></p> -->
                <img src='<?=G5_THEME_URL?>/img/default.png' width="100%">

                <p class="title">Smart Contract</p>

                <p class="sub_title">본 NFT 는 <?=CONFIG_SUB_TITLE?>이 운영하는 <?=CONFIG_TITLE?> 사업의 지분 권리 및 배당수익 권리를 증명함.</p>
                
                <p class="mt20">
                    <ul><i class="ri-check-line"></i> 권리의 내용
                        <li>- 보유수량 > <span class="red"> <?=$item_layer?> MOSS</span></li>
                        <!-- <li>- 일련번호 > <span class="red"><?=od_txt($this_od['od_cash_no'],$this_od['od_cash_info'])?>  </span></li> -->
                        <li>- 투자 수익률 권리 > <span class="red"><?=$fund_rate?>%</span></li>
                    </ul>

                    <ul>
                        <i class="ri-check-line"></i> 소유자 > <?=$member['mb_id']?>
                    </ul>
                </p>

                <p class="signature"><?=CONFIG_SUB_TITLE?></p>

                <!-- <p class="company_sign"><img src="<?=G5_THEME_URL?>/img/certified_sign.png"/></p> -->
                <p class="company_sign"><img src="<?=G5_THEME_URL?>/img/title.png"/></p>
            </div>
            
            
            <H3 class="title mt10">MY MOSS NFT</H3>
            <div class='staking_card_fill_wrap staking'>
                <a href="page.php?id=bonus_history">
                <div class="card_fill fill_card2"></div>    
                <div class="staking_card my_staking border_card<?=$item_no?>" data-id="<?=$od_id?>">
                    <div class="staking_left_wrap">
                        <!-- //<p class="item_character text_color<?=$item_no?>">
                        //    <?=$this_od['od_tax_mny']?><span class='percent_marker'>%</span> 
                            
                        //    <?=$this_od['pay_end']/12?><span class='year_marker'>년</span>
                        //</p>
                        
                        //<p class="date"><?=$this_od['od_date']?>~<?=$end_years?></p>
                         -->
                        <p class="item_character text_color2"> <?=shift_auto($sum_pv,$curreny[0])?> USDT </p>
                    </div>

                    <P class='divide dark'></P>
                    <div class="staking_right_wrap">
                        <p class="value text_color<?=$html_index?>"><?=shift_auto($item_layer,ASSETS_CURENCY)?> </p>
                        <p class="currency">moss</p>
                    </div>
                </div>
                </a>

                <!--
               <div class="info_wrap">
                    <? echo $expiry_item === 'finish_staking'? '<p class="finish_staking_text">참여 완료된 스테이킹입니다.</p>' : '' ?>
                    <div class="">
                        <span>스테이킹 상품번호</span>
                        <span><?=$this_od['od_id']?></span>
                    </div>
                    <div class="quantity_wrap">
                        <span><?=$staking_name?> 수량</span>
                        <span class="values">
                            <?=shift_auto($this_od['od_invoice'],ASSETS_CURENCY)?> NFT
                        </span>
                    </div>
                    <div class="date_wrap">
                        <span><?=$staking_name?> 기간</span>
                        <span><?=$this_od['od_date']?> ~ <?=$this_od['od_invoice_time']?></span>
                    </div>
                    <div class="num_wrap">
                        <span><?=$staking_name?> 수익률</span>
                        <span><?=$this_od['od_tax_mny']?>%</span>
                    </div>
                    <div class="sum_price_wrap">
                        <span>총 스테이킹 수익금</span>
                        <span>+ <?=shift_auto($pay_acc,$od_settle_case)?> <?=$od_settle_case?></span>
                    </div>
                    <div class="sum_price_wrap">
                        <span>총 지급 회차</span>
                        <span><?=$pay_count?> / <?=$this_od['pay_end']?></span>
                    </div>
                </div>
            </div>
        -->

        <!-- <div class="col-sm-12 col-12 content-box round history_detail mb20">
            <div class="box-header">
                보너스(수익금) 지급내역
            </div>

            <div class="box-body">
                <?
                $staking_history_sql = "select * from {$g5['mining']} where mb_id = '{$member['mb_id']}' and shop_order_id = {$this_od['od_id']}";
                $staking_history_result = sql_query($staking_history_sql);
                while($staking_history_row = sql_fetch_array($staking_history_result)){
                ?>
                    
                    <div class="history_box">
                        <div class='hist_con deposit_history'>
                            
                            <div class="hist_con_row1 deposit">
                                <div class="hist_left">
                                    <img src="<?=G5_THEME_URL?>/img/deposit.svg" alt="">
                                </div>
                                <div class="hist_mid">
                                    <p><?if($staking_history_row['allowance_name'] != 'NFT REWARD'){echo "Staking Bonus";}else{echo "NFT Earnings Dividend";}?></p>
                                    <p class="hist_date"><?=$staking_history_row['day']?></p>
                                </div>
                                <div class="hist_right">
                                    <p class='hist_value'>+ <?=shift_auto($staking_history_row['mining'],$staking_history_row['currency'])?>  <span class="currency"><?= $staking_history_row['currency'] ?></span></p>
                                    <p class="hist_won"><?=$staking_history_row['currency'] == "ETH" ? number_format(floor($staking_history_row['mining'] * $coin['eth_krw'])) : number_format(floor($staking_history_row['mining'] * $coin['esgc_krw']))?> <?=BALANCE_CURENCY?></p>    
                                </div>
                            </div>                           
                        </div> 
                    </div>
                <?}?>
            </div>
        </div> -->
</main>


<div class="gnb_dim"></div>
</section>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>