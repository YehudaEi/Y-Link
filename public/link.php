<?php

/**
 * router file
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

include("include/init.php");

header("Cache-Control: no-cache");
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: no-store");

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? "/", PHP_URL_PATH) ?: "/";
$sitePath = parse_url(SITE_URL, PHP_URL_PATH) ?? "";
if($sitePath !== "" && strpos($requestPath, $sitePath) === 0)
    $requestPath = substr($requestPath, strlen($sitePath));

$getData = false;
if(substr($requestPath, -1) == "+"){
    $getData = true;
    $requestPath = substr($requestPath, 0, -1);
}

$uri = urldecode(substr($requestPath, 1));

$longLink = false;
if(preg_match(PATH_REGEX, $uri)){
    $longLink = getLongLink($uri);
}
if(!empty($longLink)){
    if($getData){
        include('stats.php');
    }
    else{
        addVisitor($uri);
        header("Location: " . $longLink);
        echo "error in moving you to <a href=\"".htmlspecialchars($longLink)."\" rel=\"noreferrer nofollow\">this link</a>. You can click <a href=\"".htmlspecialchars($longLink)."\" rel=\"noreferrer nofollow\">here</a>";
    }
}else{
    http_response_code(404);
    include 'error-pages/404.html';
}

$DBConn->close();

?>
