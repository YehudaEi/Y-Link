<?php

/**
 * The api
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    MIT
 * @version    AGPL-3.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

require_once('include/init.php');

$res = array();

$validParams = array(
    "method" => array(
        "type" => "string",
        "validLength" => "4...6",
        "description" => "the method (create, info, custom, edit, help)",
    ),
    "link" => array(
        "type" => "url",
        "description" => "the long link",
    ),
    "password" => array(
        "type" => "string",
        "validLength" => "4...30",
        "description" => "Creator verification",
    ),
    "path" => array(
        "type" => "string",
        "validLength" => "4...30",
        "description" => "shortened link path: " . SITE_URL . "/{path}",
    ),
    "shorten_link" => array(
        "type" => "url",
        "validLength" => (strlen(SITE_URL) + 4) . "..." . (strlen(SITE_URL) + 30),
        "description" => SITE_URL . " shortened link",
    )
);

$methods = array(
    'create' => array(
        "description" => "to create new shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "link" => $validParams['link']
        )
    ),
    'info' => array(
        "description" => "to get info of shorten link",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method'],
            "password" => $validParams['password'],
            "shorten_link" => $validParams['shorten_link']
        )
    ),
    'custom' => array(
        "description" => "to create new custom shorten link",
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
        "description" => "receive help",
        "http_method" => "post",
        "paramaters" => array(
            "method" => $validParams['method']
        )
    )
);

if(!isset($_POST) || count($_POST) == 0 || !isset($methods[$_POST['method']])){
    $res['ok'] = false;
    $res["error"]['code'] = 404;
    $res["error"]['message'] = 'Method not found. try send post request method=help';
    $res["error"]['docs'] = 'https://github.com/YehudaEi/Y-Link';
}
else{
    $params = $methods[$_POST['method']]['paramaters'];
    foreach($params as $name => $tmp){
        if(!isset($_POST[$name]) || empty($_POST[$name])){
            $res['ok'] = false;
            $res["error"]['code'] = 400;
            $res["error"]['message'] = "Bad Request: \"{$name}\" is empty";
            break;
        }

        if($name != "method" && !call_user_func("valid" . ucfirst($name), $_POST[$name])){
            $res['ok'] = false;
            $res["error"]['code'] = 400;
            $res["error"]['message'] = "Bad Request: \"{$name}\" is invalid";
            break;
        }
    }

    if(count($res) == 0){
        if($_POST['method'] == "create"){
            $path = createLink($_POST['link'], $_POST['password']);
            if(is_string($path)){
                $res['ok'] = true;
                $res['res']['password'] = $_POST['password'];
                $res['res']['link'] = SITE_URL . '/' . $path;
            }
            else{
                $res['ok'] = false;
                $res["error"]['code'] = 500;
                $res["error"]['message'] = 'Server Error! description: ' . $path;
            }
        }
        elseif($_POST['method'] == "info"){
            if(getLinkPass($_POST['shorten_link']) == $_POST['password']){
                $res['ok'] = true;
                $res['res']['long_link'] = getLongLink($_POST['shorten_link']);
                $res['res']['count_clicks'] = countClicks($_POST['shorten_link']);
            }
            else{
                $res['ok'] = false;
                $res["error"]['code'] = 403;
                $res["error"]['message'] = 'Forbidden';
            }
        }
        elseif($_POST['method'] == "custom"){
            if(!linkExistByPath($_POST['path']) && $_POST['path'] != "mainTable"){
                $success = createCustomLink($_POST['link'], $_POST['path'], $_POST['password']);
                if($success == true){
                    $res['ok'] = true;
                    $res['res']['password'] = $_POST['password'];
                    $res['res']['link'] = SITE_URL . '/' . $_POST['path'];
                }
                else{
                    $res['ok'] = false;
                    $res["error"]['code'] = 500;
                    $res["error"]['message'] = 'Server Error! description: ' . $success;
                }
            }
            else{
                $res['ok'] = false;
                $res["error"]['code'] = 400;
                $res["error"]['message'] = 'Path already exist';
            }
        }
        elseif($_POST['method'] == "edit"){
            if(getLinkPass($_POST['shorten_link']) == $_POST['password']){
                $success = editLongLink($_POST['link'], $_POST['shorten_link']);
                if($success == true){
                    $res['ok'] = true;
                    $res['res']['password'] = $_POST['password'];
                    $res['res']['link'] = $_POST['shorten_link'];
                }
                else{
                    $res['ok'] = false;
                    $res["error"]['code'] = 500;
                    $res["error"]['message'] = 'Server Error! description: ' . $success;
                }
            }
            else{
                $res['ok'] = false;
                $res["error"]['code'] = 403;
                $res["error"]['message'] = 'Forbidden';
            }
        }
        elseif($_POST['method'] == "help"){
            $res['owner']['name'] = "Yehuda Eisenberg";
            $res['owner']['mail'] = "yehuda.telegram@gmail.com";
            $res['owner']['support'] = "links@".SITE_DOMAIN;
            $res['owner']['GitHub'] = "https://github.com/YehudaEi/Y-Link";
            $res['owner']['Telegram'] = "@YehudaEisenberg";
            
            foreach($methods as $name => $method){
                $res['valid_methods'][$name] = $method;
            }

            foreach($validParams as $name => $param){
                $res['valid_paramaters'][$name] = $param;
            }
        }
        else {
            $res['ok'] = false;
            $res["error"]['code'] = 500;
            $res["error"]['message'] = 'Server Error! please try again later';
        }
    }
}

echo json_encode($res, true);

$DBConn->close();
