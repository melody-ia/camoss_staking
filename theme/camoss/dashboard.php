<?
	include_once('./_common.php');
    require_once(G5_THEME_PATH.'/_include/head.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_PATH.'/util/package.php');
	include_once(G5_LIB_PATH.'/fcm_push/set_fcm_token.php');

	login_check($member['mb_id']);
    $member_info = sql_fetch("SELECT * FROM g5_member_info WHERE mb_id ='{$member['mb_id']}' order by date desc limit 0,1 ");
    
    $password_sql = "SELECT mb_password,reg_tr_password FROM g5_member WHERE mb_id = '{$member['mb_id']}' " ;
    $password_result = sql_fetch($password_sql);
    if($password_result['mb_password'] == 'sha256:12000:38cIHMUY7+MqI/FSl4zzu8fyyYV5v4kp:8UG8WFY+3smLHy4Xi0/D0SsLSSePV7P7' || $password_result['reg_tr_password'] == 'sha256:12000:YnQ4FiYau5CBORv+nqGbayBRfhxllDFL:LLC4COBu4RGCIPcOES4j5yVrPmKZR1TQ'){
        $newpassword = 1;
    }else{
        $newpassword = 0;
    }
    

?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/default.css">
<script src="<?=G5_URL?>/js/common.js"></script>


<?php
    if(defined('_INDEX_')) { // index에서만 실행
        include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
    }
    $package = package_have_return($member['mb_id']);

?>


<?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>
<main>
    <div class='container dashboard'>
        <!--  <div class="my_btn_wrap">
            <div class='row'>
               <div class='col-lg-6 col-12'>
                    <button type='button' class='btn wd main_btn b_sub' onclick="go_to_url('mywallet');"> 입출금</button>
                </div> 
                <div class='col-lg-6 col-12'>
                    <button type='button' class='btn wd main_btn b_main' onclick="go_to_url('upstairs');">패키지구매</button>
                </div>
                <div class='col-lg-12 col-12'>
                    <button type='button' class='btn wd main_btn b_third' onclick="move_to_shop()" >쇼핑몰바로가기</button>
                </div>
            </div>
        </div>
         -->


        <div class='r_card_wrap content-box round mt30'>
            <div class="card_title">구매 가능 금액</div>
            <div class="box-wrap">
                <div class='box'>
                    <p class='main_centent'><?=shift_auto($available_fund,$curencys[0])?><span class='currency'> <span
                                class='currency'><?=strtoupper($curencys[0])?></span></p>
                </div>
                <button type='button' class='btn wd main_btn b_sub' onclick="go_to_url('mywallet');"> 입금</button>
            </div>
        </div>

        <div class='r_card_wrap content-box round mt30'>
            <div class="card_title">출금 가능 금액</div>
            <div class="box-wrap">
                <div class='box'>
                    <p class='main_centent'><?=shift_auto($total_withraw,$curencys[0])?><span class='currency'> <span
                                class='currency'><?=strtoupper($curencys[0])?></span></p>
                </div>
                <!-- <button type='button' class='btn wd main_btn b_third' onclick="go_to_url('reupstairs');"> 재구매</button> -->
                <button type='button' class='btn wd main_btn b_third' onclick="go_to_url('mywallet&view=withdraw');"> 출금</button>
            </div>
        </div>

        <div class='r_card_wrap content-box round mt30'>
            <?$ordered_items = ordered_items($member['mb_id']);?>
            <div class="card_title mb20">보유 패키지 (<?=count($ordered_items)?>)
                <a href='<?=G5_URL?>/page.php?id=upstairs' class='f_right inline more'><span>더보기<i
                            class="ri-add-circle-fill"></i></span></a>
            </div>

            <p>구매등급 :</p>
            <p class='main_centent mb20'><?=shift_auto($member['pv'], $curencys[0])?> <span class='currency'>
                    <?=strtoupper($curencys[0])?></span></p>

            <?
            if(count($ordered_items) < 1) { 
                    echo "<div class='no_data'>내 보유 상품이 존재하지 않습니다</div>";
            }else{
                
                for($i = 0; $i < count($ordered_items); $i++){
                    $row = $ordered_items[$i];
                    $pack_limit_per = round(($row['pay_ing'] / $row['pay_limit'] * 250),2);
                    ?>
            <div class="col-12 r_card_box">
                <a href='/page.php?id=upstairs_detail&pay_id=<?=$row['pay_id']?>'>

                    <div class="r_card r_card_<?=substr($row['od_name'],1,1)?>">
                        <p class="title">

                            <span class='p_name' ><?=$ordered_items[$i]['it_name']?> </span>
                            
                            
                            <!-- <span style='font-size:14px;'><?=$ordered_items[$i]['it_option_subject']?></span> -->
                            <!-- <span style='font-size:20px;'>NFT</span> -->
                            <!-- <img style="width:40px;height:40px;"src="<?=G5_THEME_URL?>/img/card_logo3.png"/>  -->
                            <span class='f_right more_arrow'> 
                            <i class="ri-arrow-right-double-fill" style="font-size:30px;"></i>
                                <!-- <img src="<?=G5_THEME_URL?>/img/arrow.png" alt=""> -->
                            </span>
                            <span class='pay_id'><?=strtoupper($ordered_items[$i]['pay_id'])?></span>
                        </p>
                        <div class="b_blue_bottom"></div>
                        <div class="text_wrap">
                            <p class="value_rate"><i class="ri-discount-percent-line pack_bull"></i> 수익률 : <?=$row['pv']?>%   <i class="ri-pie-chart-2-fill pack_bull"></i> 누적지급률 : <?=$pack_limit_per?>%</p> 
                            <p class="value_date" style='text-align:right'><?=$row['od_date']?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?}
            }
			?>
            <div style='text-align:center'>-</div>
            <button type='button' class='btn wd main_btn b_main' onclick="go_to_url('upstairs');">패키지구매</button>
        </div>

        <!-- <div style="clear:both;"></div> -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <div class='r_card_wrap content-box round history_latest mb30 mt30'>
            <div class="card_title_wrap">
                <div class="card_title">패키지별 보너스한계 (<?=$limited?>%)</div>
              
            </div>
            <?
                $my_package_sql = "SELECT * from g5_order WHERE mb_id = '{$member['mb_id']}' AND pay_end = 0";
                $my_package_result = sql_query($my_package_sql);
                $my_package_cnt = sql_num_rows($my_package_result);

                if($my_package_cnt > 0){
                    $chart_data = [];
                    $chart_label = [];
                    $chart_color = [];
                    $chart_color_array = ['#ddd','#b55dcc','#516feb','#09c3fd','#5ed2dc','#373cbc','#2b3a6d','#6214ab','#5edc79','#dcc55e'];

                    while($row = sql_fetch_array($my_package_result)){
                        $pack_full_name = strtoupper($row['od_name'].'_'.$row['pay_id']);

                        if($row['pay_ing'] < 0.1){
                            $pack_per_bonus = 0;
                        }else{
                            $pack_per_bonus = Round($row['pay_ing']/($row['pay_limit']/100),2);
                        }

                        if($pack_per_bonus > 95){
                            $pack_per_color = '#dc3545';
                        }else{
                            $pack_per_color = $chart_color_array[substr($row['od_name'],1,1)];
                        }


                        array_push($chart_color,$pack_per_color);
                        array_push($chart_data,$pack_per_bonus);
                        array_push($chart_label,$pack_full_name);
                    }
                }
            ?>

            <div id="myChart2"></div>

            <p class="dashline mb20"></p>

            <div class="card_title_wrap" style="clear:both">
            <div class="card_title">총누적보너스(+탄소포인트) : </div>
            <a href='<?=G5_URL?>/page.php?id=bonus_history' class='f_right inline more'><span>더보기<i class="ri-add-circle-fill"></i></span></a>
            </div>
            <p class='main_centent'><?=shift_auto($total_fund,'usdt')?><span class='currency'>
                    <?=strtoupper($curencys[0])?></span></p>
            <?
            $bonus_history_sql	 = "SELECT allowance_name,SUM(benefit)AS total_bonus from `{$g5['bonus']}` WHERE mb_id = '{$member['mb_id']}' GROUP BY allowance_name ";
            $bonus_history_result = sql_query($bonus_history_sql);
            $bonus_history_cnt = sql_num_rows($bonus_history_result);
            if($bonus_history_cnt > 0){
                while($row = sql_fetch_array($bonus_history_result)){
            ?>

            <div class="line row">
                <div class='col-7'>
                    <!-- <span class='day'><?=timeshift($row['day'])?> </span> -->
                    <i class="ri-arrow-right-s-fill" style='font-size:14px;vertical-align: top;'></i><span class='category'><strong>누적 <?=bonus_pick_category($row['allowance_name'],'name')?> 보너스</strong></span>
                </div>
                <div class='col-5 text-right'>
                    <span class='price'><?=shift_auto($row['total_bonus'],$curencys[0])?> <?=strtoupper($curencys[0])?>
                    </span>
                </div>
            </div>

            <?}?>
            <?}else{
                echo "<div class='no_data'>보너스 내역이 존재하지 않습니다</div>";
            }?>
            <button type='button' class='btn wd main_btn b_third' onclick="go_to_url('bonus_history');"> 상세내역보기</button>
        </div>

        <script>
        // var chart_data = JSON.parse('<?=($member_info['hash_info'])?>');
        // var chart_data = <?=$bonus_per?>;

        var chart_data = <?=json_encode($chart_data)?>;
        var chart_label = <?=json_encode($chart_label)?>;
        var chart_color = <?=json_encode($chart_color)?>;

        $(function() {

            $(window).scroll(function() {
                var wrapper_height = $(window).scrollTop();
                if (wrapper_height > 500 &&  wrapper_height < 600 && chart_data.length > 0) {
                    chart("#myChart2");
                    chart = function() {};
                } else {

                }
            });


            function chart(where) {
                var chart = new ApexCharts(document.querySelector(where), options);
                chart.render();

            }

        });
        </script>
        <script src="<?=G5_THEME_URL?>/_common/js/chart/apexchart_multi.js"></script>



        <div class='r_card_wrap content-box round mt30 content_border '>
            <div class="card_title">탄소포인트 (C.P)</div>
            <div class="box-wrap">
                <div class='box'>
                    <p class='main_centent'><?=shift_auto($shop_balance,$curencys[0])?> <span class='currency'>
                            CP</span></p>
                </div>
            </div>
        </div>



        <!-- <div class='r_card_wrap content-box round regist_latest'>
            <div class="card_title_wrap">
                <div class="card_title">추천내역 </div>
                <a href='<?=G5_URL?>/page.php?id=structure' class='inline more'><span>더보기<i
                            class="ri-add-circle-fill"></i></span>
                </a>
            </div>

            <p>직추천 정회원 :</p>
            <p class='main_centent mb20 mt10'><?=$direct_reffer?> 명</p>
            <P class='dashline mb20'></P>
            <p>최근 추천 회원 :</p>

            <?
					$recommend_sql	 = "SELECT * from `{$g5['member_table']}` WHERE mb_recommend = '{$member['mb_id']}' order by mb_open_date desc limit 0,2";
					$recommend_result = sql_query($recommend_sql);
					$recommend_cnt = sql_num_rows($recommend_result);
					if($recommend_cnt > 0){
						while($row = sql_fetch_array($recommend_result)){
					?>

            <div class="line row">
                <div class='col-9'>
                    <span class='badge'><?=$member_level_array[$row['mb_level']]?> </span>
                    <span class='badge color<?=user_grade($member['mb_id'])?>'><?=$row['grade'].' CP'?> </span>
                    <span class='id'><?=$row['mb_id']?> </span>

                </div>
                <div class='col-3 text-right'>
                    <span class='day'><?=timeshift($row['mb_open_date'])?> </span>
                </div>
            </div>

            <?}?>
            <?}else{
						echo "<div class='no_data'>추천 등록 회원이 존재하지 않습니다</div>";
					}?>

            <button type='button' class='btn wd main_btn b_third' onclick="go_to_url('structure');"> 추천조직도</button>
        </div> -->

        <div class='r_card_wrap content-box round regist_latest'>
            <div class="card_title_wrap">
                <div class="card_title">추천내역 </div>
                <a href='<?=G5_URL?>/page.php?id=binary' class='inline more'><span>더보기<i
                            class="ri-add-circle-fill"></i></span>
                </a>
            </div>

            <p>추천산하 :</p>
            <p class='main_centent mb20 mt10'><?=$mb_b_child?> 명</p>
            <P class='dashline mb20'></P>
            <p>최근 추천 등록 회원 :</p>

            <?
                    $brecommend_binary_sql	 = "SELECT * from `{$g5['member_table']}` WHERE mb_brecommend = '{$member['mb_id']}' order by mb_open_date desc limit 0,2";
                    $brecommend_binary_result = sql_query($brecommend_binary_sql);
                    $brecommend_binary_cnt = sql_num_rows($brecommend_binary_result);
                    if($brecommend_binary_cnt > 0){
                        while($row = sql_fetch_array($brecommend_binary_result)){
                    ?>

            <div class="line row">
                <div class='col-9'>
                    <span class='badge'><?=$member_level_array[$row['mb_level']]?> </span>
                    <span class='badge color<?=user_grade($member['mb_id'])?>'><?=$row['grade'].' CP'?> </span>
                    <span class='id'><?=$row['mb_id']?> </span>

                </div>
                <div class='col-3 text-right'>
                    <span class='day'><?=timeshift($row['mb_open_date'])?> </span>
                </div>
            </div>

            <?}?>
            <?}else{
						echo "<div class='no_data'>추천 등록 회원이 존재하지 않습니다</div>";
					}?>

            <button type='button' class='btn wd main_btn b_third' onclick="go_to_url('binary');"> 조직도</button>
        </div>

        <div class='r_card_wrap content-box round mt30'>
            <div class="card_title mb20">다음 승급</div>
            <div class='row'>

                <div class='col-6 text-center'>
                    <p style='font-size:0.9rem'>구매등급 : </p>
                    <p class='main_centent mb20 mt10'><?=check_value($member['mb_5'])?></p>
                </div>

                <div class='col-6 text-center l_div'>
                    <p style='font-size:0.9rem'>승급기준 :</p>
                    <p class='main_centent mb20 mt10'><?=check_value($member['mb_7'])?></p>
                </div>
            </div>

            <!-- <P class='dashline mb20'></P>
            <?
                $rank_sql = "SELECT * FROM rank WHERE mb_id = '{$member['mb_id']}' ";
                $rank_sql_result = sql_query($rank_sql);
                $rank_history_cnt = sql_num_rows($rank_sql_result);
					if($bonus_history_cnt > 0){
						while($row = sql_fetch_array($rank_sql_result)){
			?>


            <?}?>
            <?}else{
                echo "<div class='no_data'>승급기록이 없습니다.</div>";
            }?> -->
        </div>

        <div class='r_card_wrap content-box round mt30'>
            <?
            
                if(!$member['mb_8'] || $member['mb_8'] == ''){
                    $total_brecom_sale = 0;   
                }else{
                    $total_brecom_sale = floatval(conv_number($member['mb_8']));   
                }

                if(!$member['mb_9'] || $member['mb_9'] == ''){
                    $small_brecom_sale = 0;   
                }else{
                    $small_brecom_sale = floatval(conv_number($member['mb_9']));
                }
                $big_brecom_sale = $total_brecom_sale - $small_brecom_sale;

            ?>
            <div class="card_title mb20">산하 실적</div>
            <p class='main_centent mb20 mt10'><?=shift_auto($total_brecom_sale,2)?></p>
            <div class='row'>

                <div class='col-6 text-center'>
                    <p style='font-size:0.9rem'>대실적 </p>
                    <p class='main_centent mb20 mt10'><?=shift_auto($big_brecom_sale,2)?></p>
                </div>

                <div class='col-6 text-center l_div'>
                    <p style='font-size:0.9rem'>소실적합 </p>
                    <p class='main_centent mb20 mt10'><?=shift_auto($small_brecom_sale,2)?></p>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
$(function() {
    var new_pass_reg = <?=$newpassword?>;
    if (new_pass_reg == 1) {
        dialogModal('<p>패스워드를 변경</p>', '<p>초기 로그인패스워드와 핀코드를 반드시 변경해주세요</p>', 'warning');
        $('.closed').click(function() {
            location.href = '<?=G5_URL?>/page.php?id=profile';
        });
        console.log('password : ' + new_pass_reg);
    }

});
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>