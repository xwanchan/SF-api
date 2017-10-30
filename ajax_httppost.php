<?php

define('_ROOT', str_replace("\\", '/', dirname(__FILE__)));

require_once (_ROOT . "/class/SFforHttpPost.class.php");

$action = $_POST["action"];
if (empty($action)) {
    exit();
}

switch ($action) {
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
        echo $data;
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
        echo $data;
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
        echo $data;
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
        echo $data;
        break;
        break;

    default:
        break;
}
?>
