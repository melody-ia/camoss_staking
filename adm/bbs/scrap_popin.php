<?php
include_once('./_common.php');

include_once(G5_PATH.'/head.sub.php');

if(!$is_admin)
    alert('접근 권한이 없습니다.', G5_URL);

echo <<<HEREDOC
<script>
    if (window.name != 'win_scrap') {
        alert('올바른 방법으로 사용해 주십시오.');
        window.close();
    }
</script>
HEREDOC;

if ($write['wr_is_comment'])
    alert_close('코멘트는 스크랩 할 수 없습니다.');

$sql = " select count(*) as cnt from {$g5['scrap_table']}
            where mb_id = '{$member['mb_id']}'
            and bo_table = '$bo_table'
            and wr_id = '$wr_id' ";
$row = sql_fetch($sql);
if ($row['cnt']) {

    $back_url = get_pretty_url($bo_table, $wr_id);

    echo <<<HEREDOC
    <script>
    if (confirm('이미 스크랩하신 글 입니다.\\n\\n지금 스크랩을 확인하시겠습니까?'))
        document.location.href = './scrap.php';
    else
        window.close();
    </script>
    <noscript>
    <p>이미 스크랩하신 글 입니다.</p>
    <a href="./scrap.php">스크랩 확인하기</a>
    <a href="{$back_url}">돌아가기</a>
    </noscript>
HEREDOC;
    exit;
}

include_once($member_skin_path.'/scrap_popin.skin.php');

include_once(G5_PATH.'/tail.sub.php');