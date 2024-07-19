<?php
if($member['mb_id'] == 'admin'){
    $menu['menu750'] = array (
        array('750000', 'm3cron 관리', G5_ADMIN_URL.'/m3cron_list.php'),
        array('750100', 'm3cron 설정', G5_ADMIN_URL.'/m3cron_list.php'),
        array('750200', 'm3cron 로그', G5_ADMIN_URL.'/m3cron_log.php')
    );
}else{
    
}
