<?php
	include_once('./_common.php');

	$type = $_POST['type'];

	if($_POST['mb_id']){
		
		// 추천인 후원인
		if($type == 1 || $type == 3){
			$sth = sql_query("select mb_id,mb_level,grade from {$g5['member_table']}  where mb_id like '%".$_POST['mb_id']."%' AND mb_id != 'admin' ");
			$rows = array();
			while($r = mysqli_fetch_assoc($sth)) {
				$rows[] = $r;
			}
			print json_encode($rows);

		// 센터멤버, 센터이름검색 
		}else if($type == 2){
			$sth = sql_query("select mb_id,mb_name,mb_nick,grade,mb_level,mb_center_name from {$g5['member_table']}  where (mb_center_name like '%{$_POST['mb_id']}%' OR mb_id like '%{$_POST['mb_id']}%' ) and center_use ='1' AND mb_id != 'admin'");
			$rows = array();
			while($r = mysqli_fetch_assoc($sth)) {
				$rows[] = $r;
			}
			print json_encode($rows);
		}
	}else{
		print "[]";
	}

?>