<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
// add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>
<link rel="stylesheet" href="<?=$qa_skin_url?>/style.css">


<form name="fqalist" id="fqalist" class='fqalist' action="./qadelete.php" onsubmit="return fqalist_submit(this);" method="post">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="token" value="<?php echo get_text($token); ?>">


<!-- <?php if ($admin_href || $write_href) { ?>
<ul class="btn_top top btn_bo_user">
    <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_b03"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only">관리자</span></a></li><?php } ?>
	<?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b03"><i class="fa fa-pencil" aria-hidden="true"></i><span class="sound_only">문의등록</span></a></li><?php } ?>
    <?php if ($is_admin == 'super' || $is_auth) {  ?>
	<li>
		<button type="button" class="btn_more_opt btn_b03 btn"><i class="fa fa-ellipsis-v" aria-hidden="true"></i><span class="sound_only">게시판 리스트 옵션</span></button>
		<?php if ($is_checkbox) { ?>	
        <ul class="more_opt">
            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"><i class="fa fa-trash-o" aria-hidden="true"></i> 선택삭제</button></li>
        </ul>
        <?php } ?>

        <script>
        // 게시판 리스트 관리자 옵션
		$(".btn_more_opt").on("click", function() {
		    $(".more_opt").toggle();
		})
		</script>
	</li>
	<?php } ?>
</ul>
<?php } ?> -->

<div id="bo_list">

    <?php if ($category_option) { ?>
    <!-- 카테고리 시작 { -->
    <!-- <nav id="bo_cate">
        <h2><?php echo $qaconfig['qa_title'] ?> 카테고리</h2>
        <ul id="bo_cate_ul">

            <?php if ($is_checkbox) { ?>
                <li class="all_chk chk_box">
                    <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" class="selec_chk">
                    <label for="chkall" style="margin-right:10px;"><span></span></label>
                </li>
            <?php } ?>

            <?php echo $category_option ?>
        </ul>
    </nav> -->
    <!-- } 카테고리 끝 -->
    <?php } ?>


    <div class="list_01">
        <ul>
            <?php
            for ($i=0; $i<count($_list); $i++) {
            ?>
            <li class="bo_li<?php if ($is_checkbox) echo ' bo_adm'; ?>">
                <?php if ($is_checkbox) { ?>
                <div class="bo_chk chk_box">
                    <input type="checkbox" name="chk_qa_id[]" value="<?php echo $_list[$i]['qa_id'] ?>" id="chk_qa_id_<?php echo $i ?>" class="selec_chk">
                	<label for="chk_qa_id_<?php echo $i ?>">
                    	<span></span>
                    	<!-- <b class="sound_only"><?php echo $_list[$i]['subject'] ?></b> -->
                        <div class="bo_cate_link"><?php echo $_list[$i]['category']; ?></div>	
                    </label>
                </div>
                <?php } ?>
                <div class="bo_cnt">
                	<!-- <div>
                		<strong class="bo_cate_link"><?php echo $_list[$i]['category']; ?></strong>	
                	</div> -->
                    <a href="<?php echo $_list[$i]['view_href']; ?>" class="bo_subject">
                        <?php echo $_list[$i]['subject']; ?>
                        <?php if ($_list[$i]['icon_file']) echo " <i class=\"fa fa-download\" aria-hidden=\"true\"></i>" ; ?>
                    </a>
                </div>
                
                <div class="li_info">
                    <span class="sound_only">작성자: </span><span class='strong'><?php echo $_list[$i]['name']; ?> </span> | 
                    <span class="bo_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $_list[$i]['date']; ?></span>
                    <div class="li_stat <?php echo ($_list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy'); ?>"><?php echo ($_list[$i]['qa_status'] ? '답변완료' : '답변대기'); ?></div>
                </div>
            </li>
            <?php
            }
            ?>
            <?php if ($i == 0) { echo '<li class="empty_list">게시물이 없습니다.</li>'; } ?>
        </ul>
    </div>
</div>
</form>



<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<!-- 페이지 -->
<?php echo $list_pages;  ?>



<div id="bo_list_total">
    <span>전체 <?php echo number_format($total_count) ?>건</span>
    <?php echo $page ?> 페이지
</div>

<!-- 게시판 검색 시작 { -->

<?php if ($write_href) { ?><div class='btn_layer'><a href="<?php echo $write_href ?>" class="btn_b03 write_btn"><span >문의등록</span></a></div><?php } ?>

<!-- } 게시판 검색 끝 -->

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fqalist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_qa_id[]")
            f.elements[i].checked = sw;
    }
}

function fqalist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_qa_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다"))
            return false;
    }

    return true;
}
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->

<script>
	$(function(){
		$(".top_title h3").html("<span >1:1문의</span>");
	});
</script>