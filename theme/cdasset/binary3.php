<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');


$bo_table = "g5_write_notice";
$bo_table_java = "notice";

$list_cnt = sql_fetch("select count(*) as cnt from {$bo_table} where wr_id=wr_parent order by wr_datetime desc");
$cnt = $list_cnt['cnt'];

$sql = "select * from {$bo_table} where wr_id=wr_parent order by wr_datetime desc";
$list = sql_query($sql);

$title = '후원조직도';
?>

<style>
    /*popup*/
.popup_layer {position:fixed;top:0;left:0;z-index: 10000; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.4); }
/*팝업 박스*/
.popup_box{position: relative;top:50%;left:50%; overflow: auto; height: 600px; width:375px;transform:translate(-50%, -50%);z-index:1002;box-sizing:border-box;background:#fff;box-shadow: 2px 5px 10px 0px rgba(0,0,0,0.35);-webkit-box-shadow: 2px 5px 10px 0px rgba(0,0,0,0.35);-moz-box-shadow: 2px 5px 10px 0px rgba(0,0,0,0.35);}
/*컨텐츠 영역*/
.popup_box .popup_cont {padding:50px;line-height:1.4rem;font-size:14px; }
.popup_box .popup_cont h2 {padding:15px 0;color:#333;margin:0;}
.popup_box .popup_cont p{ border-top: 1px solid #666;padding-top: 30px;}
/*버튼영역*/
.popup_box .popup_btn {display:table;table-layout: fixed;width:100%;height:70px;background:#ECECEC;word-break: break-word;}
.popup_box .popup_btn a {position: relative; display: table-cell; height:70px;  font-size:17px;text-align:center;vertical-align:middle;text-decoration:none; background:#ECECEC;}
.popup_box .popup_btn a:before{content:'';display:block;position:absolute;top:26px;right:29px;width:1px;height:21px;background:#fff;-moz-transform: rotate(-45deg); -webkit-transform: rotate(-45deg); -ms-transform: rotate(-45deg); -o-transform: rotate(-45deg); transform: rotate(-45deg);}
.popup_box .popup_btn a:after{content:'';display:block;position:absolute;top:26px;right:29px;width:1px;height:21px;background:#fff;-moz-transform: rotate(45deg); -webkit-transform: rotate(45deg); -ms-transform: rotate(45deg); -o-transform: rotate(45deg); transform: rotate(45deg);}
.popup_box .popup_btn a.close_day {background:#5d5d5d;}
.popup_box .popup_btn a.close_day:before, .popup_box .popup_btn a.close_day:after{display:none;}
/*오버레이 뒷배경*/
.popup_overlay{position:fixed;top:0px;right:0;left:0;bottom:0;z-index:1001;;background:rgba(0,0,0,0.5);}
/*popup*/
</style>

<main>
    <script>
        $(document).ready(function() {
            var this_url = location.href;
            var str_arr = this_url.split('&');
            var result_url = this_url.replace(str_arr[0]+"&"," ");
            openPop();

           //팝업 띄우기
            function openPop() {
                document.getElementById("popup_layer").style.display = "block";

            }

            //팝업 닫기
            function closePop() {
                document.getElementById("popup_layer").style.display = "none";
            }
            
        });
    </script>
    <div class='container nomargin nopadding'>

<div class="popup_layer" id="popup_layer" style="display: none;">
  <div class="popup_box">
      <div style="height: 10px; width: 375px; float: top;">
        <a href="javascript:closePop();"><img src="/static/img/ic_close.svg" class="m_header-banner-close" width="30px" height="30px"></a>
      </div>
      <!--팝업 컨텐츠 영역-->
      <div class="popup_cont">
         <?include_once(G5_THEME_PATH.'/chart.php');?>
      </div>

      
  </div>
</div>
	</div>
    <div class="gnb_dim"></div>
</main>
</script>

<script>
	$(function(){
		$(".top_title h3").html("<span >후원조직도</span>");
	});
</script>
<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
