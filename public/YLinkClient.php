<?php

/**
 * Client SDK
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

class Ylink{

    /**
     * the server url.
     * 
     * @var string the server url.
     */
    private static $serverUrl = "https://y-link.ml/";

    /**
     * the admin password.
     * 
     * @var string the admin password.
     */
    private $password;


    /**
     * Constructor function.
     * 
     * @param string $password the admin password.
     * @return void
     */
    public function __construct($password){
        $this->password = $password;
    }

    /**
     * Send request to the server.
     * 
     * @param array $data the post data.
     * @return array resualt from the server.
     */
    private function Request($data){
        $BaseUrl = self::$serverUrl . "api.php";
    	
        $ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $BaseUrl);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch ,CURLOPT_POSTFIELDS, $data);
       
        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if(empty($error)){
            $res = json_decode($res, true);
            if(is_array($res))
                return $res;
        }

        return array("ok" => false, "error" => array("code" => 500, "message" => "Unknown error"));
    }

    /**
     * check if link is valid
     * 
     * @param string $link the link
     * @return bool link valid or invalid
     */
    static function validLink($link){
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

        return true;
    }

    /**
     * create new link
     * 
     * @param string $link long link.
     * @param string $path (optional) sort url path.
     * @return array result from the server.
     */
    public function CreateLink($link, $path = null){
        if(!Ylink::validLink($link))
            throw new Exception("Invalid link");
        
        else{
            if($path !== null){
                $data = array(
                    "method" => "custom",
                    "password" => $this->password,
                    "link" => $link,
                    "path" => $path
                );
            }
            else{
                $data = array(
                    "method" => "create",
                    "password" => $this->password,
                    "link" => $link
                );
            }

            $res = $this->Request($data);

            return $res;
        }
    }

    /**
     * edit exist link destination.
     * 
     * @param string $link new long link.
     * @param string $path sortened url path.
     * @return array result from the server.
     */
    public function EditLink($link, $path){
        if(!Ylink::validLink($link))
            throw new Exception("Invalid link");
        
        else{
            $data = array(
                "method" => "edit",
                "password" => $this->password,
                "link" => $link,
                "shorten_link" => self::$serverUrl . $path
            );

            $res = $this->Request($data);

            return $res;
        }
    }

    /**
     * get info about shorten link
     * 
     * @param string $path sortened url path.
     * @return array result from the server.
     */
    public function LinkInfo($path){
        $data = array(
            "method" => "info",
            "password" => $this->password,
            "shorten_link" => self::$serverUrl . $path
        );

        $res = $this->Request($data);

        return $res;
    }
    
    /**
     * get stats about shorten link clicks
     * 
     * @param string $path sortened url path.
     * @return array result from the server.
     */
    public function LinkStats($path){
        $data = array(
            "method" => "stats",
            "password" => $this->password,
            "shorten_link" => self::$serverUrl . $path
        );

        $res = $this->Request($data);

        return $res;
    }
}
