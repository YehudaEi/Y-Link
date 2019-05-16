<html><head>
	<meta dir="rtl" https-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>קיצור קישורים</title>
	<link rel="icon" type="image/x-icon" href="logo.png">
	<meta name="robots" content="index,follow">
	<style>
.tooltip {
    position: relative;
    display: inline-block;
}
.tooltip .tooltiptext {
    visibility: hidden;
    width: 140px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 150%;
    left: 50%;
    margin-left: -75px;
    opacity: 0;
    transition: opacity 0.3s;
}
.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}
.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
</style>
</head>

<body bgcolor="white">
    <div style="visibility: hidden;"></body></div>
    <div align="center" dir="rtl">
        <h1>ברוכים הבאים לאתר הרשמי של בוט קיצור הקישורים!</h1>
        <h2 style="color:red;">לידעתכם: לא ניתן לבדוק את כמות הכניסות ביצירה דרך האתר. באפשרותכם ליצור גם דרך ה<a href="https://t.me/YLinkBot">בוט</a></h2>
        <h2 style="color:green;">חדש!! API שלא דורש רישום!!! מוזמנים להסתכל <a href="api.php">כאן</a></h2>
    </div>
	<form method="post" action="">
		<table border="0" align="center" dir="rtl">
			<tbody>
				<tr>
					<td align="center" colspan="2">
						שם האתר:<input type="url" name="url" id="url" dir="ltr" placeholder="http://www.example.com" size="50" value="" required="">
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="צור" name="crate">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
		
			<table width="100%" id="footer">
				<tbody>
					<tr>
						<td>
							<div align="right"> <a href="mailto:info@y-link.ml?Subject=y-link.ml" style="color:gray" title="Yehuda Eisenberg" target="_top">info@y-link.ml</a>
								כל הזכויות שמורות © 2019
								<br>
								<br>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		
	
</body></html>

<?php
if(isset($_POST['crate'])){
    echo "<script>document.body.innerHTML = '';</script><div align=\"center\" dir=\"rtl\">";
$url = isset($_POST['url']) ? $_POST['url'] : "y-link.ml";
if(preg_match("%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu",$url)){
    $link = json_decode(file_get_contents("http://y-link.ml/api.php?method=create&password=".uniqid('u')."&link=".$url), true);
    if(!$link['ok']) die($link['error']);
    $nurl = $link['res']['link'];
    echo <<<HTML
    <p style="font-size:60px;">אתה מוזמן להנות מהקישור שלך: <a href="{$nurl}" id="url">{$nurl}</a></p><br><br>
    <div class="tooltip">
    <button onclick="CopyToClipboard('$nurl')" onmouseout="outFunc()" style="font-size:30px;">
    <span class="tooltiptext" id="myTooltip" style="font-size:20px;">לחץ כדי להעתיק</span>
    העתק קישור
    </button>
</div>
<script>
function CopyToClipboard(text) {
            
            var success = true;
            if (window.clipboardData) { // Internet Explorer
                window.clipboardData.setData("Text", text);
            }
            else {
                    // create a temporary element for the execCommand method
                var forExecElement = CreateElementForExecCommand (text);
                        /* Select the contents of the element 
                            (the execCommand for 'copy' method works on the selection) */
                SelectContent (forExecElement);
                var supported = true;
                    // UniversalXPConnect privilege is required for clipboard access in Firefox
                try {
                    if (window.netscape && netscape.security) {
                        netscape.security.PrivilegeManager.enablePrivilege ("UniversalXPConnect");
                    }
                        // Copy the selected content to the clipboard
                        // Works in Firefox and in Safari before version 5
                    success = document.execCommand ("copy", false, null);
                }
                catch (e) {
                    success = false;
                }
                
                    // remove the temporary element
                document.body.removeChild (forExecElement);
            }
            if (success) {
                var tooltip = document.getElementById("myTooltip");
                tooltip.innerHTML = "הקישור שהועתק: " + text;
            }
            else {
                var tooltip = document.getElementById("myTooltip");
                tooltip.innerHTML = "הדפדפן שלך לא תומך בהעתקה";
            }
        }
        function CreateElementForExecCommand (text) {
            var forExecElement = document.createElement ("div");
                // place outside the visible area
            forExecElement.style.position = "absolute";
            forExecElement.style.left = "-10000px";
            forExecElement.style.top = "-10000px";
                // write the necessary text into the element and append to the document
            forExecElement.textContent = text;
            document.body.appendChild (forExecElement);
                // the contentEditable mode is necessary for the  execCommand method in Firefox
            forExecElement.contentEditable = true;
            return forExecElement;
        }
        function SelectContent (element) {
                // first create a range
            var rangeToSelect = document.createRange ();
            rangeToSelect.selectNodeContents (element);
                // select the contents
            var selection = window.getSelection ();
            selection.removeAllRanges ();
            selection.addRange (rangeToSelect);
        }
function outFunc() {
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "לחץ כדי להעתיק";
}
</script>
HTML;
}
else
    echo "<H1 style=\"color:red; font-size:60px;\"> הקישור אינו תקין!\nשלח קישור תקין כגון: http://y-link.ml</H1>";
}
?>