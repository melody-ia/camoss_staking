<?php

include_once('./_common.php');
include_once('../adm/inc.member.class.php');

$max_org_num = 50;
$org_num     = 8;

$start_id = $config['cf_admin'];

// $go_id = $member['mb_id'];

if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}

$mem_list = [];

if($_GET['reset']){
	$sql  = "select count(*) as cnt from g5_member";
	$mrow = sql_fetch($sql);

	$sql = "select * from g5_member_class_chk where mb_id='".$tree_id."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
	$row = sql_fetch($sql);

	if ($mrow['cnt']>$row['cc_usr'] || !$row['cc_no'] || $_GET["reset"]){

		make_habu('');

		$sql = "delete from g5_member_class ";
		sql_query($sql);

		get_recommend_down($tree_id,$tree_id,'11');

		$sql  = " select * from g5_member_class where mb_id='{$tree_id}' order by c_class asc";	
		$result = sql_query($sql);
		for ($i=0; $row=sql_fetch_array($result); $i++) { 
			$row2 = sql_fetch("select count(c_class) as cnt from g5_member_class where  mb_id='".$tree_id."' and c_class like '".$row['c_class']."%'");
			$sql = "update g5_member set mb_child='".$row2['cnt']."' where mb_id='".$row['c_id']."'";
			sql_query($sql);
		}

		$sql = "insert into g5_member_class_chk set mb_id='".$tree_id."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow['cnt']."'";
		sql_query($sql);

	}


	$sql = "select * from g5_member_bclass_chk where mb_id='".$tree_id."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
	$row = sql_fetch($sql);

	if ($mrow['cnt']>$row['cc_usr'] || !$row['cc_no'] || $_GET["reset"]){
		
		make_habu('B');
		$sql = "delete from g5_member_bclass" ;
		sql_query($sql);

		get_brecommend_down($tree_id,$tree_id,'11');

		$sql  = " select * from g5_member_bclass where mb_id='{$tree_id}' order by c_class asc";	
		$result = sql_query($sql);
		for ($i=0; $row=sql_fetch_array($result); $i++) { 
			$row2 = sql_fetch("select count(c_class) as cnt from g5_member_bclass where  mb_id='".$tree_id."' and c_class like '".$row['c_class']."%'");
			$sql = "update g5_member set mb_b_child='".$row2['cnt']."' where mb_id='".$row['c_id']."'";
			sql_query($sql);
		}

		$sql = "insert into g5_member_bclass_chk set mb_id='".$tree_id."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow['cnt']."'";
		sql_query($sql);

	}

	exit;
}

// 후원트리 하부
function brecommend_array($brecom_id, $count, $limit=0)
{
    global $mem_list;

    // $new_arr = array();
    $b_recom_sql = "SELECT mb_id,mb_level,mb_name,grade,mb_rate,mb_save_point,mb_brecommend_type,pv from g5_member WHERE mb_brecommend='{$brecom_id}' ";
    $b_recom_result = sql_query($b_recom_sql);
    $cnt = sql_num_rows($b_recom_result);

    if ($cnt < 1) {
        // 마지막
    } else {
        ++$count;
        while ($row = sql_fetch_array($b_recom_result)) {
            brecommend_array($row['mb_id'], $count,$limit);

            // print_R($count.' :: '.$row['mb_id']."<br>");
            // $mem_list[$count]['id'] = $brecom_id;
            if($limit != 0 && $count <= $limit){
                $row['count'] = $count;
                array_push($mem_list, $row);
            }
            
        }
    }
	
    return $mem_list;
} 




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
	WHERE c.mb_id='{$start_id}' AND c.c_id='$go_id'
";
$srow = sql_fetch($sql);
$my_depth = strlen($srow['c_class']);



if ($order_proc==1){
	$sql  = "select today as tpv from brecom_bonus_today where mb_id='".$srow['c_id']."'";
	$row2 = sql_fetch($sql);

	$sql  = "select noo as tpv from ".$ngubun."recom_bonus_noo where mb_id='".$srow['c_id']."'";
	$row3 = sql_fetch($sql);


	$sql  = "select thirty as tpv from ".$ngubun."thirty where mb_id='".$srow['c_id']."'";
	$row5 = sql_fetch($sql);
}else{


	$sql  = "select no,today as tpv from ".$ngubun."today where mb_id='".$srow['c_id']."'";
	$row2 = sql_fetch($sql);

	if ($row2['no']){

	}else{

		$sql  = "select ".$order_field." as tpv from g5_order where mb_id='".$srow['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
		$row2 = sql_fetch($sql);
		if (!$row2['tpv']) $row2['tpv'] = 0;
		sql_query("insert ".$ngubun."today SET today=".$row2['tpv']." ,mb_id='".$srow['c_id']."'");	
	}

	$sql  = "select no,noo as tpv from ".$ngubun."noo where mb_id='".$srow['c_id']."'";
	$row3 = sql_fetch($sql);
	if ($row3['no']){

	}else{
		$sql  = "select ".$order_field." as tpv from g5_order where mb_id in (select c_id from ".$class_name." where mb_id='".$member['mb_id']."'  and c_class like '".$srow['c_class']."%') and od_receipt_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
		$row3 = sql_fetch($sql);

		$row3 = sql_fetch($sql);
		if (!$row3['tpv']) $row3['tpv'] = 0;
		$sql  = "insert ".$ngubun."noo SET noo=".$row3['tpv']." ,mb_id='".$srow['c_id']."'";
		sql_query($sql);	
	}

	//���� 30��
	$sql  = "select no,thirty as tpv from ".$ngubun."thirty where mb_id='".$srow['c_id']."'";
	$row5 = sql_fetch($sql);
	if ($row5['no']){

	}else{
		$sql  = "select ".$order_field." as tpv from g5_order where mb_id in (select c_id from ".$class_name." where mb_id='".$member['mb_id']."' and c_class like '".$srow['c_class']."%') and od_receipt_time between '".Date("Y-m-d",time()-(60*60*24*30))." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
		$row5 = sql_fetch($sql);
		if (!$row5['tpv']) $row5['tpv'] = 0;
		sql_query("insert ".$ngubun."thirty SET thirty=".$row5['tpv']." ,mb_id='".$srow['c_id']."'");	
	}

}


if ($srow['b_recomm']){
	$left_sql = " SELECT mb_rate,mb_save_point,pv, (SELECT noo FROM brecom_bonus_noo WHERE mb_id ='{$srow['b_recomm']}' ) AS noo FROM g5_member WHERE mb_id = '{$srow['b_recomm']}' ";
	
	$mb_self_left_result = sql_fetch($left_sql);
	$mb_self_left_acc = $mb_self_left_result['pv'] + $mb_self_left_result['noo'];
	$row6['tpv'] = $mb_self_left_acc ;
	
}else{
	$row6['tpv'] = 0;
}

if ($srow['b_recomm2']){
	$right_sql = " SELECT mb_rate,mb_save_point,pv, (SELECT noo FROM brecom_bonus_noo WHERE mb_id ='{$srow['b_recomm2']}' ) AS noo FROM g5_member WHERE mb_id = '{$srow['b_recomm2']}' ";
	$mb_self_right_result = sql_fetch($right_sql);
	$mb_self_right_acc = $mb_self_right_result['pv'] + $mb_self_right_result['noo'];
	$row7['tpv'] = $mb_self_right_acc ;
}else{
	$row7['tpv'] = 0;
}

$sql    = "select c_class from ".$class_name." where mb_id='".$member['mb_id']."' and c_id='".$go_id."'";
$row4   = sql_fetch($sql);
$mdepth = (strlen($row4['c_class'])/2);

			

			if (!$srow['b_child']) $srow['b_child']=1;
			//if (!$srow['c_child']) $srow['c_child']=1;

			$member_info_data = sql_fetch("SELECT * FROM g5_member_info WHERE mb_id ='{$srow['c_id']}' order by date desc limit 0,1 ");
			$recom_info = json_decode($member_info_data['recom_info'],true);
			$brecom_info = json_decode($member_info_data['brecom_info'],true);

			
if ($srow['c_class']){
?>

		<ul id="org" style="display:none" >
			<li>
			[<?=(strlen($srow['c_class'])/2)-1?>-<?=($srow['c_child'])?>-<?=($srow['b_child']-1)?>]
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

			
				/* $line = brecommend_array($go_id,0,99);

				if(count($line) > 0){
					for($i=0; $i < count($line); $i++){

						$mem_row = array_reverse($line)[$i];
						

						
						echo "<ul><li>";
						echo ($i+1)."-1-1";
						echo "| ".$mem_row['mb_level'];
						echo "| ".$mem_row['mb_id']."|".$mem_row['mb_name'];
						echo " |0 ";
						echo " |0 ";
						echo " |1 ";
						echo " |0 ";
						echo " |0 ";
						echo " |1000 ";
						echo " | ";
						echo " | ";
						echo " | ";
						echo " |0 ";
						echo " |0 ";
						echo " |0 ";
						echo " |0 ";
						echo " |0 ";
						echo " |".$gubun;
					}

					for($i=0; $i < count($line); $i++){
						echo "</li></ul>";
					}
				}  */

				
				



				/* echo "
				<ul><li>1-1-2	|비회원	|test15|test15	|0				|0	|1	|0	|0	|1,000,000	|69	|50,000,000	|1	|0	|0	|1	|2	|0	|B
				
				<ul><li>2-1-1	|비회원	|test16|test16	|0				|0	|1	|0	|0	|500,000	|68	|500,000	|1	|0	|0	|1	|1	|0	|B
				
				<ul><li>3-1-0	|비회원	|test17|test17	|0				|0	|1	|0	|0	|0	|67	|500,000	|0	|0	|0	|1	|0	|0	|B 

				</li></ul>
				</li></ul>
				</li></ul>
				"; */
			?>
			</li>
		</ul>

		<div id="div_result"></div>
        <button type='button' id='zoomOut' class='zoom2-btn'>Zoom Out</button>
        <button type='button' id='zoomIn' class='zoom-btn'>Zoom In</button>


    </div>
	<?
if(count($depth_arr) > 0){
	$org_depth = max($depth_arr) - min($depth_arr);
}else{
	$org_depth	=0;
}

?>
    <div id="chart-container" class="orgChart"></div>

    <script>
    $(function() {

        $('#chart-container').orgchart({
            'data': $('#org'),
            'zoom': true,
        });

		console.log('b_child ::' + '<?=$org_depth ?>');
		var org_depth = <?=$org_depth ?>;
        var $container = $('#chart-container');
        var $chart = $('.orgchart');
        var div = $chart.css('scale', '0.6');
		var _height = org_depth*-40;
		var div = $chart.css('transform','matrix(1,0,0,1,5,'+_height+')');
        // $chart.css('transform',matrix(1,0,0,1,5,-410));
        var currentZoom = 0.6;
        var zoomval = 1;

        $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);
        var my_num = 0;

        // zoom buttons	
        $('#zoomIn').on('click', function() {
            my_num++;
            zoomval = currentZoom += 0.1;
            $chart.css("transform", 'matrix(' + zoomval + ', 0, 0, ' + zoomval + ', 0 ,' + (-410 + (
                my_num) * 85) + ')');
            $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);
        });

        $('#zoomOut').on('click', function() {
            zoomval = currentZoom -= 0.1;
            my_num--;
            $chart.css("transform", 'matrix(' + zoomval + ', 0, 0, ' + zoomval + ', 0 ,' + (-410 + (
                my_num) * 85) + ')');
            $container.scrollLeft(($container[0].scrollWidth - $container.width()) / 2);

        });

    });

	

	
	
<?}?>
