<?php

/**
 * The api
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

require_once('include/init.php');

$res = array();

$validParams = array(
    "method" => array(
        "type" => "string",
        "valid length" => "4...6",
        "description" => "the method (e.g. create, info, help...)",
    ),
    "link" => array(
        "type" => "url",
        "description" => "the long link",
    ),
    "password" => array(
        "type" => "string",
        "valid length" => "4...30",
        "description" => "Creator verification",
    ),
    "path" => array(
        "type" => "string",
        "valid length" => "4...30",
        "description" => "shortened link path: " . SITE_URL . "/{path}",
    ),
    "shorten_link" => array(
        "type" => "url",
        "valid length" => (strlen(SITE_URL) + 4) . "..." . (strlen(SITE_URL) + 30),
        "description" => SITE_URL . " shortened link",
    ),
    "start_date" => array(
        "type" => "date",
        "valid length" => "19",
        "description" => "start date for stats (in format: yyyy-mm-dd HH:mm:ss)",
    ),
    "end_date" => array(
        "type" => "date",
        "valid length" => "19",
        "description" => "end date for stats (in format: yyyy-mm-dd HH:mm:ss)",
    ),
);

$methods = array(
    'create' => array(
        "description" => "Create new shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "link" => $validParams['link']
        )
    ),
    'info' => array(
        "description" => "Get info of shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "shorten_link" => $validParams['shorten_link']
        )
    ),
    'stats' => array(
        "description" => "Get stats of shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "shorten_link" => $validParams['shorten_link']
        ),
        "optional_paramaters" => array(
            "start_date" => $validParams['start_date'],
            "end_date" => $validParams['end_date']
        )
    ),
    'raw_stats' => array(
        "description" => "Get raw stats of shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "shorten_link" => $validParams['shorten_link']
        ),
        "optional_paramaters" => array(
            "start_date" => $validParams['start_date'],
            "end_date" => $validParams['end_date']
        )
    ),
    'custom' => array(
        "description" => "Create new custom shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "link" => $validParams['link'],
            "path" => $validParams['path']
        )
    ),
    'edit' => array(
        "description" => "Edit link destination",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "shorten_link" => $validParams['shorten_link'],
            "link" => $validParams['link']
        )
    ),
    'help' => array(
        "description" => "Receive help",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method']
        )
    )
);

if(isset($_GET['create_readme'])){
    $i = 1; $j = 1; $k = 1;
    foreach ($methods as $methodName => $method){
        echo $i . ". **" . $methodName . "**\n";
        echo "\t 1. Description: `" . $method['description'] . "`\n";
        echo "\t 2. HTTP Method: `" . strtoupper($method['http_method']) . "`\n";
        echo "\t 3. Paramaters: \n";
        foreach ($method['paramaters'] as $parameterName => $params){
            echo "\t\t" . $j . ". `" . $parameterName . "`:\n";
            foreach ($params as $name => $description){
                echo "\t\t\t" . $k . ". " . $name . ": `" . $description . "`\n";
                
                $k++;
            }
            
            $k = 1;
            $j++;
        }
        
        if(isset($method['optional_paramaters'])){
            echo "\t 4. Optional Paramaters: \n";
            $j = 1;
            foreach ($method['optional_paramaters'] as $parameterName => $params){
                echo "\t\t" . $j . ". `" . $parameterName . "`:\n";
                foreach ($params as $name => $description){
                    echo "\t\t\t" . $k . ". " . $name . ": `" . $description . "`\n";
                    
                    $k++;
                }
                
                $k = 1;
                $j++;
            }
        }
        
        echo "\n";
        $j = 1;
        $i++;
    }
    
    die();
}

/**
 * build error response
 * 
 * @param int $code error code
 * @param string $message error message
 * @return array the response
 */
function errorResponse($code, $message){
    return array(
        "ok" => false,
        "error" => array(
            "code" => $code,
            "message" => $message
        )
    );
}

$method = (isset($_POST['method']) && is_string($_POST['method'])) ? $_POST['method'] : null;

if($method === null || !isset($methods[$method])){
    $res = errorResponse(404, 'Method not found. try send POST request "method=help"');
    $res["error"]['docs'] = 'https://github.com/YehudaEi/Y-Link';
}
else{
    foreach($methods[$method]['paramaters'] as $name => $tmp){
        if(!isset($_POST[$name]) || !is_string($_POST[$name]) || trim($_POST[$name]) === ""){
            $res = errorResponse(400, "Bad Request: \"{$name}\" is empty");
            break;
        }

        if($name != "method" && !call_user_func("valid" . ucfirst($name), $_POST[$name])){
            $res = errorResponse(400, "Bad Request: \"{$name}\" is invalid");
            break;
        }
    }
    
    if(count($res) == 0){
        foreach(($methods[$method]['optional_paramaters'] ?? array()) as $name => $tmp){
            if(isset($_POST[$name]) && !empty($_POST[$name]) && !call_user_func("valid" . ucfirst($name), $_POST[$name])){
                $res = errorResponse(400, "Bad Request: \"{$name}\" is invalid");
                break;
            }
        }
    }

    if(count($res) == 0 && isset($methods[$method]['paramaters']['shorten_link']) && !checkLinkPassword($_POST['shorten_link'], $_POST['password'])){
        $res = errorResponse(403, 'Forbidden');
    }

    if(count($res) == 0){
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;

        switch($method){
            case "create":
                $path = createLink($_POST['link'], $_POST['password']);
                if($path !== false){
                    $res['ok'] = true;
                    $res['res']['password'] = $_POST['password'];
                    $res['res']['link'] = SITE_URL . '/' . $path;
                }
                else{
                    $res = errorResponse(500, 'Server Error! please try again later');
                }
                break;

            case "info":
                $res['ok'] = true;
                $res['res']['long_link'] = getLongLink($_POST['shorten_link']);
                $res['res']['count_clicks'] = countClicks($_POST['shorten_link']);
                break;

            case "stats":
                $res['ok'] = true;
                $res['res']['count_clicks'] = countClicks($_POST['shorten_link'], $startDate, $endDate);
                $res['res']['stats'] = getStatsOfLink($_POST['shorten_link'], $startDate, $endDate);
                break;

            case "raw_stats":
                $res['ok'] = true;
                $res['res']['raw_data'] = getAllClickOfLink($_POST['shorten_link'], $startDate, $endDate);
                break;

            case "custom":
                $path = trim($_POST['path']);
                if(linkExistByPath($path) || reservedPath($path)){
                    $res = errorResponse(400, 'Path already exist');
                }
                elseif(createCustomLink($_POST['link'], $path, $_POST['password']) !== false){
                    $res['ok'] = true;
                    $res['res']['password'] = $_POST['password'];
                    $res['res']['link'] = SITE_URL . '/' . $path;
                }
                else{
                    $res = errorResponse(500, 'Server Error! please try again later');
                }
                break;

            case "edit":
                if(editLongLink($_POST['link'], $_POST['shorten_link'])){
                    $res['ok'] = true;
                    $res['res']['password'] = $_POST['password'];
                    $res['res']['link'] = $_POST['shorten_link'];
                }
                else{
                    $res = errorResponse(500, 'Server Error! please try again later');
                }
                break;

            case "help":
                $res['ok'] = true;
                $res['owner']['name'] = "Yehuda Eisenberg";
                $res['owner']['mail'] = "yehuda.telegram@gmail.com";
                $res['owner']['support'] = "links@".SITE_DOMAIN;
                $res['owner']['GitHub'] = "https://github.com/YehudaEi/Y-Link";
                $res['owner']['Telegram'] = "@YehudaEisenberg";
                $res['valid_methods'] = $methods;
                $res['valid_paramaters'] = $validParams;
                break;

            default:
                $res = errorResponse(500, 'Server Error! please try again later');
        }
    }
}

echo json_encode($res);

$DBConn->close();
