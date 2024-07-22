<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$qa_skin_path = get_skin_path('qa', (G5_IS_MOBILE ? $qaconfig['qa_mobile_skin'] : $qaconfig['qa_skin']));
$qa_skin_url  = get_skin_url('qa', (G5_IS_MOBILE ? $qaconfig['qa_mobile_skin'] : $qaconfig['qa_skin']));

include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');

$qa_skin_path = G5_THEME_PATH.'/skin/qa/basic';
$qa_skin_url = G5_THEME_URL.'/skin/qa/basic';

/* if (G5_IS_MOBILE) {
    // 모바일의 경우 설정을 따르지 않는다.
    include_once('./_head.php');
    echo conv_content($qaconfig['qa_mobile_content_head'], 1);
} else {
    if($qaconfig['qa_include_head'] && is_include_path_check($qaconfig['qa_include_head']))
        @include ($qaconfig['qa_include_head']);
    else
        include ('./_head.php');
    echo conv_content($qaconfig['qa_content_head'], 1);
} */
?>

<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<div class='qaboard'>
    <div class="news_wrap content-box6">
			<h3 class="title">1:1문의</h3>
			<p class="sub_title">문의를 남겨주시면 담당자가 확인후 답변드립니다.<br>(같은 내용은 1회만 올려주세요 ※중복문의시 지연처리)</p>
            

