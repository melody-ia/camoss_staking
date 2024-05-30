<?php
$sub_menu = "650200";

include_once('./_common.php');
include_once('adm/inc.member.class.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');

login_check($member['mb_id']);

$gubun = "B";


if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}

$start_id = $config['cf_admin'];
$tree_id = $config['cf_admin'];
$go_id = $member['mb_id'];


if ($_GET['go']=="Y"){
	goto_url("page.php?id=binary&gubun=".$gubun."#org_start");
	exit;
}

$token = get_token();
$mb_org_num = 80;


$qstr.='&fr_date='.$fr_date.'&to_date='.$to_date.'&starter='.$starter.'&partner='.$partner.'&team='.$team.'&bonbu='.$bonbu.'&chongpan='.$chongpan;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';


$g5['title'] = '조직도(박스)';

?>


<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">

<!-- 
<div style="padding:0px 0px 0px 10px;">
	<a name="org_start"></a>
	<div style="float:left">
	<input type="button" class="btn_menu" value="검색메뉴닫기" onclick="btn_menu()">
	</div>
	<div style="float:right;padding-right:10px">

	<button type='button' id='zoomOut' class='zoom2-btn'>Zoom Out</button>
	<button type='button' id='zoomIn' class='zoom-btn'>Zoom In</button>
	</div>
</div>
<div style="padding-top:10px;clear:both"></div>

<div id="div_left" style="width:15%;float:left;min-height:710px;">
	<div style="margin-left:10px;padding:5px 5px 5px 5px;border:1px solid #d9d9d9;height:100%">
	<?
	if (!$fr_date) $fr_date = Date("Y-m-d", time()-60*60*24*365);
	if (!$to_date) $to_date = Date("Y-m-d", time());
	?>


		<form name="sForm2" id="sForm2" method="get" action="./page.php">
        <input type="hidden" name="id" value="binary">
		<input type="hidden" name="now_id" id="now_id" value="<?=$now_id?>">
		<table width="100%">
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px">
				<div style="float:left">
				<b>표시인원</b>
				</div>
				<div style="float:right">
				<input type="text" id="mb_org_num"  name="mb_org_num" value="<?php echo $member['mb_org_num']; ?>" class="frm_input" style="text-align:center" size="3" maxlength="3"> 단계 &nbsp;
				</div>
				</td>
			</tr>
			<style>
				.search_btn{border:1px solid #aaa;padding:5px 10px;border-radius:0;}
				.search_btn.active{background:green;border:1px solid green;color:white}
			</style>
			<tr>
				<td bgcolor="#f2f5f9" height="20" style="padding:20px 0;" align=center>
				<input type="radio" id="gubun2" name="gubun" style="display:none" onclick="document.sForm2.submit();" value="B"<?if ($gubun=="B") echo " checked"?>>
				<label for="gubun2" class='btn search_btn <?if($_GET['gubun']=='B')echo 'active';?>'>후원조직</label>
				</td>
			</tr>
			
		</table> 
		</form>

		<div id="div_member"></div>

		<form name="sForm" id="sForm" method="post" style="padding-top:10px" onsubmit="return false;">
		<input type="hidden" name="gubun" value="<?=$gubun?>">
		<table width="100%">
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>회원검색</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px">
				
				<select name="sfl" id="sfl">
				    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
					<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option>
					</select>
				<div style="padding-top:5px">
				<label for="stx" class="sound_only" >검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="stx" value="<?php echo $stx ?>" id="stx"  class="required frm_input" style="padding:0 5px;" onkeypress="event.keyCode==13?btn_search():''">
				</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="button" onclick="btn_search();" class="btn_submit" value="검 색">
				</td>
			</tr>
		</table>
		</form>

		<div id="div_result" style="margin-top:5px;overflow-y: auto;height:418px">

		</div>
	</div>
</div> -->
<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css"
    rel="stylesheet" />
<link rel="stylesheet" href="/adm/css/font-awesome.min.css">
<link rel="stylesheet" href="/adm/css/jquery.orgchart.css">
<link href="/adm/css/scss/member_org.css" rel="stylesheet">
<script type="text/javascript" src="/adm/jquery.orgchart2.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>

<script>
$.datepicker.regional["ko"] = {
    closeText: "close",
    prevText: "이전달",
    nextText: "다음달",
    currentText: "오늘",
    monthNames: ["1월(JAN)", "2월(FEB)", "3월(MAR)", "4월(APR)", "5월(MAY)", "6월(JUN)", "7월(JUL)", "8월(AUG)", "9월(SEP)",
        "10월(OCT)", "11월(NOV)", "12월(DEC)"
    ],
    monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
    dayNames: ["일", "월", "화", "수", "목", "금", "토"],
    dayNamesShort: ["일", "월", "화", "수", "목", "금", "토"],
    dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
    weekHeader: "Wk",
    dateFormat: "yymmdd",
    firstDay: 0,
    isRTL: false,
    showMonthAfterYear: true,
    yearSuffix: ""
};
$.datepicker.setDefaults($.datepicker.regional["ko"]);
</script>


<style type="text/css">
	#chart-container {
		width: inherit;
		overflow: auto !important;
		border:0;
	}

	.orgchart .node {
		box-sizing: border-box;
		display: inline-block;
		position: relative;
		margin: 0;
		padding: 3px;
		/* height:143px; */
		text-align: center;
		width: 150px;
	}

	.orgchart .node .title {
		background: #fff;
		border: 2px solid rgb(95, 95, 95);
		color: #000;
		height: 185px;
		font-weight: normal;
		line-height: 15px;
		padding-top: 5px;
		cursor: pointer;
	}

	.orgchart .node .title .symbol {
		display: none;
	}

	.orgchart .node .title .dec {
		font-size: 11px;
		line-height: 15px;
	}

	.orgchart .node .title .dec.p {
		margin-top: 5px;
	}

	.orgchart .node .title .mb {
		margin: 3px 0;
		word-break: break-all;
		white-space: normal;
		font-weight: bold;
		font-size: 16px;
		padding: 0;
		color: green;
		font-weight: 600;
		line-height: 30px;
		font-family: Arial, Helvetica, sans-serif;
	}

	.orgchart .node .mb .user_name {
		color: #555;
		font-size: 12px;
		display: block;
		line-height: 20px;
	}

	.zoom-btn {
		display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		touch-action: manipulation;
		cursor: pointer;
		user-select: none;
		color: #fff;
		background-color: #364fa0;
		border: 1px solid transparent;
		border-color: #364fa0;
		border-radius: 4px;
	}

	.zoom2-btn {
		display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		touch-action: manipulation;
		cursor: pointer;
		user-select: none;
		color: #fff;
		background-color: #364fa0;
		border: 1px solid transparent;
		border-color: #364fa0;
		border-radius: 4px;
	}

	.my-class {
		display: inline-block;

		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		touch-action: manipulation;
		cursor: pointer;
		user-select: none;
		color: #fff;
		background-color: #5cb85c;
		border: 1px solid transparent;
		border-color: #4cae4c;
		border-radius: 4px;
	}

	.oc-export-btn {
		display: none;
	}

	.box_foot {
		position: absolute;
		bottom: 6px;
		width: 100%;
		height: 20px;
		display: inline-block;
		left: 0;
		padding: 0 5px;
	}

	.orgchart .node .title .box_foot .dec {
		line-height: 16px;
		border-top: 1px solid #ddd;
	}

	.orgchart .node .title .box_foot .hash {
		line-height: 14px;
		min-width: inherit;
		color: green;
	}

	.dec.p_left {
		left: 0;
		float: left;
		width: 49%;
		border-right: 1px solid #ddd;
	}

	.dec.p_right {
		right: 0;
		width: 50%;
		float: right;
	}

	tbody td {
		border: 0;
	}

	

	/* 유저 아이콘*/
	.user_icon {
		background: #cbd2de;
		width: 100%;
		height: 100%;
		text-align: center;
		display: block;
		border-radius: 50%;
		display: inline-block;
		width: 20px;
		height: 20px;
		vertical-align: middle;
		line-height: 20px;
		margin-right: 5px;
		font-size: 13px;
	}

	.user_icon>i {
		font-weight: 300;
	}

	.user_icon.lv0 {
		color: white;
	}

	.user_icon.lv1 {
		background: #2b3a6d;
		color: white;
	}

	.user_icon.lv2 {
		background: #2b3a6d;
		color: gold;
	}

	.user_icon.lv3 {
		background: #2b3a6d;
		color: #40d0fb;
	}

	.user_icon.lv9 {
		color: black;
	}

	.user_icon.lv10 {
		color: black;
	}

	.badge {
		padding: 3px 6px;
		color: white;
		font-weight: 600;
	}

	/*검색바*/
	.searchid {
		color: green;
		font-weight: 600;
	}

	.search_nick {
		color: #666;
		font-weight: 300;
		margin-left: 10px;
	}

	/* 컬러 */

	.color0 {
		background: #cacaca !important;
		color: black
	}

	.color1 {
		background: #b55dccd9 !important
	}

	.color2 {
		background: #516feb !important;
	}

	.color3 {
		background: #09c3fd !important;
	}

	.color4 {
		background: #5ed2dc !important;
	}

	.color5 {
		background: #373cbc !important;
	}

	.color6 {
		background: #2b3a6d !important;
	}

	.color9 {
		background: #6214ab !important;
	}

	.color10 {
		background: #6214ab !important;
	}

	.grade_0 {
		background: #ddd !important;
	}

	.grade_1 {
		background: gold !important;
	}

	.grade_2 {
		background: green !important;
	}

	.grade_3 {
		background: red !important;
	}

	.grade_4 {
		background: orangered !important;
	}

	.grade_5 {
		background: blue !important;
	}

	.grade_6 {
		background: black !important;
	}

	.grade_9 {
		background: black !important;
	}

	#div_right table {
		border: 0px;
	}

	.btn_menu {
		padding: 5px;
		border: 1px solid #ced9de;
		background: rgb(246, 249, 250);
		cursor: pointer
	}

	.pool_img {
		display: none;
	}

	.lvl_img {
		width: 30px;
	}

	/* 추가내용 */
	/* .dark .orgchart .node .title{background:#212429;color:white} */
	.dark .orgchart .node .title{background:#fff}
	.dark #div_right{background:white}
	.dark .user_icon{width:20px !important;display:inline-grid !important}
	.dark strong{color:black;}
	.mb_name{margin:-3px 0 5px 0;font-weight:bold}
</style>

<!-- <script type="text/javascript">
	function clickExportButton(){
		 $(".oc-export-btn").click();
	}
</script> -->
<section class="structure_wrap">
				<!--<p>데이터 크기로 인해 한번에 5대씩 화면에 나타납니다</p>-->
				<!-- <div class="btn_input_wrap" style='background:white'>
					<div class="bin_top">회원 검색</div>
					<ul class="row align-items-center">
						<li class="col-9 user_search_wrap">
							<input type="text" id="now_id" class="" style='background:#eff3f9;color:black;border:1px solid #d9dfe8' placeholder="회원찾기"/>
						</li>
						<li class="col-3 search_btn_wrap">
							<button type="button" class="btn wd b_skyblue" id="binary_search" style="padding: 12px 10px;" onclick="member_search();"><i class="ri-search-line"></i></button>
						</li>
					</ul>
				</div> -->

				
<div id="div_right" style="width:100%;overflow-x:scroll;">

<?

$max_org_num = 80;
$org_num     = 5;

$sql = "SELECT c.c_id,c.c_class,(
	SELECT mb_level
	FROM g5_member
	WHERE mb_id=c.c_id) AS mb_level,(
	SELECT mb_name
	FROM g5_member
	WHERE mb_id=c.c_id) AS c_name,(
	SELECT COUNT(*)
	FROM g5_member
	WHERE mb_recommend=c.c_id) AS c_child,(
	SELECT mb_b_child
	FROM g5_member
	WHERE mb_id=c.c_id) AS b_child,(
	SELECT COUNT(mb_no)
	FROM g5_member
	WHERE mb_brecommend=c.c_id AND mb_leave_date = '') AS m_child,  (
	SELECT mb_no
	FROM g5_member
	WHERE mb_id=c.c_id) AS m_no
	,(select mb_rate FROM g5_member WHERE mb_id=c.c_id) AS mb_rate
	, ( select recom_sales FROM g5_member WHERE mb_id=c.c_id) AS recom_sales
	,(select pv FROM g5_member WHERE mb_id=c.c_id) AS mb_save_point
	,(select grade FROM g5_member WHERE mb_id=c.c_id) AS grade
	,(SELECT mb_child FROM g5_member WHERE mb_id=c.c_id) AS mb_children
	FROM g5_member m
	JOIN ".$class_name." c ON m.mb_id=c.mb_id
	WHERE c.mb_id='{$start_id}'
";

$srow = sql_fetch($sql);
$my_depth = strlen($srow['c_class']);

$sql    = "select c_class from ".$class_name." where mb_id='".$tree_id."' and c_id='".$go_id."'";
$row4   = sql_fetch($sql);

$mdepth = (strlen($row4['c_class'])/2);


//업데이트유무 확인
$sql = "select * from ".$class_name."_chk where cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$mrow = sql_fetch($sql);


if (!$srow['b_child']) $srow['b_child']=1;

?>
    <ul id="org" style="display:none;">
        <li>
            <?=(strlen($srow['c_class'])/2)-1?>-<?=($srow['c_child'])?>-<?=($srow['b_child']-1)?>
            |<?=get_member_label($srow['mb_level'])?>
            |<?=$srow['c_id']?>|<?=$srow['c_name']?>
            |<?=Number_format($brecom_info['LEFT']['hash'])?>
            |<?=Number_format($brecom_info['RIGHT']['hash'])?>
            |<?=$srow['mb_level']?>
            |<?=pv($brecom_info['LEFT']['sales'])?>
            |<?=pv($brecom_info['RIGHT']['sales'])?>
            |<?=pv($srow['recom_sales'])?>
            |<?=($srow['mb_children']-1)?>
            |<?=pv($srow['mb_save_point'])?>
            |<?=$srow['grade']?>
            |<?=Number_format($srow['mb_rate'])?>
            |<?=pv($recom_info['sales_10'])?>
            |<?=($srow['c_child'])?>
            |<?=($srow['b_child']-1)?>
            |<?=Number_format($recom_info['hash_10'])?>
            |<?=$gubun?>
            <? 
			get_org_down($srow);
?>
        </li>
        <?
		//업데이트 완료 
		if ($mrow['cc_run']==0){
			$sql = "update ".$class_name."_chk set cc_run=1 where cc_no='{$mrow['cc_no']}'";
			sql_query($sql);
		}
?>
    </ul>

    <div>

        <button type='button' id='zoomOut' class='zoom2-btn'>Zoom Out</button>
        <button type='button' id='zoomIn' class='zoom-btn'>Zoom In</button>


    </div>
    <div id="chart-container" class="orgChart"></div>
    <script>
    $(function() {

        $('#chart-container').orgchart({
            'data': $('#org'),
            'zoom': true,
        });

        var $container = $('#chart-container');
        var $chart = $('.orgchart');

        var div = $chart.css('scale', '0.6');
        var div = $chart.css('transform','matrix(1,0,0,1,315,-570)');
        var div = $chart.css('transform');
        var currentZoom = 0.6;
        var zoomval = 1;

        $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);
        var my_num = 0;

        // zoom buttons	
        $('#zoomIn').on('click', function() {
            my_num++;
            zoomval = currentZoom += 0.1;
            $chart.css("transform", 'matrix(' + zoomval + ', 0, 0, ' + zoomval + ', 315 ,' + (-570 + (
                my_num) * 85) + ')');
            $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);
        });

        $('#zoomOut').on('click', function() {
            zoomval = currentZoom -= 0.1;
            my_num--;
            $chart.css("transform", 'matrix(' + zoomval + ', 0, 0, ' + zoomval + ', 315 ,' + (-570 + (
                my_num) * 85) + ')');
            $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);

        });

    });
    </script>
</div>

<script type="text/javascript">
var go_id = '<?=$member['mb_id'];?>';

function set_member(set_id, set_type) {
    window.open('/adm/recommend_set.php?set_id=' + set_id + '&set_type=' + set_type + '&now_id=' + $("#now_id").val(),
        'set_recomm', 'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
}

function open_register() {
    window.open('/adm/shop/recommend_register.php?gp=ao&now_id=' + $("#now_id").val(), 'set_register',
        'width=600, height=500, resizable=no, scrollbars=no, left=0, top=0');
}



$(document).ready(function() {
    $("#fr_date, #to_date").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        maxDate: "+0d"
    });
    <?php if ($stx && $sfl){ ?>
    btn_search();
    <?php } ?>
});

function edit_member(edit_id) {
    window.open('/adm/recommend_edit.php?gubun=<?=$gubun?>&edit_id=' + edit_id, 'edit_recomm',
        'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
    /*
    if(event.button==2){	
    	window.open('recommend_edit.php?gubun=<?=$gubun?>&edit_id='+edit_id, 'edit_recomm', 'width=520, height=500, resizable=no, scrollbars=yes, left=0, top=0');
    }else{
    	go_member(edit_id);
    }
    */
}

function go_member(go_id) {
    $("#now_id").val(go_id);
	
	console.log(go_id);

    $.get("/util/ajax_get_org_load.php?gubun=<?=$gubun?>&fr_date=<?=$fr_date?>&to_date=<?=$to_date?>&go_id=" + go_id,
        function(data) {
            $('#div_right').html(data);
        });
}

go_member(go_id);

function btn_print() {
    var html = $('#chart-container');

    var strHtml =
        `<!doctype html><html lang="ko"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="imagetoolbar" content="no" /><title></title><link rel="stylesheet" type="text/css" media="all" href="/adm/jquery.orgchart.css"></`;
    strHtml +=
        `head><body style="padding:0px;margin:0px;"><div id="chart-container" class="orgChart"><!--body--></div></body></html>`;
    var strContent = html.html();
    var objWindow = window.open('', 'print', 'width=640, height=800, resizable=yes, scrollbars=yes, left=0, top=0');
    if (objWindow) {
        var strSource = strHtml;
        strSource = strSource.replace(/\<\!\-\-body\-\-\>/gi, strContent);

        objWindow.document.open();
        objWindow.document.write(strSource);
        objWindow.document.close();

        setTimeout(function() {
            objWindow.print();
        }, 500);
    }
}

function btn_menu() {
    if ($("#div_left").css("display") == "none") {
        $("#div_left").show();
        $("#div_right").css("width", "85%");
    } else {
        $("#div_left").hide();
        $("#div_right").css("width", "100%");
    }
}

function btn_search() {
    if ($("#stx").val() == "") {
        //	alert("검색어를 입력해주세요.");
        $("#stx").focus();
    } else {
        $.post("/util/ajax_get_tree_member.php", $("#sForm").serialize(), function(data) {
            $("#div_result").html(data);
        });
    }
}

function btn_org() {
    if (confirm("조직도를 재구성 하시겠습니까?")) {
        location.href = "page.php?id=binary&reset=1&sfl=<?=$sfl?>&stx=<?=$stx?>&gubun=<?=$gubun?>";
    }
}
</script>