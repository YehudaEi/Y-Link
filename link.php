<?php
    header("Cache-Control: no-cache");
    header("Cache-Control: no-store");
    $uri = substr($_SERVER['REQUEST_URI'], 1);
    if(isset($uri) && preg_match("/^[a-zA-Z0-9]+$/",$uri)){
        $servername = "localhost"; 
        $username = "id5492206_telegram"; 
        $password = "yehuda_dev"; 
        $dbname = "id5492206_telegram"; 
         
        // Create connection 
        $conn = new mysqli($servername, $username, $password, $dbname); 
        // Check connection 
        if ($conn->connect_error) { 
            sendMessage(291563178,"Connection failed: " . $conn->connect_error."\n\nuri: \"".$uri."\"",null,"HTML"); 
            http_response_code(500);
            include 'error!/500.html';
            die(); 
        }  
        $sql = "SELECT `counter`,`link` FROM `Link` WHERE `id` = '".$uri."'"; 
        $res = $conn->query($sql); 
        if (!empty($res) && $res->num_rows > 0) {
            $res1 = $res->fetch_assoc();
            $link = $res1["link"]; 
            $count = $res1["counter"]; 
            $sql = "UPDATE `Link` SET `counter` = ".($count + 1)." WHERE `Link`.`id` = '".$uri."'";
            $conn->query($sql);
        }
        $conn->close();
    }
    if(isset($link) && !empty($link))
        header("Location: ".urldecode($link));
    else{
        http_response_code(404);
        include 'error!/404.html';
    }
?>