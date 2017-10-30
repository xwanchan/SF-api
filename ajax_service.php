<?php

//define('_ROOT', str_replace("\\", '/', dirname(__FILE__)));

define('_ROOT', "");


require_once (_ROOT . "class/SFforWebservice.class.php");

$action = $_POST["action"];
if (empty($action)) {
    exit();
}

$post_data = $_POST;
$temp = null;
$post_data['pay_method'] = 1;
$post_data['custid'] = '5322059827';
foreach ($post_data as $key=>$item) 
    $temp[] = $key.'='.$item;
$data = array('data_string'=>implode('&', $temp));
$url = 'http://59.188.249.201/buys-hk-mac-sf/get-params.php';
$data = curlToApi($url, $data);

function curlToApi($url, $param) {
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $strPOST = http_build_query($param);

    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    return $sContent;
}

/*switch ($action) {
//    下订单接口
    case "OrderService":
        $post_data = $_POST;
        unset($post_data["action"]);
        $SF = new SFapi();
        $Mode = $post_data["OrderService_Mode"];
        unset($post_data["OrderService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->OrderService($post_data)->Send()->readJSON();
        } else {
            $data = $SF->OrderService($post_data)->Send()->webView();
        }
        //echo $data;
        break;
    case "OrderFilterService":
        $post_data = $_POST;
        unset($post_data["action"]);
        $SF = new SFapi();
        $Mode = $post_data["OrderFilterService_Mode"];
        unset($post_data["OrderFilterService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->OrderFilterService($post_data)->Send()->readJSON();
        } else {
            $data = $SF->OrderFilterService($post_data)->Send()->webView();
        }
        //echo $data;
        break;
    case "OrderSearchService":
        $post_data = $_POST;
        $search_orderid = $post_data["search_orderid"];
        $SF = new SFapi();
        $Mode = $post_data["OrderSearchService_Mode"];
        unset($post_data["OrderSearchService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->OrderSearchService($search_orderid)->Send()->readJSON();
        }else{
            $data = $SF->OrderSearchService($search_orderid)->Send()->webView();
        }
        //echo $data;
        break;
    case "RouteService":
        $post_data = $_POST;
        $route_mailno = $post_data["route_mailno"];
        $SF = new SFapi();
        $Mode = $post_data["RouteService_Mode"];
        unset($post_data["RouteService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->RouteService($route_mailno)->Send()->readJSON();
        }else{
            $data = $SF->RouteService($route_mailno)->Send()->webView();
        }
        //echo $data;
        break;
        break;

    default:
        break;
}
//echo $data;*/

if ($data != null){
	//print_r($data);
	
	include_once('../../../config.php');
	include_once(ROOT_DIR.'/classes/email_content.php');
	include_once(ROOT_DIR.'/classes/phpmailer/PHPMailerAutoload.php');
	
	$response['invoice_id'] = $_POST['orderid'];
	$response['return_ary'] = $data;
	
	$output = json_decode($data, true);
	
	//print_r($output);	
	
	$response['status'] = $output['data'][0]['childs'][0]['childs'][0]['text']; //ERR or OK
	
	if ($response['status'] == 'OK'){
		
		$response['content'] = json_encode($output['data'][0]['childs'][1]['childs'][0]['attr']);
		$response['waybill_id'] = $output['data'][0]['childs'][1]['childs'][0]['attr']['mailno'];
		$response['invoice_id'] = $output['data'][0]['childs'][1]['childs'][0]['attr']['orderid'];
		$response['date_add'] = date("Y-m-d H:i:s");
		$rs_transaction = $db->misc('update '.TABLE_PREFIX.'transaction set waybill_id = '.$db->mySQLSafe($response['waybill_id']).' where invoice_id="'.$response['invoice_id'].'"');
		
		//find out the transaction 
		$transaction = $db->select("SELECT * FROM ".TABLE_PREFIX."transaction WHERE invoice_id=".$db->mySQLSafe($response['invoice_id']));
		if($transaction){
			//add transaction log
			$transaction_id = $transaction[0]['id'];
			unset($transaction[0]['id'], $transaction[0]['date_add']);
			$transaction_log_update = $db->insert(TABLE_PREFIX."transaction_log", $transaction[0]);
			
			$log_data['action'] = "Submit to SF";
			$log_data['note'] = "";
			$log_data['comment'] = "";
			order_log($log_data, $transaction_id, $_SESSION[SERVER_NAME]['user_id']);
			unset($log_data);
		}
		if($transaction[0]['check_code'] && $sf_function['status']){
			send_order_email($transaction[0]['check_code'], '', '', 'delivery');
		}
	}elseif ($response['status'] == 'ERR'){
		$response['code'] = $output['data'][0]['childs'][1]['attr']['code'];
		$response['msg']  = $output['data'][0]['childs'][1]['childs'][0]['text'];
	
	}
	
	
	
	
	$rs = $db->insert(TABLE_PREFIX.'waybill', $response);
	
	
	
	echo json_encode($response);
	//print_r($response);
	
	
//}else{
//	$response['status'] = 'ERR';
//	echo json_encode($response);
}
?>
