<?php
$sub_menu = "600250";
include_once('./_common.php');

$g5['title'] = "수당지급 내역통계";
$excel_down = true;
include_once(G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-90 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date."&amp;to_id=".$fr_id;
$query_string = $qstr ? '?'.$qstr : '';


?>

<style>
.red {
    color: red
}

.text-center {
    text-align: center
}

.sch_last {
    display: inline-block;
}

.rank_img {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.btn_submit {
    width: 100px;
    margin-left: 20px;
}

.black_btn {
    background: #333 !important;
    border: 1px solid black !important;
    color: white;
}
</style>

<script>
$(function() {
    $("#fr_date, #to_date").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        maxDate: "+0d"
    });
});

function fvisit_submit(act) {
    var f = document.fvisit;
    f.action = act;
    f.submit();
}
</script>



<?
$colspan = 9;

$sql = " SELECT count(A.cnt) as total FROM (select count(DAY) AS cnt FROM soodang_pay WHERE DAY between '{$fr_date}' and '{$to_date}' Group by DAY) AS A ";
$total_count = sql_fetch($sql)['total'];

$rows = 50;

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$sql = " select day
            FROM soodang_pay WHERE DAY between '{$fr_date}' and '{$to_date}'
            Group by day ORDER BY DAY DESC limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">

<form name="fvisit" id="fvisit" class="local_sch02 local_sch" method="get">
<div>
    <div class="sch_last">
        <strong>기간별검색</strong>
        <input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input" size="15" style="width:120px" maxlength="10">
        <label for="fr_date" class="sound_only">시작일</label>
        ~
        <input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input" size="15" style="width:120px" maxlength="10">
        <label for="to_date" class="sound_only">종료일</label>
        
    </div>

    <!-- <div class="sch_last" style="margin-left:20px">
        <strong>센터멤버검색</strong>
        <input type="text" name="fr_id" value="<?php echo $fr_id ?>" id="fr_id" class="frm_input" size="15" style="width:120px" maxlength="10">
        <label for="fr_id" class="sound_only">회원아이디</label>
    </div> -->

    <input type="submit" value="검색" class="btn_submit">
    <input type="button" class="btn_submit excel" id="btnExport" data-name="bonus_total_history" value="엑셀 다운로드">
</div>

<div class="tbl_head01 tbl_wrap" style="min-height:800px;">
    <table id ="table">
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
            <tr>
                <th>no</th>
                <th>수당지급일</th>
                <th>수당지급합계</th>
                <th>데일리</th>
                <th>추천</th>
                <th>롤업</th>
                <th>직급</th>
                <th>센터</th>
                <th>기타(관리자지급)</th>

            </tr>
        </thead>
        <tbody>
            <div>수당 지급 통계</div>
            <?php
    for ($i=0; $rows=sql_fetch_array($result); $i++) {
       
        $bonus_day = $rows['day'];
        $bg = 'bg'.($i%2);

        
        $day_total = "SELECT 
        SUM(hap) AS  total,
        MAX(IF(allowance_name = 'daily', hap, 0)) AS daily_hap,
        MAX(IF(allowance_name = 'direct', hap, 0)) AS direct_hap,
        MAX(IF(allowance_name = 'rollup', hap, 0)) AS rollup_hap,
        MAX(IF(allowance_name = 'grade', hap, 0)) AS grade_hap,
        MAX(IF(allowance_name = 'center', hap, 0)) AS center_hap,
        MAX(IF(allowance_name = 'balance changed', hap, 0)) AS etc_hap
         FROM 
        (SELECT allowance_name, SUM(benefit) AS hap
        FROM soodang_pay WHERE DAY = '{$bonus_day}' GROUP BY allowance_name)  A  ";
        
        $day_total_result = sql_query($day_total);

        while( $row = sql_fetch_array($day_total_result)){

            $total += $row['total'];
            $daily_hap += $row['daily_hap'];
            $direct_hap += $row['direct_hap'];
            $rollup_hap += $row['rollup_hap'];
            $grade_hap += $row['grade_hap'];
            $center_hap += $row['center_hap'];
            $etc_hap += $row['etc_hap'];
    ?>

            <tr class="<?php echo $bg; ?>">
                <td class='no'><?=$i?></td>
                <td class='no'><?=$bonus_day?></td>

                <td class='text-center'><?=Number_format($row['total'])?></td>
                <td class='text-center'><?=Number_format($row['daily_hap'])?></td>
                <td class='text-center'><?=Number_format($row['direct_hap'])?></td>
                <td class='text-center'><?=Number_format($row['rollup_hap'])?></td>
                <td class='text-center'><?=Number_format($row['grade_hap'])?></td>
                <td class='text-center'><?=Number_format($row['center_hap'])?></td>
                <td class='text-center'><?=Number_format($row['etc_hap'])?></td>
            </tr>

            <?php
        }
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
    ?>
        </tbody>
        <tfoot>
        <td><?=$i?></td>
        <td>합계</td>
        <td><?=Number_format($total)?></td>
        <td><?=Number_format($daily_hap)?></td>
        <td><?=Number_format($direct_hap)?></td>
        <td><?=Number_format($rollup_hap)?></td>
        <td><?=Number_format($grade_hap)?></td>
        <td><?=Number_format($center_hap)?></td>
        <td><?=Number_format($etc_hap)?></td>
    </tfoot>
    </table>
</div>
</form>

<?php
if (isset($domain))
    $qstr .= "&amp;domain=$domain";
    $qstr .= "&amp;page=";

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;
?>

<script>
   
</script>


<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>