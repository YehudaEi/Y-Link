<?php

/**
 * The init file
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */


error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache");
header("Cache-Control: no-store");
date_default_timezone_set('Asia/Jerusalem');

require_once('config.php');

mysqli_report(MYSQLI_REPORT_OFF);
$DBConn = new mysqli(DB['host'], DB['username'], DB['password'], DB['dbname']);
if($DBConn->connect_errno){
    http_response_code(500);
    if(basename($_SERVER['SCRIPT_NAME'] ?? "") == "api.php"){
        echo json_encode(array("ok" => false, "error" => array("code" => 500, "message" => "Server Error! please try again later")));
    }
    else{
        header("Content-Type: text/html; charset=utf-8");
        include(__DIR__ . '/../error-pages/500.html');
    }
    die();
}
$DBConn->set_charset("utf8mb4");

require_once("Parser-PHP-2.1.1/bootstrap.php");
require_once('func.php');

