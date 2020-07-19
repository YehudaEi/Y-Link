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

$DBConn = new mysqli(DB['host'], DB['username'], DB['password'], DB['dbname']);
mysqli_set_charset($DBConn, "utf8mb4");

require_once('func.php');

