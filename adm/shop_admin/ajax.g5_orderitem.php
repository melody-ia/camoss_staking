<?php
$sub_menu = '400400';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

// $od_id = isset($_POST['od_id']) ? safe_replace_regex($_POST['od_id'], 'od_id') : 0;

$pay_id = $_POST['pay_id'];
$sql = " select * from soodang_pay where pay_id = '{$pay_id}' ";
$pay = sql_fetch($sql);

if(! ($pay['pay_id'] && $pay['pay_id']))
    die('<div>주문정보가 존재하지 않습니다.</div>');

// 상품목록
$sql = " select allowance_name, SUM(benefit) AS group_benefit from soodang_pay WHERE pay_id = '{$pay_id}' GROUP BY allowance_name";
$result = sql_query($sql);
?>
<style>
    #orderitemlist tfoot td{height:15px;background:#eee}
</style>
<section id="cart_list">
    <h2 class="h2_frm"><?=$pay_id?> 누적 보너스 기록</h2>

    <div class="tbl_head01 tbl_wrap bonus_detail">
        <table>
        <caption>보너스기록</caption>
        <thead>
        <tr>
            <th scope="col">보너스(수당)명</th>
            <th scope="col">누적금액</th>
            <th scope="col">상태</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
            
           

            // 배송비
            switch($row['ct_send_cost'])
            {
                case 1:
                    $ct_send_cost = '착불';
                    break;
                case 2:
                    $ct_send_cost = '무료';
                    break;
                default:
                    $ct_send_cost = '선불';
                    break;
            }
            $total_benefit += $row['group_benefit']; 
            ?>
            <tr>
                <td class="td_mngsmall"><?php echo $row['allowance_name']; ?></td>
                <td class="td_cntsmall" style='text-align:right'><?php echo number_format($row['group_benefit'],2); ?></td>
                <td class="td_num"></td>
                <!-- <td class="td_num"><?php echo number_format($ct_price['stotal']); ?></td>
                <td class="td_num"><?php echo number_format($opt['cp_price']); ?></td>
                <td class="td_num"><?php echo number_format($ct_point['stotal']); ?></td>
                <td class="td_sendcost_by"><?php echo $ct_send_cost; ?></td> -->
            </tr>
            
        <?php
        }
        ?>
        <tfoot>
            <tr>
                <td>누적합계</td>
                <td style='text-align:right'><?=number_format($total_benefit,2)?></td>
                <td></td>
            </tr>
        </tfoot>

        </tbody>
        </table>
    </div>
</section>