<?php
include_once('./_common.php');

$od_id = $_POST['od_id'];
$func = $_POST['func'];
// $od_id = '2021122416374801';

$debug = false;

if($debug){
    $func = 'delete';
    $od_id = '2024102523373501';
}

$od_item_sql = "SELECT * from g5_order WHERE od_id = {$od_id}";
$od_item = sql_fetch($od_item_sql); 

$now_datetime = G5_TIME_YMDHIS;

function  od_name_return_rank($val){
    // if(strlen($val) < 5){
    //     return substr($val,1,1);
    // }else{
    //     return 0;
    // }
    $sql = "select it_maker from g5_item where it_name = '{$val}'";
    return strtolower(sql_fetch($sql)['it_maker']);
}

if($func == 'delete'){
    if($od_item){

        $amt = $od_item['od_cash'];
        $upstair = $od_item['upstair'];
        $pv = $od_item['pv'];
        $od_misu = $od_item['od_misu'];
        $mb_id = $od_item['mb_id'];

        if($od_item['od_cash_no'] != "재구매"){

            //상품생성 테이블 삭제 
            $rank_num = od_name_return_rank($od_item['od_name']);
            $package_group = "package_".$rank_num;

            $package_have_sql = "SELECT * from {$package_group} WHERE od_id = '{$od_item['od_id']}' ";
            $package_have = sql_fetch($package_have_sql);
        
            print_R($package_have_sql);
            echo "<br><br>";
        
            if($package_have){
                $del_package = "DELETE FROM {$package_group} WHERE od_id = '{$od_item['od_id']}' ";
        
                print_R($del_package);
                echo "<br><br>";
                
                if(!$debug){
                    $pack_del_result = sql_query($del_package);
                }else{
                    $pack_del_result = 1;
                }
            }
        
            // 금액반환처리
            if($od_item['od_refund_price'] > 0){
                $amt1 = $od_item['od_refund_price'];
                $amt2 = $amt - $od_item['od_refund_price'];
                $amt_txt = $amt2.' / '.$amt1;

                $update_member_sql = "UPDATE g5_member set mb_deposit_calc= mb_deposit_calc + {$amt2}, mb_balance_calc = mb_balance_calc + {$amt1}, mb_save_point = mb_save_point - {$upstair}, mb_rate = mb_rate - {$pv}, 
                pv = pv - {$upstair}, mb_index = mb_index - {$od_misu} ";
            }else{
                $update_member_sql = "UPDATE g5_member set mb_deposit_calc= mb_deposit_calc + {$amt}, mb_save_point = mb_save_point - {$upstair}, mb_rate = mb_rate - {$pv}, 
                pv = pv - {$upstair}, mb_index = mb_index - {$od_misu} ";
                $amt_txt = $amt;
            }

            if($rank_num == 0){
                $update_member_sql .=", sales_day = '0000-00-00' , rank_note = '' , rank = 0 ";
            }else{
                $update_member_sql .=", rank_note = 'Membership-PACK' , rank = 1 ";
            }
            $update_member_sql .= " WHERE mb_id = '{$mb_id}' ";
        
            print_R($update_member_sql);
            echo "<br><br>";
            
            if(!$debug){
                $update_result = sql_query($update_member_sql);
            }else{
                $update_result = 1;
            }
        

        }else{
           
            $update_member_sql = "UPDATE g5_member SET pv = pv - {$amt}, 
            mb_balance_calc = mb_balance_calc + {$amt}, mb_index = mb_index - {$od_misu} WHERE mb_id = '{$mb_id}'";
        
            $update_result = sql_query($update_member_sql);
        }

        if($update_result){
            $de_data = $od_item['od_name']." | ".$amt." | ".$od_item['od_status'].' 건 구매취소 | '.$amt_txt;
            $od_del_log_sql = "INSERT g5_order_delete set de_key = {$od_item['od_id']}
            , de_data = '{$de_data}'
            , mb_id = '{$od_item['mb_id']}'
            , de_ip = '{$_SERVER['REMOTE_ADDR']}'
            , de_datetime = '{$now_datetime}' ";

            print_R($od_del_log_sql);
            echo "<br><br>";
            
            if(!$debug){
                $result = sql_query($od_del_log_sql);
            }else{
                $result = 1;
            }

            if($result){
                $del_odlist_sql = "DELETE from g5_order WHERE od_id = {$od_id} ";
                
                echo $del_odlist_sql;
                echo "<br><br>";

                if(!$debug){
                    $del_odlist_result = sql_query($del_odlist_sql);
                }else{
                    $del_odlist_result = 1;
                }
            }
        }

    }
}else if($func == 'modifyOrder'){
    

    if($od_item){
        
        $soodang_date = $_POST['soodang_date'];
        $mod_sql = "UPDATE g5_order set od_soodang_date = '{$soodang_date}' WHERE od_id = {$od_item['od_id']} ";
        $del_odlist_result = sql_fetch($mod_sql);
    }
}



if(!$debug){
    if($del_odlist_result){
        ob_end_clean();
        echo json_encode(array("response"=>"OK", "data"=>'complete'));
    }else{
        ob_end_clean();
        echo json_encode(array("response"=>"FAIL", "data"=>"<p>ERROR<br>Please try later</p>"));
    }
}


?>