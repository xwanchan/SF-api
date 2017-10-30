<?php

define('_ROOT', str_replace("\\", '/', dirname(__FILE__)));
require_once (_ROOT . "/class/SFprinter.class.php");
require_once (_ROOT . "/class/Pclzip.class.php");
$action = $_POST["action"];
if (empty($action)) {
    exit();
}

switch ($action) {
//    下订单接口
    case "Printer":
        $post_data = $_POST;
        unset($post_data["action"]);

        $pic = "save/old_no" . time() . ".png";
        $olderpic = _ROOT . "/" . $pic;

        $SF = new SFprinter();
        $data = array(
            "express_type" => $post_data["express_type"],
            "mailno" => $post_data["mailno"],
            "orderid" => $post_data["orderid"],
            "j_company" => $post_data["j_company"],
            "j_contact" => $post_data["j_contact"],
            "j_tel" => $post_data["j_tel"],
            "j_province" => $post_data["j_province"],
            "j_city" => $post_data["j_city"],
            "j_qu" => $post_data["j_qu"],
            "j_address" => $post_data["j_address"],
            "j_number" => $post_data["j_number"],
            "d_company" => $post_data["d_company"],
            "d_contact" => $post_data["d_contact"],
            "d_tel" => $post_data["d_tel"],
            "d_province" => $post_data["d_province"],
            "d_city" => $post_data["d_city"],
            "d_qu" => $post_data["d_qu"],
            "d_address" => $post_data["d_address"],
            "d_number" => $post_data["d_number"],
            "pay_method" => $post_data["pay_method"],
            "custid" => $post_data["custid"],
            "daishou" => $post_data["daishou"], //代收款项
            "remark" => $post_data["remark"],
            "things" => $post_data["things"]
        );
        $SF->SFdata($data)->SFprint($olderpic);
        $zipurl = "save/order" . time() . ".zip";
        $archive = new PclZip($zipurl);
//        $v_list = $archive->create($olderpic, PCLZIP_OPT_REMOVE_PATH, '', PCLZIP_OPT_ADD_PATH, '');
        $v_list = $archive->create($pic, PCLZIP_OPT_REMOVE_PATH, 'save', PCLZIP_OPT_ADD_PATH, 'PrintOrder');
        echo $zipurl;
        die;

        break;
    default:
        break;
}
?>
