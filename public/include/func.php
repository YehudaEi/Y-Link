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
    
    $res = $DBConn->query('SELECT `path` FROM `mainTable` WHERE `path` = "'.cleanString($path).'";');
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
function validLink($link, $allowYLink = false){
    if(preg_match("/magnet:\?xt=urn:[a-z0-9]+:[a-z0-9]{32}/i", $link))
        return true;
    if(!(parse_url($link, PHP_URL_SCHEME) && parse_url($link, PHP_URL_HOST)))
        return false;
    if(strpos(parse_url($link, PHP_URL_HOST), "="))
        return false;
    if(parse_url($link, PHP_URL_HOST) == SITE_DOMAIN && !$allowYLink)
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
    if(!validLink($link, true))
        return false;

    $id = trim(str_replace(SITE_URL . "/", "", $link));
    if(mb_strlen($id) < 4 || mb_strlen($id) > 30) 
        return false;

    if(!linkExistByPath($id))
        return false;
    
    return true;
}

/**
 * check if start date is valid
 * 
 * @param string $date start date
 * @return bool start date valid or invalid
 */
function validStart_date($date){
    if(strlen($date) != 19)
        return false;
    
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    
    return $d && $d->format('Y-m-d H:i:s') == $date;
}

/**
 * check if end date is valid
 * 
 * @param string $date end date
 * @return bool end date valid or invalid
 */
function validEnd_date($date){
    if(strlen($date) != 19)
        return false;
    
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    
    return $d && $d->format('Y-m-d H:i:s') == $date;
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

    $sql = "INSERT INTO `mainTable` (`id`, `path`, `link`, `password`, `ip`, `deleted`) VALUES " .
           "(NULL, '" . $path . "', '" . cleanString($link) . "', '" . cleanString($password) . "', '" . cleanString(CLIENT_IP) . "', 0);";
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
 * @param string $startDate start date
 * @param string $endDate end date
 * @return int num of clicks
 */
function countClicks($link, $startDate = null, $endDate = null){
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $link));
    if(!linkExistByPath($path))
        return false;

    if(!empty($startDate) && !empty($endDate)){
        $res = $DBConn->query('SELECT `id` FROM `clicks` WHERE `path` = "'.cleanString($path).'" AND `time` > "'.cleanString($startDate).'" AND `time` < "'.cleanString($endDate).'";');
    }
    else{
        $res = $DBConn->query('SELECT `id` FROM `clicks` WHERE `path` = "'.cleanString($path).'";');
    }

    return $res->num_rows ?? false;
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
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $link));
    if(!linkExistByPath($path))
        return false;

    if(!empty($startDate) && !empty($endDate)){
        $res = $DBConn->query('SELECT `user_agent`,`language`,`referrer`,`time` FROM `clicks` WHERE `path` = "'.cleanString($path).'" AND `time` > "'.cleanString($startDate).'" AND `time` < "'.cleanString($endDate).'";');
    }
    else{
        $res = $DBConn->query('SELECT `user_agent`,`language`,`referrer`,`time` FROM `clicks` WHERE `path` = "'.cleanString($path).'";');
    }
    
    return $res->fetch_all(MYSQLI_ASSOC) ?? false;
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
    if($data == false)
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

        $browser = strtolower($tmpBrowser->browser->name);
        $device = strtolower($tmpBrowser->device->type);
        $os = strtolower($tmpBrowser->os->name);

        if ($browser == "internet explorer") $browser = "IE";
        if ($os == "ubuntu") $os = "linux";
        if ($os == "os x") $os = "macos";
        
        if(strpos(strtolower($click['user_agent']), "bot") !== false || strpos(strtolower($click['user_agent']), "whatsapp") !== false){
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
    global $DBConn;
    
    $path = trim(str_replace(SITE_URL . "/", "", $link));
    if(!linkExistByPath($path))
        return false;

    $res = $DBConn->query('SELECT `path`,`link` FROM `mainTable` WHERE `path` = "'.cleanString($path).'" AND `deleted` != 1;');
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

    $sql = "INSERT INTO `mainTable` (`id`, `path`, `link`, `password`, `ip`, `deleted`) VALUES " .
           "(NULL, '" . cleanString($path) . "', '" . cleanString($link) . "', '" . cleanString($password) . "', '" . cleanString(CLIENT_IP) . "', 0);";
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
    
    $stmt = $DBConn->prepare("INSERT INTO `clicks` (id, path, ip, user_agent, language, referrer, time) VALUES (NULL, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $stmt->bind_param("sssss", $path, $ip, $agent, $lang, $referrer);
    $path = cleanString($path);
    $ip = cleanString($_SERVER['REMOTE_ADDR'] ?? "");
    $agent = cleanString($_SERVER['HTTP_USER_AGENT'] ?? "");
    $lang = cleanString($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "");
    $referrer = cleanString($_SERVER["HTTP_REFERER"] ?? "");
    
    if(!isset($_SESSION['links']) || !is_array($_SESSION['links'])){
        $_SESSION['links'] = array();
    }
    
    if(!in_array($path . "~~~" . $_SERVER["HTTP_REFERER"], $_SESSION['links'])){
        $_SESSION['links'][] = $path . "~~~" . $_SERVER["HTTP_REFERER"];
        $stmt->execute();
    }
    
    $stmt->close();
}
