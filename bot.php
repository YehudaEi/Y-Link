<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Jerusalem');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

if($update == NULL){
    http_response_code(403);
    include 'error!/403.html';
    exit();
}

$rm = json_encode(array('inline_keyboard' => array(array(array('text' => 'ממלכת הרובוטים הישראלית', 'url' => "t.me/IL_BOTS/98")),array(array('text' => 'ליוצר הבוט', 'url' => "y-link.ml/Mail")))));
$markup = array('inline_keyboard' => array(array(array('text' => 'מעבר לרובוט', 'url' => "t.me/YLinkbot"))));

function curlPost($method,$datas=[]==NULL){
    $token = "";
    
    $urll = "https://api.telegram.org/bot".$token."/".$method;
	
    $ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$urll);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
   
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
		curl_close($ch);
    }else{
		curl_close($ch);
        return json_decode($res,true);
    }
}
function sendMessage($id, $mes, $reply_markup = NULL, $parse_mode = "markdown", $rmi = null){
    $PostData = array(
        'chat_id' => $id,
        'text' => $mes,
        'parse_mode' => $parse_mode, 
        'reply_markup' => $reply_markup,
        'reply_to_message_id' => $rmi
        );
    $out = curlPost('sendMessage',$PostData,$id);
    return $out;
}
function answerInline($id, $data=[]){
$PostData = array(
    'inline_query_id' => $id,
    'cache_time' => 30,
    'results' => $data
);
$res = curlPost('answerInlineQuery',$PostData);
return $res;
}

if(isset($update["message"]["text"])){
    $mes = $update["message"]["text"];
    $mesId = $update["message"]["message_id"];
    $id = $update["message"]["chat"]["id"];
    if($mes == "/start")
         sendMessage($id,"היי 👋🏼 
ברוכים הבאים לרובוט מקצר הקישורים.
שלחו לי קישור שתרצו ואחזיר לכם כתובת מקוצרת שלו.

📍הרובוט עובד גם במצב אינליין, פשוט להקליד את השם משתמש של הרובוט ואז קישור.

🆕 חדש ברובוט!!
מידע אודות כמות צפיות בקישורים.
למידע נוסף /info",$rm);
    elseif($mes == "/info")
        sendMessage($id,"🆕 חדש ברובוט!!
אוכל לגלות לכם כמה כניסות בוצעו על ידי משתמשים בקישור שיצרתם דרכי.
שלחו לי קישור ואקצר אותו, לאחר מכן שלחו לי הקישור (המקוצר) ואחזיר לכם את כמות הכניסות שמשתמשים ביצעו באמצעות הקישור.",$rm);
    else{
        if(filter_var($mes, FILTER_VALIDATE_URL) || filter_var("http://".$mes, FILTER_VALIDATE_URL)){
            if(parse_url($mes, PHP_URL_HOST) == "y-link.ml"){
                $link = json_decode(file_get_contents("http://y-link.ml/api.php?method=get_click&password=tgID".$id."&link=".($mes)), true);
                if(!$link['ok'])
                    sendMessage($id,"שגיאה! נסה שנית..",null,null,$mesId);
                else
                    sendMessage($id,"כמות הלחיצות על הקישור שלך: ".$link['res']['clicks'],null,null,$mesId);
            }
            else{
                $link = json_decode(file_get_contents("http://y-link.ml/api.php?method=create&password=tgID".$id."&link=".($mes)), true);
                if(!$link['ok'])
                    sendMessage($id,"הקישור אינו תקין!\nשלח קישור תקין כגון: http://y-link.ml",null,null,$mesId);
                else
                    sendMessage($id,$link['res']['link'],null,null,$mesId);
            }
        }
        else
            sendMessage($id,"הקישור אינו תקין!\nשלח קישור תקין כגון: http://y-link.ml",null,null,$mesId);
    }
}
elseif(isset($update["inline_query"]["query"])){
    $inlineQ = $update["inline_query"]["query"];
    $inlineFromId = $update["inline_query"]["from"]["id"];
    $InlineQId = $update["inline_query"]["id"];
    if($inlineQ == ""){
        $markup = array('inline_keyboard' => array(array(array('text' => 'מעבר לרובוט', 'url' => "t.me/YLinkbot"))));
        $mResult = array(array(
    		"type" => "article",
            "id" => "1",
            "title" => "הקלד את הקישור",
    		"description" => "לדוגמא: http://y-link.ml",
            "message_text" => "לחץ [כאן](t.me/YLinkBot) למעבר לבוט",
            "reply_markup" => $markup,
            "parse_mode" => "Markdown",
    	));
        answerInline($InlineQId,json_encode($mResult));
    }
    else{
        if(filter_var($inlineQ, FILTER_VALIDATE_URL) || filter_var("http://".$inlineQ, FILTER_VALIDATE_URL)){
            if(parse_url($inlineQ, PHP_URL_HOST) == "y-link.ml"){
                $link = json_decode(file_get_contents("http://y-link.ml/api.php?method=get_click&password=tgID".$inlineFromId."&link=".$inlineQ), true);
                if(!$link['ok'])
                    $mResult = array(array(
                		"type" => "article",
                        "id" => "1",
                        "title" => "שגיאה!",
                		"description" => "נסה שנית..",
                        "message_text" => "התרחשה שגיאה!\nלחץ [כאן](t.me/YLinkBot) למעבר לבוט",
                        "reply_markup" => $markup,
                        "parse_mode" => "Markdown",
                	));
                else
                    $mResult = array(array(
                		"type" => "article",
                        "id" => "1",
                        "title" => "רובוט קיצור קישורים",
                		"description" => "לחץ כאן לקבלת כמות הלחיצות",
                        "message_text" => "כמות הלחיצות על הקישור שלך: ".$link['res']['clicks'],
                        "parse_mode" => "markdown"
                	));
            }
            else{
                $link = json_decode(file_get_contents("http://y-link.ml/api.php?method=create&password=tgID".$inlineFromId."&link=".$inlineQ), true);
                if(!$link['ok'])
                    $mResult = array(array(
                		"type" => "article",
                        "id" => "1",
                        "title" => "שגיאה!",
                		"description" => "נסה שנית..",
                        "message_text" => "התרחשה שגיאה!\nלחץ [כאן](t.me/YLinkBot) למעבר לבוט",
                        "reply_markup" => $markup,
                        "parse_mode" => "Markdown",
                	));
                else
                    $mResult = array(array(
                		"type" => "article",
                        "id" => "1",
                        "title" => "רובוט קיצור קישורים",
                		"description" => "לחץ כאן לקבלת הקישור",
                        "message_text" => $link['res']['link'],
                        "parse_mode" => "markdown"
                	));
            }
        }
        else
            $mResult = array(array(
        		"type" => "article",
                "id" => "1",
                "title" => "הקישור אינו תקין",
        		"description" => "קישור לדוגמא: http://y-link.ml",
                "message_text" => "לחץ [כאן](t.me/YLinkBot) למעבר לבוט",
                "reply_markup" => $markup,
                "parse_mode" => "Markdown",
        	));
        answerInline($InlineQId,json_encode($mResult));
    }
}
?>