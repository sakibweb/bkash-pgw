<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define configuration variables
    $createURL = "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/create";
    $executeURL = "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/execute/";
    $tokenURL = "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/token/grant";
    $scriptURL = "https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js";
    $proxy = "";
    $app_key = "";
    $app_secret = "";
    $username = "";
    $password = "";
    $intent = "sale";

    // Function to get bKash token
    function bkash_Get_Token() {
        global $tokenURL, $app_key, $app_secret, $username, $password, $proxy;

        $post_token = array(
            'app_key' => $app_key,
            'app_secret' => $app_secret
        );    

        $url = curl_init($tokenURL);
        $posttoken = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            'password:'.$password,
            'username:'.$username
        );                    
        
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        if (!empty($proxy)) {
            curl_setopt($url, CURLOPT_PROXY, $proxy);
        }

        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);    
    }

    // Generate token and store it in session and config file
    $request_token = bkash_Get_Token();
    $idtoken = $request_token['id_token'];
    $_SESSION['token'] = $idtoken;

    // Create payment
    if(isset($_GET['createpayment'])) {
        global $createURL, $app_key, $proxy, $intent;

        $amount = $_GET['amount'];
        $invoice = $_GET['invoice'];

        $createpaybody = array('amount' => $amount, 'currency' => 'BDT', 'merchantInvoiceNumber' => $invoice, 'intent' => $intent);   
        $url = curl_init($createURL);
        $createpaybodyx = json_encode($createpaybody);
        $header = array(
            'Content-Type:application/json',
            'authorization:'.$idtoken,
            'x-app-key:'.$app_key
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $createpaybodyx);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        if (!empty($proxy)) {
            curl_setopt($url, CURLOPT_PROXY, $proxy);
        }

        $resultdata = curl_exec($url);
        curl_close($url);
        echo $resultdata;
        exit;
    }

    // Execute payment
    if(isset($_GET['executepayment'])) {
        global $executeURL, $app_key, $proxy;

        $paymentID = $_GET['paymentID'];
        $url = curl_init($executeURL . $paymentID);
        $header = array(
            'Content-Type:application/json',
            'authorization:'.$idtoken,
            'x-app-key:'.$app_key
        );    
        
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        if (!empty($proxy)) {
            curl_setopt($url, CURLOPT_PROXY, $proxy);
        }

        $resultdatax = curl_exec($url);
        curl_close($url);
        echo $resultdatax;
        exit;
    }
}
?>
