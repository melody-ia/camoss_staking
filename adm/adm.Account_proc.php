<?
include_once('./_common.php');

if($_POST['type'] == "update"){
    for($i = 0 ; $i < count($_POST['no']); $i ++){
        $no = $_POST['no'][$i];
        $sequence = $_POST['sequence'][$i];
        $category = $_POST['category'][$i];
        $account_name = $_POST['account_name'][$i];
        $bank_name = $_POST['bank_name'][$i];
        $bank_account = $_POST['bank_account'][$i];
        $bank_account_name = $_POST['bank_account_name'][$i];

        $used = $_POST['used'][$i] ? $_POST['used'][$i] : 0;
        $category_no = $category == "입금" ? 1 : 2;
    
        $update = 
        "update wallet_account set 
        category_no = {$category_no},
        sequence = {$sequence},
        category = '{$category}',
        account_name = '{$account_name}',
        bank_name = '{$bank_name}',
        bank_account = '{$bank_account}',
        bank_account_name = '{$bank_account_name}',
        used = {$used},
        create_dt = now()
        where no = $no";
        $result = sql_query($update);
    }
    if( $result){
        alert('설정이 저장되었습니다.');
        goto_url('./adm.Account_Manage.php');
    }
}else{
    $sql = "insert into wallet_account(`used`,`create_dt`) values(0,now())";
    $result = sql_query($sql);
    $code = '400';
    if($result){
        $code = '200';
    }
    echo json_encode(array('code'=>$code));
}


?>