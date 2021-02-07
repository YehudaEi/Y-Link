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
    private static $serverUrl = "http://y-link.ml/";

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
        if(empty(curl_error($ch))){
            curl_close($ch);
            $res = json_decode($res, true);

            return $res;
        }
        curl_close($ch);
        return array("ok" => false, "error" => array("code" => 500, "message" => "Unknown error"));
    }

    /**
     * check if link is valid
     * 
     * @param string $link the link
     * @return bool link valid or invalid
     */
    static function validLink($link){
        if(preg_match("/magnet:\?xt=urn:[a-z0-9]+:[a-z0-9]{32}/i", $link))
            return true;
        if(!(parse_url($link, PHP_URL_SCHEME) && parse_url($link, PHP_URL_HOST)))
            return false;
        if(strpos(parse_url($link, PHP_URL_HOST), "="))
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
}
