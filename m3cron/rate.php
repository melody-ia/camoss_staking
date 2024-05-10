<?php
    $conn = include_once('/var/www/html/tripsia/m3cron/dbconfig.php');

    function calc_hana(){
        $url = 'https://quotation-api-cdn.dunamu.com/v1/forex/recent?codes=FRX.KRWUSD';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    $result_hana = calc_hana();
    $datahana = json_decode($result_hana,ture);
    $datahana = $datahana[0];
    $_buying = $datahana['cashBuyingPrice'];
    $_selling = $datahana['cashSellingPrice'];

    $sql = "update g5_shop_default set de_token_buy_price = '{$_buying}', de_token_sell_price = '{$_selling}', de_token_update_at = now()";
    mysqli_query($conn, $sql);
?>