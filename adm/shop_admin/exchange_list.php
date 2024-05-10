<?php
$sub_menu = '400401';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$sql_common = " from sh_shop_order ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    // switch ($sfl) {
    //     case 'pp_id' :
    //         $sql_search .= " ({$sfl} = '{$stx}') ";
    //         break;
    //     case 'od_id' :
    //         $sql_search .= " ({$sfl} = '{$stx}') ";
    //         break;
    //     default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            // break;
    // }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "no";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$g5['title'] = '주문내역(교환)';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$colspan = 10;
?>

<div class="local_ov01 local_ov">
   <span class="btn_ov01"><span class="ov_txt">전체 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <select name="sfl" title="검색대상">
        <option value="order_name"<?php echo get_selected($sfl, "order_name"); ?>>발신자</option>
        <option value="delivery_name"<?php echo get_selected($sfl, "delivery_name"); ?>>수신자</option>
        <option value="order_code"<?php echo get_selected($sfl, "order_code"); ?>>결제ID</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
    <input type="submit" class="btn_submit" value="검색">
</form>



<form name="fpersonalpaylist" id="fpersonalpaylist" method="post" action="./exchange_list_proc.php" onsubmit="return fpersonalpaylist_submit(this);">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">개인결제 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">No.</th>
        <th scope="col"><?php echo subject_sort_link('order_code') ?>결제ID</a></th>
        <th scope="col">상품명</th>
        <th scope="col">입금금액</th>
        <th scope="col">수량</th>
        <th scope="col">발신자</th>
        <th scope="col">발신자 연락처</th>
        <th scope="col">수신자</th>
        <th scope="col">수신자 연락처</th>
        <th scope="col">수신자 주소</th>
        <th scope="col"><?php echo subject_sort_link('tot_state') ?>상태</a></th>
        <th scope="col"><?php echo subject_sort_link('datetime') ?>입금일</a></th>
        <th scope="col"><?php echo subject_sort_link('complete_date') ?>상태변경일</a></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // if($row['od_id'])
        //     $od_id = '<a href="./orderform.php?od_id='.$row['od_id'].'" target="_blank">'.$row['od_id'].'</a>';
        // else
        //     $od_id = '&nbsp;';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" id="no<?php echo $i; ?>" name="no[<?php echo $i; ?>]" value="<?php echo $row['no']; ?>">
            <input type="checkbox" id="chk_<?php echo $i; ?>" name="chk[]" value="<?php echo $i; ?>" title="내역선택">
    
        <td><?php echo get_text($row['no']); ?></td>
        <td><a href="https://etherscan.io/tx/<?=$row['order_code']?>" style="font-style:italic;text-decoration:underline;" target="_blank"> <?php echo substr($row['order_code'],0,12)." ... ".substr($row['order_code'],strlen($row['order_code'])-12,12) ?></a></td>
        <td><?=$row['mask_type'] == "A" ? "유아마스크" : "어린이마스크"?></td>
        <td><?php echo number_format($row['goods_order_total']); ?></td>
        <td><?php echo $row['order_total'] ?></td>
        <td><?php echo $row['order_name'] ?></td>
        <td><?php echo hyphen_hp_number($row['order_hp1']) ?></td>
        <td><?php echo $row['delivery_name']; ?></td>
        <td><?php echo hyphen_hp_number($row['delivery_hp1']) ?></td>
        <td class="td_left"><?php echo $row['delivery_addr2']?>(<?=$row['delivery_addr3']?>)<br><?=$row['delivery_addr1']?></td>
        <td>
        <select name="status" id="status" data-no="<?=$row['no']?>">
        <option value="0" <?=$row['tot_state'] == '0' ? 'selected' : '' ?>>입금</option>
        <option value="1" <?=$row['tot_state'] == '1' ? 'selected' : '' ?>>입금확인</option>
        <option value="2" <?=$row['tot_state'] == '2' ? 'selected' : '' ?>>배송준비중</option>
        <option value="3" <?=$row['tot_state'] == '3' ? 'selected' : '' ?>>발송</option>
        </select>
        </td>
        <td ><?=$row['datetime']?></td>
        <td ><?=$row['complete_date']?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>

function fpersonalpaylist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

$('select[name=status]').change(function(){

    var result = confirm("상태값을 변경하시겠습니까?")
    if(result){

        $.ajax({
            url:'./exchange_list_proc.php',
            dataType:'json',
            type:"POST",
            async:false,
            cache: false,
            data:{
                no : $(this).data('no'),
                status : $(this).val()
            },
            success: function(res){
        
                    alert(res.result)
                    window.location.reload()
               
            }

        })
       
    }

})
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');