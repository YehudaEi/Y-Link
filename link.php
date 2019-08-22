<?php
    header("Cache-Control: no-cache");
    header("Cache-Control: no-store");
    $uri = substr($_SERVER['REQUEST_URI'], 1);
    if(isset($uri) && preg_match("/^[a-zA-Z0-9_]+$/",$uri)){
        $servername = "localhost"; 
        $username = "root";
        $password = ""; 
        $dbname = "link"; 
         
        $conn = new mysqli($servername, $username, $password, $dbname); 
        if ($conn->connect_error) { 
            http_response_code(500);
            include 'error!/500.html';
            die(); 
        }  
        $sql = "SELECT `counter`,`link`,`id` FROM `Link` WHERE `id` = \"".$uri."\" AND `Link`.`deleted` = FALSE"; 
        $res = $conn->query($sql); 
        if (!empty($res) && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                if($row['id'] == $uri)
                {
                    $link = $row["link"]; 
                    $count = $row["counter"]; 
                    break;
                }
            }
        }
    }
    if(isset($link) && !empty($link)){
        header("Location: ".urldecode($link));
        echo "error in moving you to <a href=\"".urldecode($link)."\" rel=\"noreferrer nofollow\">this link</a>. You can click <a href=\"".urldecode($link)."\" rel=\"noreferrer nofollow\">here</a>";
        
        $sql = "UPDATE `Link` SET `counter` = ".($count + 1)." WHERE `Link`.`id` = '".$uri."'";
        $conn->query($sql);
    }else{
        http_response_code(404);
        include 'error!/404.html';
    }
    
    
    if(isset($conn))
        $conn->close();
    
?>