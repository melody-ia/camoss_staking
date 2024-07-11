<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');


$title = '후원조직도';
?>

<style>
    /*popup*/
.popup_layer {position:fixed;top:0;left:0;z-index: 10000; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.4); }


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

        });
    </script>
    <div class='container nomargin nopadding'>

  <div class="popup_box">
      <!--팝업 컨텐츠 영역-->
      <div class="popup_cont">
         <!-- <?include_once(G5_THEME_PATH.'/chart.php');?> -->
         <iframe src="chart.php" width="100%" height="750px" style="border:hidden;"></iframe>
         
      </div>

      
  </div>

	</div>
    
</main>
</script>

<script>
	$(function(){
		$(".top_title h3").html("<span >후원조직도</span>");
	});
</script>
<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
