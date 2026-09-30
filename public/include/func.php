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
 * run a prepared statement
 *
 * @param string $sql the query (with "?" placeholders)
 * @param string $types bind types (e.g. "ss")
 * @param mixed ...$params values to bind
 * @return mixed mysqli_result for SELECT, true for other queries, false on failure
 */
function dbQuery($sql, $types = "", ...$params){
    global $DBConn;

    $stmt = $DBConn->prepare($sql);
    if($stmt === false)
        return false;

    if($types !== "")
        $stmt->bind_param($types, ...$params);

    if(!$stmt->execute()){
        $stmt->close();
        return false;
    }

    $res = $stmt->get_result();
    $stmt->close();

    return $res === false ? true : $res;
}

/**
 * get the path from shorten link (or return the path itself)
 *
 * @param string $link shorten link or path
 * @return string path
 */
function pathFromLink($link){
    $prefix = '~^https?://' . preg_quote(SITE_DOMAIN, '~') . '/~i';

    return trim(preg_replace($prefix, '', trim($link)));
}

/**
 * check if path exist in th DB
 *
 * @param string $path path for check
 * @return bool path exist or not
 */
function linkExistByPath($path){
    $res = dbQuery('SELECT `path` FROM `mainTable` WHERE `path` = ?', "s", $path);
    if(!$res)
        return false;

    while($row = $res->fetch_assoc()){
        if($row['path'] === $path)
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
function validLink($link, $allowYLink = false){
    if(!is_string($link))
        return false;

    $link = trim($link);

    if(preg_match("/^magnet:\?xt=urn:[a-z0-9]+:[a-z0-9]{32}/i", $link))
        return true;

    $scheme = parse_url($link, PHP_URL_SCHEME);
    $host = parse_url($link, PHP_URL_HOST);
    if(!($scheme && $host))
        return false;
    if(in_array(strtolower($scheme), array("javascript", "data", "vbscript", "file"), true))
        return false;
    if(strpos($host, "=") !== false)
        return false;
    if(strtolower($host) == strtolower(SITE_DOMAIN) && !$allowYLink)
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
    if(!is_string($password))
        return false;

    $password = trim($password);

    return mb_strlen($password) >= 4 && mb_strlen($password) <= 30;
}

/**
 * check if path is valid
 *
 * @param string $path the path
 * @return bool path valid or invalid
 */
function validPath($path){
    if(!is_string($path))
        return false;

    $path = trim($path);

    if(mb_strlen($path) < 4 || mb_strlen($path) > 30)
        return false;

    return (bool)preg_match(PATH_REGEX, $path);
}

/**
 * check if path is reserved (used by files of the site)
 *
 * @param string $path the path
 * @return bool path reserved or not
 */
function reservedPath($path){
    return file_exists(__DIR__ . '/../' . trim($path));
}

/**
 * check if shorten link is valid
 *
 * @param string $link the shorten link
 * @return bool shorten link valid or invalid
 */
function validShorten_link($link){
    if(!validLink($link, true))
        return false;

    $path = pathFromLink($link);
    if(mb_strlen($path) < 4 || mb_strlen($path) > 30)
        return false;

    return linkExistByPath($path);
}

/**
 * check if date is valid (format: Y-m-d H:i:s)
 *
 * @param string $date the date
 * @return bool date valid or invalid
 */
function validDate($date){
    if(!is_string($date) || strlen($date) != 19)
        return false;

    $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);

    return $d && $d->format('Y-m-d H:i:s') === $date;
}

/**
 * check if start date is valid
 *
 * @param string $date start date
 * @return bool start date valid or invalid
 */
function validStart_date($date){
    return validDate($date);
}

/**
 * check if end date is valid
 *
 * @param string $date end date
 * @return bool end date valid or invalid
 */
function validEnd_date($date){
    return validDate($date);
}


/**
 * generate random string
 *
 * @param int $len string length
 * @return string random string
 */
function rnd($len = 6) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $alphaLength = strlen($alphabet) - 1;
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        $str .= $alphabet[random_int(0, $alphaLength)];
    }
    return $str;
}

/**
 * get the client ip
 *
 * @return string client ip
 */
function clientIp(){
    return $_SERVER['REMOTE_ADDR'] ?? "";
}

/**
 * create link
 *
 * @param string $link the long string
 * @param string $password password
 * @return mixed sorten link path or false on failure
 */
function createLink($link, $password){
    $link = trim($link);

    $res = dbQuery('SELECT `path`, `password` FROM `mainTable` WHERE `link` = ? AND `deleted` = 0', "s", $link);
    if($res){
        while($row = $res->fetch_assoc()){
            if(hash_equals($row['password'], $password))
                return $row['path'];
        }
    }

    do{
        $path = rnd();
    } while(linkExistByPath($path) || reservedPath($path));

    return createCustomLink($link, $path, $password);
}

/**
 * get password of link
 *
 * @param string $link shorten link
 * @return mixed false or password
 */
function getLinkPass($link){
    $path = pathFromLink($link);

    $res = dbQuery('SELECT `path`, `password` FROM `mainTable` WHERE `path` = ?', "s", $path);
    if(!$res)
        return false;

    while($row = $res->fetch_assoc()){
        if($row['path'] === $path)
            return $row['password'];
    }

    return false;
}

/**
 * check the password of link
 *
 * @param string $link shorten link
 * @param string $password password for check
 * @return bool password correct or not
 */
function checkLinkPassword($link, $password){
    $stored = getLinkPass($link);

    return is_string($stored) && is_string($password) && hash_equals($stored, $password);
}

/**
 * count click of shorten link
 *
 * @param string $link shorten link
 * @param string $startDate start date
 * @param string $endDate end date
 * @return int num of clicks
 */
function countClicks($link, $startDate = null, $endDate = null){
    $path = pathFromLink($link);
    if(!linkExistByPath($path))
        return false;

    if(!empty($startDate) && !empty($endDate)){
        $res = dbQuery('SELECT COUNT(*) AS `count` FROM `clicks` WHERE `path` = ? AND `time` > ? AND `time` < ?', "sss", $path, $startDate, $endDate);
    }
    else{
        $res = dbQuery('SELECT COUNT(*) AS `count` FROM `clicks` WHERE `path` = ?', "s", $path);
    }

    if(!$res)
        return false;

    return (int)$res->fetch_assoc()['count'];
}

/**
 * info click of shorten link
 *
 * @param string $link shorten link
 * @param string $startDate start date
 * @param string $endDate end date
 * @return array info of clicks
 */
function getAllClickOfLink($link, $startDate = null, $endDate = null){
    $path = pathFromLink($link);
    if(!linkExistByPath($path))
        return false;

    if(!empty($startDate) && !empty($endDate)){
        $res = dbQuery('SELECT `user_agent`,`language`,`referrer`,`time` FROM `clicks` WHERE `path` = ? AND `time` > ? AND `time` < ?', "sss", $path, $startDate, $endDate);
    }
    else{
        $res = dbQuery('SELECT `user_agent`,`language`,`referrer`,`time` FROM `clicks` WHERE `path` = ?', "s", $path);
    }

    if(!$res)
        return false;

    return $res->fetch_all(MYSQLI_ASSOC);
}

/**
 * get stats of link clicks
 *
 * @param string $link shorten link
 * @param string $startDate start date
 * @param string $endDate end date
 * @return array stats of clicks
 */
function getStatsOfLink($link, $startDate = null, $endDate = null){
    $data = getAllClickOfLink($link, $startDate, $endDate);
    if($data === false)
        return false;

    $browsers = array(
        "chrome" => 0,
        "firefox" => 0,
        "edge" => 0,
        "IE" => 0,
        "opera" => 0,
        "safari" => 0,
        "samsung internet" => 0,
        "miui browser" => 0,
        "bot" => 0,
        "other" => 0
    );
    $devices = array(
        "desktop" => 0,
        "tablet" => 0,
        "mobile" => 0,
        "bot" => 0,
        "other" => 0
    );
    $oss = array(
        "windows" => 0,
        "android" => 0,
        "ios" => 0,
        "linux" => 0,
        "macos" => 0,
        "kaios" => 0,
        "bot" => 0,
        "other" => 0
    );
    $referrals = array(
        "direct" => 0,
        "other" => 0
    );

    foreach($data as $click){
        $tmpBrowser = new WhichBrowser\Parser($click['user_agent']);
        $tmpReferrer = parse_url($click['referrer'], PHP_URL_HOST);
        $userAgent = strtolower($click['user_agent']);

        $browser = strtolower($tmpBrowser->browser->name ?? "");
        $device = strtolower($tmpBrowser->device->type ?? "");
        $os = strtolower($tmpBrowser->os->name ?? "");

        if ($browser == "internet explorer") $browser = "IE";
        if ($os == "ubuntu") $os = "linux";
        if ($os == "os x") $os = "macos";

        if(strpos($userAgent, "bot") !== false || strpos($userAgent, "whatsapp") !== false){
            $browser = "bot";
            $device = "bot";
            $os = "bot";
        }

        if(isset($browsers[$browser]))
            $browsers[$browser]++;
        else
            $browsers['other']++;

        if(isset($devices[$device]))
            $devices[$device]++;
        else
            $devices['other']++;

        if(isset($oss[$os]))
            $oss[$os]++;
        else
            $oss['other']++;

        if($tmpReferrer){
            $referrals[$tmpReferrer] = isset($referrals[$tmpReferrer]) ? $referrals[$tmpReferrer] + 1 : 1;
        }
        else{
            if(strlen($click['referrer']) == 0)
                $referrals['direct']++;
            else
                $referrals['other']++;
        }
    }

    return array(
        "browser" => $browsers,
        "device" => $devices,
        "os" => $oss,
        "referral" => $referrals
    );
}

/**
 * get long link by shorten link
 *
 * @param string $link shorten link
 * @return string long link
 */
function getLongLink($link){
    $path = pathFromLink($link);

    $res = dbQuery('SELECT `path`,`link` FROM `mainTable` WHERE `path` = ? AND `deleted` != 1', "s", $path);
    if(!$res)
        return false;

    while($row = $res->fetch_assoc()){
        if($row['path'] === $path)
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
 * @return mixed path or false on failure
 */
function createCustomLink($link, $path, $password){
    $path = trim($path);

    $success = dbQuery('INSERT INTO `mainTable` (`path`, `link`, `password`, `ip`, `deleted`) VALUES (?, ?, ?, ?, 0)',
                       "ssss", $path, trim($link), $password, clientIp());

    return $success ? $path : false;
}

/**
 * edit long link by shorten link
 *
 * @param string $link the new long link
 * @param string $shortLink shorten link
 * @return bool success update or not
 */
function editLongLink($link, $shortLink){
    $path = pathFromLink($shortLink);
    if(!linkExistByPath($path))
        return false;

    return (bool)dbQuery('UPDATE `mainTable` SET `link` = ? WHERE `path` = ?', "ss", trim($link), $path);
}

/**
 * add visitor
 *
 * @param string $path path of the shorten link
 * @return void
 */
function addVisitor($path){
    if(!linkExistByPath($path))
        return;

    if(session_status() === PHP_SESSION_NONE && !headers_sent())
        session_start();

    $referrer = $_SERVER["HTTP_REFERER"] ?? "";

    if(!isset($_SESSION['links']) || !is_array($_SESSION['links'])){
        $_SESSION['links'] = array();
    }

    $key = $path . "~~~" . $referrer;
    if(in_array($key, $_SESSION['links'], true))
        return;

    $_SESSION['links'][] = $key;

    dbQuery('INSERT INTO `clicks` (`path`, `ip`, `user_agent`, `language`, `referrer`, `time`) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)',
            "sssss", $path, clientIp(), $_SERVER['HTTP_USER_AGENT'] ?? "", $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "", $referrer);
}
