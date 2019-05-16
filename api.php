<?php
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache");
header("Cache-Control: no-store");
date_default_timezone_set('Asia/Jerusalem');

function rnd($len = 6) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1; 
    for ($i = 0; $i < $len; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); 
}
function LinkTool($link, $pass, $mode, $helperArg = null){ 
    
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "link"; 
     
    $tablename = "`Link`"; 
    // Create connection 
    $conn = new mysqli($servername, $username, $password, $dbname); 
    // Check connection 
    if ($conn->connect_error) {die(json_encode(array('ok' => false, "error" => "db error! (connection)"), TRUE));}  

    if($mode == "rand"){
        $randTemp = rnd();
        $bool = 0;
        while(true){
            $sql = "SELECT * FROM $tablename"; 
            $temp = $conn->query($sql); 
            while ($row = $temp->fetch_assoc())
                if($row['id'] && $randTemp == $row['id']) {
                    $randTemp = rnd();
                    $bool = 1;
                }
            if($bool == 1)
                $randTemp = rnd();
            else
                break;
        }
        return $randTemp;
    }
    elseif($mode == "get_click" && parse_url($link, PHP_URL_HOST) == "y-link.ml"){
        $linkId = substr(parse_url($link)['path'],1);
        if(preg_match("/^[a-zA-Z0-9]+$/",$linkId)){
            $sql = "SELECT `password` FROM `Link` WHERE `id` = '".$linkId."'"; 
            $temp = $conn->query($sql);
            $LinkPass =  $temp->fetch_assoc()["password"];
            if($LinkPass == $pass){
                $sql = "SELECT `counter` FROM `Link` WHERE `id` = '".$linkId."'"; 
                $tmp = $conn->query($sql);
                $conn->close();
                if($tmp->num_rows > 0){
                    $res = $tmp->fetch_assoc();
                    return $res['counter'];
                }
                else
                    die(json_encode(array('ok' => false, "error" => "the link not exist"), TRUE));
            }
            die(json_encode(array('ok' => false, "error" => "Bad Request: Worng Password"), TRUE));
        }
        die(json_encode(array('ok' => false, "error" => "db error! (invalid link)"), TRUE));
    }
    elseif($mode == "link_exist"){
        $linkId = substr(parse_url($link)['path'],1);
        $sql = "SELECT `password` FROM `Link` WHERE `id` = '".$linkId."'"; 
        $temp = $conn->query($sql);
        if($temp->num_rows > 0){
            $LinkPass = $temp->fetch_assoc()["password"];
            if($LinkPass == $pass){
                return "creator";
            }
            else
                return "not creator";
        }
        return "not exist";
    }
    elseif($mode == "update_link" || parse_url($helperArg, PHP_URL_HOST) == "y-link.ml"){
        $linkId = substr(parse_url($helperArg)['path'],1);
        if(preg_match("/^[a-zA-Z0-9]+$/",$linkId)){
            $sql = "SELECT `password` FROM `Link` WHERE `id` = '".$linkId."'"; 
            $temp = $conn->query($sql);
            $LinkPass =  $temp->fetch_assoc()["password"];
            if($LinkPass == $pass){
                $sql = "UPDATE $tablename SET `link` = '".$link."' WHERE `link`.`id` = '".$linkId."';"; 
                if ($conn->query($sql) === TRUE){return "http://y-link.ml/".$linkId;} else{die(json_encode(array('ok' => false, "error" => "db error! (update)"), TRUE));}
            }
            else
                die(json_encode(array('ok' => false, "error" => "Bad Request: Worng Password"), TRUE));
        }
        else
            die(json_encode(array('ok' => false, "error" => "db error! (invalid link)"), TRUE));
    }
    elseif($mode == "create_custom"){
        $sql = "SELECT * FROM $tablename"; 
        $temp = $conn->query($sql); 
        while ($row = $temp->fetch_assoc()){ 
            if($row['link'] && strtolower($link) == strtolower(urldecode($row['link'])) && $row['password'] == $pass) { 
                $conn->close(); 
                return "http://y-link.ml/".$row['id']; 
            } 
        }
        $sql = "INSERT INTO $tablename (`link`, `id`, `counter`, `platform`, `password`) VALUES ('".urlencode(urldecode($link))."','".$helperArg."', '0' , 'ml', ".$pass."');"; 
        if ($conn->query($sql) === TRUE){} else{die(json_encode(array('ok' => false, "error" => "db error! (insert)"), TRUE));}
        $conn->close(); 
        return "http://y-link.ml/".$helperArg;
    }
    elseif($mode == "create"){
        $sql = "SELECT * FROM $tablename"; 
        $temp = $conn->query($sql); 
        while ($row = $temp->fetch_assoc()){ 
            if($row['link'] && strtolower($link) == strtolower(urldecode($row['link'])) && $row['password'] == $pass) { 
                $conn->close(); 
                return "http://y-link.ml/".$row['id']; 
            } 
        }
        $rand = LinkTool(0, $link, "rand");
        $sql = "INSERT INTO $tablename (`link`, `id`, `counter`, `platform`, `password`) VALUES ('".urlencode(urldecode($link))."','".$rand."', '0' , 'ml' ,'".$pass."');"; 
        if ($conn->query($sql) === TRUE){} else{die(json_encode(array('ok' => false, "error" => "db error! (insert)"), TRUE));}
        $conn->close(); 
        return "http://y-link.ml/".$rand;
    }
}

$res = array();
$methods = array('create', 'get_click', 'custom', 'edit_link', 'help');
$tokens = array("eliko13542");

if(!isset($_GET) || count($_GET) == 0 || !in_array($_GET['method'], $methods)){
    $res['ok'] = false;
    $res["error"] = 'Method not found. see ?method=help';
}
elseif(!isset($_GET['password']) && $_GET['method'] != "help"){
    $res['ok'] = false;
    $res["error"] = 'Bad Request: \'password\' is empty';
}
elseif((isset($_GET['password']) && (!preg_match("/^[a-zA-Z0-9]+$/",$_GET['password']) || strlen($_GET['password']) == 0 || strlen($_GET['password']) > 20)) && $_GET['method'] != "help"){
    $res['ok'] = false;
    $res["error"] = 'Bad Request: \'password\' is invalid';
}

else{
    switch ($_GET['method']) {
        case 'create':{
            if(!isset($_GET['link'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is empty';
            }
            elseif(!filter_var($_GET['link'], FILTER_VALIDATE_URL) && !filter_var("http://".$_GET['link'], FILTER_VALIDATE_URL)){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is invalid';
            }
            elseif(filter_var($_GET['link'], FILTER_VALIDATE_URL)){
                $link = LinkTool($_GET['link'], $_GET['password'], "create");
                if($link){
                    $res['ok'] = true;
                    $res['res']['password'] = $_GET['password'];
                    $res['res']['link'] = $link;
                }
                else{
                    $res['ok'] = false;
                    $res["error"] = 'Unknown Error!';
                }
            }
            elseif(filter_var("http://".$_GET['link'], FILTER_VALIDATE_URL)){
                $link = LinkTool("http://".$_GET['link'], $_GET['password'], "create");
                if($link){
                    $res['ok'] = true;
                    $res['res']['password'] = $_GET['password'];
                    $res['res']['link'] = $link;
                }
                else{
                    $res['ok'] = false;
                    $res["error"] = 'Unknown Error!';
                }
            }
            else{
                $res['ok'] = false;
                $res["error"] = 'Unknown Error!';
            }
            }break;
        case 'get_click':{
            if(!isset($_GET['link'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is empty';
            }
            elseif(!filter_var($_GET['link'], FILTER_VALIDATE_URL)){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is invalid';
            }
            elseif(parse_url($_GET['link'], PHP_URL_HOST) == "y-link.ml"){
                $tmp = LinkTool($_GET['link'], $_GET['password'], "link_exist");
                if($tmp == "not exist"){
                    $res['ok'] = false;
                    $res["error"] = 'Bad Request: \'link\' not exist';
                }
                else{
                    $res['ok'] = true;
                    $res['res']['password'] = $_GET['password'];
                    $res['res']['clicks'] = LinkTool($_GET['link'], $_GET['password'], "get_click");
                }
            }
            else{
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is invalid';
            }
            }break;
        case 'edit_link':{
            if(!isset($_GET['link'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is empty';
            }
            elseif(!isset($_GET['shorten_link'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'shorten_link\' is empty';
            }
            elseif(!filter_var($_GET['link'], FILTER_VALIDATE_URL)){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is invalid';
            }
            elseif(!filter_var($_GET['shorten_link'], FILTER_VALIDATE_URL)){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'shorten_link\' is invalid';
            }
            elseif(parse_url($_GET['shorten_link'], PHP_URL_HOST) == "y-link.ml"){
                $tmp = LinkTool($_GET['shorten_link'], $_GET['password'], "link_exist");
                if($tmp == "not exist"){
                    $res['ok'] = false;
                    $res["error"] = 'Bad Request: \'shorten_link\' not exist';
                }
                elseif($tmp == "not creator"){
                    $res['ok'] = false;
                    $res["error"] = 'Bad Request: Worng Password';
                }
                else{
                    $res['ok'] = true;
                    $res['res']['password'] = $_GET['password'];
                    $res['res']['new_link'] = $_GET['link'];
                    $res['res']['link'] = LinkTool($_GET['link'], $_GET['password'], "update_link", $_GET['shorten_link']);
                }
            }
            else{
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' or \'shorten_link\' is invalid';
            }
            }break;
        case 'help':{
            $res['valid_methods']['create']['description']              = "For creating new links";
            $res['valid_methods']['create']['param']['method']          = "create";
            $res['valid_methods']['create']['param']['password']        = "creator pass - to get num of clicks / edit the link";
            $res['valid_methods']['create']['param']['link']            = "the link";
            
            $res['valid_methods']['get_click']['description']           = "For get num of clicks";
            $res['valid_methods']['get_click']['param']['method']       = "get_click";
            $res['valid_methods']['get_click']['param']['password']     = "Creator verification";
            $res['valid_methods']['get_click']['param']['shorten_link'] = "the shorten link";
            
            $res['valid_methods']['edit_link']['description']           = "Edit link destination";
            $res['valid_methods']['edit_link']['param']['method']       = "get_click";
            $res['valid_methods']['edit_link']['param']['password']     = "Creator verification";
            $res['valid_methods']['edit_link']['param']['shorten_link'] = "the shorten link";
            $res['valid_methods']['edit_link']['param']['link']         = "the new link";

            $res['valid_methods']['custom']['description']              = "custom shorten link";
            $res['valid_methods']['custom']['param']['method']          = "custom";
            $res['valid_methods']['custom']['param']['password']        = "Creator verification";
            $res['valid_methods']['custom']['param']['token']           = "token..";
            $res['valid_methods']['custom']['param']['path']            = "custom path link (e.g. y-link.ml/path)";
            $res['valid_methods']['custom']['param']['link']            = "the link";

            $res['valid_methods']['help']['description']                = "get help";
            $res['valid_methods']['help']['param']['method']            = "help";

            $res['variables']['method']['type']                         = "string";
            $res['variables']['method']['description']                  = "The method..";
            $res['variables']['password']['type']                       = "string (Up to 20)";
            $res['variables']['password']['description']                = "creator pass - to get num of clicks / edit the link";
            $res['variables']['link']['type']                           = "valid link (string)";
            $res['variables']['link']['description']                    = "The Link...";
            $res['variables']['path']['type']                           = "string (Up to 20)";
            $res['variables']['path']['description']                    = "shorten link path: y-link.ml/path";
            $res['variables']['shorten_link']['type']                   = "y-link.ml link (string)";
            $res['variables']['shorten_link']['description']            = "api output shorten link";
            }break;
        case 'custom':{
            if(!isset($_GET['token'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'token\' is empty';
            }
            elseif(!isset($_GET['link'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is empty';
            }
            elseif(!isset($_GET['path'])){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'path\' is empty';
            }
            elseif(!in_array($_GET['token'], $tokens)){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'token\' is invalid';
            }
            elseif(!filter_var($_GET['link'], FILTER_VALIDATE_URL) && !filter_var("http://".$_GET['link'], FILTER_VALIDATE_URL)){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'link\' is invalid';
            }
            elseif(!preg_match("/^[a-zA-Z0-9]+$/",$_GET['path']) || strlen($_GET['path']) > 30){
                $res['ok'] = false;
                $res["error"] = 'Bad Request: \'path\' is invalid';
            }
            elseif(filter_var($_GET['link'], FILTER_VALIDATE_URL)){
                $link = LinkTool($_GET['link'], $_GET['password'], "create_custom", $_GET['path']);
                if($link){
                    $res['ok'] = true;
                    $res['res']['password'] = $_GET['password'];
                    $res['res']['link'] = $link;
                }
                else{
                    $res['ok'] = false;
                    $res["error"] = 'Unknown Error!';
                }
            }
            elseif(filter_var("http://".$_GET['link'], FILTER_VALIDATE_URL)){
                $link = LinkTool("http://".$_GET['link'], $_GET['password'], "create_custom", $_GET['path']);
                if($link){
                    $res['ok'] = true;
                    $res['res']['password'] = $_GET['password'];
                    $res['res']['link'] = $link;
                }
                else{
                    $res['ok'] = false;
                    $res["error"] = 'Unknown Error!';
                }
            }
            else{
                $res['ok'] = false;
                $res["error"] = 'Unknown Error!';
            }
            }break;
        default:
            $res['ok'] = false;
            $res["error"] = 'Method not found';
            break;
    }
}
echo json_encode($res, true);