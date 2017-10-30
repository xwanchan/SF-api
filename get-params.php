<?php
header("Access-Control-Allow-Origin: *");  
header("Access-Control-Allow-Origin: *");  
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');  
define('_ROOT', "");
require_once (_ROOT . "class/SFforWebservice.class.php");

$temp = $_POST['data_string'];
$post_data = null;
foreach (explode('&', $temp) as $key=>$item) {
	$temp1 = explode('=', $item);
	$post_data[$temp1[0]] = $temp1[1];
}
$action = $post_data["action"];
unset($post_data["action"]);
switch ($action) {
    case "OrderService":
		$SF = new SFapi();
		$Mode = $post_data["OrderService_Mode"];
		unset($post_data["OrderService_Mode"]);
		if ($Mode == "JSON") {
		    $data = $SF->OrderService($post_data)->Send()->readJSON();
		}else {
		    $data = $SF->OrderService($post_data)->Send()->webView();
		}
		echo $data;
        break;
    case "OrderFilterService":
        $SF = new SFapi();
        $Mode = $post_data["OrderFilterService_Mode"];
        unset($post_data["OrderFilterService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->OrderFilterService($post_data)->Send()->readJSON();
        } else {
            $data = $SF->OrderFilterService($post_data)->Send()->webView();
        }
        echo $data;
        break;
    case "OrderSearchService":
        $search_orderid = $post_data["search_orderid"];
        $SF = new SFapi();
        $Mode = $post_data["OrderSearchService_Mode"];
        unset($post_data["OrderSearchService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->OrderSearchService($search_orderid)->Send()->readJSON();
        }else{
            $data = $SF->OrderSearchService($search_orderid)->Send()->webView();
        }
        echo $data;
        break;
    case "RouteService":
        $route_mailno = $post_data["route_mailno"];
        $SF = new SFapi();
        $Mode = $post_data["RouteService_Mode"];
        unset($post_data["RouteService_Mode"]);
        if ($Mode == "JSON") {
            $data = $SF->RouteService($route_mailno)->Send()->readJSON();
        }else{
            $data = $SF->RouteService($route_mailno)->Send()->webView();
        }
        echo $data;
        break;
        break;

    default:
        break;
}