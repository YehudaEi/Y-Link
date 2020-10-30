<?php

/**
 * The functions file
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

/**
 * escape string (clean sql injection)
 * 
 * @param string $str string for clean
 * @return string cleaned string
 */
function cleanString($str){
    global $DBConn;
    
    return trim($DBConn->real_escape_string($str));
}

/**
 * check if path exist in th DB
 * 
 * @param string $path path for check
 * @return bool path exist or not
 */
function linkExistByPath($path){
    global $DBConn;
    
    $res = $DBConn->query('SELECT * FROM `mainTable` WHERE `path` = "'.cleanString($path).'";');
    while($row = $res->fetch_assoc()){
        if($row['path'] == $path)
            return true;
    }
    
    return false;
}

/**
 * check if link is valid
 * 
 * @param string $link the link
 * @return bool link valid or invalid
 */
function validLink($link){
    if(!(parse_url($link, PHP_URL_SCHEME) && parse_url($link, PHP_URL_HOST)))
        return false;
    if(strpos(parse_url($link, PHP_URL_HOST), "="))
        return false;

    return true;
}

/**
 * check if password is valid
 * 
 * @param string $password the password
 * @return bool password valid or invalid
 */
function validPassword($password){
    $password = trim($password);

    if(mb_strlen($password) < 4 || mb_strlen($password) > 30)
        return false;
    
    return true;
}

/**
 * check if path is valid
 * 
 * @param string $path the path
 * @return bool path valid or invalid
 */
function validPath($path){
    $path = trim($path);

    if(mb_strlen($path) < 4 || mb_strlen($path) > 30) 
        return false;
    
    if(!preg_match(PATH_REGEX, $path))
        return false;
    
    return true;
}

/**
 * check if shorten link is valid
 * 
 * @param string $link the shorten link
 * @return bool shorten link valid or invalid
 */
function validShorten_link($link){
    if(!validLink($link))
        return false;

    $id = trim(str_replace(SITE_URL . "/", "", $link));
    if(mb_strlen($id) < 4 || mb_strlen($id) > 30) 
        return false;

    if(!linkExistByPath($id))
        return false;
    
    return true;
}

/**
 * generate random string
 * 
 * @param int $len string length
 * @return string random string
 */
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

/**
 * create link
 * 
 * @param string $link the long string
 * @param string $password password
 * @return string sorten link path
 */
function createLink($link, $password){
    global $DBConn;

    $result = $DBConn->query("SELECT `path` FROM `mainTable` WHERE `link` = '" . cleanString($link) . "' AND `password` = '" . cleanString($password) . "'");
    if($result->num_rows > 0)
        return $result->fetch_array()['path'];
    
    do{
        $path = rnd();
    } while(linkExistByPath($path));

    $sql = "INSERT INTO `mainTable` (`id`, `path`, `link`, `password`, `deleted`) VALUES " .
           "(NULL, '" . $path . "', '" . cleanString($link) . "', '" . cleanString($password) . "', 0);";
    $DBConn->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `" . DB['dbname'] . "`.`" . $path . "` ( 
        `id` INT NOT NULL AUTO_INCREMENT , 
        `ip` TEXT NOT NULL , 
        `user_agent` TEXT NOT NULL , 
        `language` TEXT NOT NULL , 
        `referrer` TEXT NOT NULL , 
        `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_general_ci;";
    $DBConn->query($sql);

    return $path;
}

/**
 * get password of link
 * 
 * @param string $link shorten link
 * @return mixed false or password
 */
function getLinkPass($link){
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $link));
    if(!linkExistByPath($path))
        return false;

    $res = $DBConn->query('SELECT `password` FROM `mainTable` WHERE `path` = "'.cleanString($path).'";');
    return $res->fetch_array()['password'] ?? false;
}

/**
 * count click of shorten link
 * 
 * @param string $link shorten link
 * @return int num of clicks
 */
function countClicks($link){
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $link));
    if(!linkExistByPath($path))
        return false;

    $res = $DBConn->query('SELECT COUNT(*) FROM `' . DB['dbname'] . '`.`'.cleanString($path).'`;');

    return $res->fetch_array()["COUNT(*)"] ?? false;
}

/**
 * get long link by shorten link
 * 
 * @param string $link shorten link
 * @return string long link
 */
function getLongLink($link){
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $link));
    if(!linkExistByPath($path))
        return false;

    $res = $DBConn->query('SELECT * FROM `mainTable` WHERE `path` = "'.cleanString($path).'";');
    while($row = $res->fetch_assoc()){
        if($row['path'] == $path)
            return $row['link'];
    }
    
    return false;
}

/**
 * create custom link
 * 
 * @param string $link the long string
 * @param string $path path in the server
 * @param string $password password
 * @return bool success create or not
 */
function createCustomLink($link, $path, $password){
    global $DBConn;

    $sql = "INSERT INTO `mainTable` (`id`, `path`, `link`, `password`, `deleted`) VALUES " .
           "(NULL, '" . cleanString($path) . "', '" . cleanString($link) . "', '" . cleanString($password) . "', 0);";
    $DBConn->query($sql);

    $sql = "CREATE TABLE `" . DB['dbname'] . "`.`" . cleanString($path) . "` ( 
        `id` INT NOT NULL AUTO_INCREMENT , 
        `ip` TEXT NOT NULL , 
        `user_agent` TEXT NOT NULL , 
        `language` TEXT NOT NULL , 
        `referrer` TEXT NOT NULL , 
        `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_general_ci;";
    $DBConn->query($sql);

    return $path;
}

/**
 * edit long link by shorten link
 * 
 * @param string $link shorten link
 * @return bool success update or not
 */
function editLongLink($link, $shortLink){
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $shortLink));
    if(!linkExistByPath($path))
        return "short link not found";

    $DBConn->query('UPDATE `mainTable` SET `link` = "' . cleanString($link) . '" WHERE `path` = "' . cleanString($path) . '";');
    return true;
}

/**
 * add visitor 
 * 
 * @param string $path path of the shorten link
 * @return void
 */
function addVisitor($path){
    global $DBConn;

    if(!linkExistByPath($path))
        return;

    $stmt = $DBConn->prepare("INSERT INTO `" . DB['dbname'] . "`.`" . cleanString($path) . "` (id, ip, user_agent, language, referrer, time) VALUES (NULL, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $stmt->bind_param("ssss", $ip, $agent, $lang, $referrer);
    $ip = cleanString($_SERVER['REMOTE_ADDR'] ?? "");
    $agent = cleanString($_SERVER['HTTP_USER_AGENT'] ?? "");
    $lang = cleanString($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "");
    $referrer = cleanString($_SERVER["HTTP_REFERER"] ?? "");
    $stmt->execute();
    
    $stmt->close();
}
