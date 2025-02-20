<?php
include_once('./_common.php');
include_once('../common.php');

$theme_path = G5_PATH.'/'.G5_THEME_DIR.'/'.$config['cf_theme'];
define('G5_THEME_PATH',        $theme_path);

include_once(G5_THEME_PATH.'/head.sub.php');
?>
<style type="text/css">
.id_search {padding:30px;}
.id_search li {float:left;width:32.3%;padding:0.5%;}
.id_search li:nth-child(3n+0) {width:32.4%;}
.id_search li span {display:block;padding:5px;line-height:18px;font-size:14px;border:solid 1px #ddd;cursor:pointer;}
.id_search li span:hover {background-color:#777;color:#fff;}
.infoBx {border:solid 2px rgba(39,48,62,0.4);border-radius:8px;margin-bottom:30px;}
.infoBx h3 {line-height:40px;font-size:15px;padding-left:20px;border-bottom:solid 1px rgba(0,0,0,0.1);background-color:rgba(39,48,62,0.05);}
.infoBx ul{    display: block;float: left;width: 100%;height: auto;margin-top: 10px;}
.infoBx strong{font-size:16px;font-family: Montserrat, Arial, sans-serif;line-height: 24px;}
.infoBx ul p{font-size:13px;line-height: 20px;}
.close_btn{margin-top:20px;height:40px; background:#333;color:white;}
.blanked{display:table;width:100%;text-align:center;}
.blanked > div{display:table-cell;vertical-align: middle;height:100px;}
</style>


<?
/*## 프레임 아이디 찾기 ################################################*/
if ($_GET['mbid'] && $member['mb_level'] > 8) {?>

<script>
$(function(){
	$('span[id^="id_"]').click(function () {
		var $id = $(this).attr("id").replace("id_","");
		$("#set_id_sel",parent.document.body).val($id);
		$("#insert_id",parent.document.body).show();
		$("#set_id_sel",parent.document.body).focus();
		$("#framer",parent.document.body).attr("src","");
		$("#framewrp",parent.document.body).hide();
	});
});
</script>

<div class="id_search">
<div class="infoBx">
	<h3>주문 아이디 검색</h3>
	<ul>
	<?
		$i = 0;
		$qry = sql_query(" select mb_id, mb_name from g5_member where mb_leave_date = '' and (mb_id like '%{$_GET[mbid]}%' or mb_name like '%{$_GET[mbid]}%') and mb_level > 2 order by mb_id ");
		while ($res = sql_fetch_array($qry)) {
			if ($res['mb_id']) {
	?>
		<li><span id="id_<?=$res['mb_id']?>"><strong><?=$res['mb_id']?> <p>(<?=$res['mb_name']?>)</p></span></li>
	<?
				$i++;
			}
		}
		if ($i == 0) {
	?>
		<li>No result found</li>
	<?
		}
	?>
	</ul>
	<p class="clr"></p>
</div>
</div>




<!-- 추천 아이디 찾기 ################################################ -->
<?} else if ($_GET['type'] == 'rcm_search') {?>
<div class="id_search">
<script>
$(function(){
	$('span[id^="id_"]').click(function () {
		var $id = $(this).attr("id").replace("id_","");
		$("#mb_recommend, #reg_mb_recommend",parent.document.body).val($id);
		$("#reg_mb_recommend",parent.document.body).focus();
		$("#framer",parent.document.body).attr("src","");
		$("#framewrp",parent.document.body).hide();
	});
});
</script>
	<div class="infoBx">
		<h3>추천인 검색 결과</h3>
		<ul>
		<?
			$recom_keyword = $_GET['rcm'];

			if($recom_keyword != ''){
				$sql = " select mb_id, mb_name, mb_email from g5_member where mb_leave_date = '' and mb_id != '{$_GET['mb_id']}' and (mb_id like '%{$recom_keyword}%' or mb_name like '%{$recom_keyword}%') AND mb_id != 'admin'  order by mb_id ";
			}else{
				$sql = " select mb_id, mb_name, mb_email from g5_member where mb_leave_date = '' and mb_id != '{$_GET['mb_id']}' AND mb_id != 'admin'  order by mb_id ";
			}
			
			$qry = sql_query($sql);
			$qry_num = sql_num_rows($qry);

			if($qry_num < 1){
				echo "<div class='blanked'><div>검색 결과가 없습니다.</div></div>";
			}else{
				while ($res = sql_fetch_array($qry)) {?>
				<li><span id="id_<?=$res['mb_id']?>"><strong><?=$res['mb_id']?></strong><p>(<?=$res['mb_name']?>)</p></span></li>
				<?}?>
			<?}?>
			
		</ul>
		<p class="clr"></p>
	</div>

		<script>
		function close_ajax(){
			$("#reg_mb_recommend",parent.document.body).focus();
			$("#framer",parent.document.body).attr("src","");
			$("#framewrp",parent.document.body).hide();
		}
		</script>
			<div  style="padding-top:30px">
				<input type="button" class='close_btn' onclick="close_ajax()" value=" close ">
			</div>
	</div>
</div><!-- // id_search -->


<!-- 후원인검색 ################################################ -->
<?} else if ($_GET['type'] == 'brcm_search') {?>
	<div class="id_search">
	
	<script>
	$(function(){
		$('span[id^="id_"]').click(function () {
			var $id = $(this).attr("id").replace("id_","");
			$("#mb_brecommend, #reg_mb_brecommend",parent.document.body).val($id);
			$("#reg_mb_brecommend",parent.document.body).focus();
			$("#framer",parent.document.body).attr("src","");
			$("#framewrp",parent.document.body).hide();
		});
	});
	</script>
	<div class="infoBx">
		<h3>후원인 검색 결과</h3>
		<ul>
		<?
			$brecom_keyword = $_GET['brcm'];

			if($brecom_keyword != ''){
				$sql = " select mb_id, mb_name, mb_email from g5_member where mb_leave_date = '' and mb_id != '{$_GET['mb_id']}' and (mb_id like '%{$_GET['brcm']}%' or mb_name like '%{$_GET['brcm']}%') AND mb_id != 'admin'  order by mb_id ";
			}else{
				$sql = " select mb_id, mb_name, mb_email from g5_member where mb_leave_date = '' and mb_id != '{$_GET['mb_id']}' AND mb_id != 'admin'  order by mb_id ";
			}
			
			$qry = sql_query($sql);
			$qry_num = sql_num_rows($qry);

			if($qry_num < 1){
				echo "<div class='blanked'><div>검색 결과가 없습니다.</div></div>";
			}else{
				while ($res = sql_fetch_array($qry)) {?>
				<li><span id="id_<?=$res['mb_id']?>"><strong><?=$res['mb_id']?></strong><p>(<?=$res['mb_name']?>)</p></span></li>
			<?}?>
			<?}?>
		</ul>
		<p class="clr"></p>
	</div>
	<script>
	function close_ajax(){
		$("#reg_mb_brecommend",parent.document.body).focus();
		$("#framer",parent.document.body).attr("src","");
		$("#framewrp",parent.document.body).hide();
	}
	</script>
			<div  style="padding-top:30px">
				<input type="button" class='close_btn' onclick="close_ajax()" value=" close ">
			</div>
	</div>


<!-- 센터검색 ################################################ -->
<?} else if ($_GET['type']=='center_search') {?>
<div class="id_search">

<script>
$(function(){
	$('span[id^="id_"]').click(function () {
		var $id = $(this).attr("id").replace("id_","");
		$("#mb_center",parent.document.body).val($id);
		$("#mb_center",parent.document.body).focus();
		$("#framer",parent.document.body).attr("src","");
		$("#framewrp",parent.document.body).hide();
	});
});
</script>
<div class="infoBx">
	<h3>센터 검색 결과</h3>
	<ul>
	<?
		
		$center_keyword = $_GET['center'];

		if($center_keyword != ''){
			$sql = " select * from g5_member where (mb_center_name like '%{$center_keyword}%' OR mb_id like '%{$center_keyword}%' ) and center_use = 1 AND mb_id != 'admin'  order by mb_id  ";
		}else{
			$sql = " select * from g5_member where mb_leave_date = '' and center_use = 1 AND mb_id != 'admin'  order by mb_id ";
		}
		
		$qry = sql_query($sql);
		$qry_num = sql_num_rows($qry);

		if($qry_num < 1){
			echo "<div class='blanked'><div>검색 결과가 없습니다.</div></div>";
		}else{
			while ($res = sql_fetch_array($qry)) {?>
			<li><span id="id_<?=$res['mb_id']?>"><strong><?=$res['mb_id']?> [ <?=$res['mb_center_name']?> ] </strong><p>(<?=$res['mb_name']?>)</p></span></li>
			<?}?>
		<?}?>
	</ul>
	<p class="clr"></p>
</div>
<script>
function close_ajax(){
	$("#reg_center",parent.document.body).focus();
	$("#framer",parent.document.body).attr("src","");
	$("#framewrp",parent.document.body).hide();
}
</script>
		<div align="center" style="padding-top:30px">
		<input type="button" class='close_btn' onclick="close_ajax()" value=" close ">
		</div>
</div>

<?}else if ($_POST['mb_id']) {
	$mb_info = sql_fetch(" select mb_name, mb_tel, mb_hp, mb_zip1, mb_zip2, mb_addr1, mb_addr2, mb_addr3, mb_email from g5_member where mb_leave_date = '' and mb_id = '{$_POST['mb_id']}' ");
?>
<?=$mb_info['mb_name']?>^<?=$mb_info['mb_tel']?>^<?=$mb_info['mb_hp']?>^<?=$mb_info['mb_zip1']?><?=$mb_info['mb_zip2']?>^<?=$mb_info['mb_addr1']?>^<?=$mb_info['mb_addr2']?>^<?=$mb_info['mb_addr3']?>^<?=$mb_info['mb_email']?>

<?} else if ($_POST['rcm_id']) {
	$rcm_id = trim($_POST['rcm_id']);
	$mb_info = sql_fetch(" select mb_id, mb_name from g5_member where mb_id = '{$rcm_id}' ");
	if ($mb_info) {
		echo "ok";
	} else {
		echo "break";
	}
}else{
	include_once(G5_THEME_PATH.'/head.sub.php');
?>

<script>
function close_ajax(){
	$("#reg_mb_recommend",parent.document.body).focus();
	$("#framer",parent.document.body).attr("src","");
	$("#framewrp",parent.document.body).hide();
}
</script>
		<div align="center" style="padding-top:30px">
		<input type="button"  class='close_btn' onclick="close_ajax()" value=" close ">
		</div>
<?}?>
