<?php include 'theme/header.php'; ?>

<body>
<div style="top:0;right:0;position:fixed;"><span dir="rtl">בס"ד</span></div>

<?php
    function validLink($link){
        if(!(parse_url($link, PHP_URL_SCHEME) && parse_url($link, PHP_URL_HOST)) && !(parse_url("http://".$link, PHP_URL_SCHEME) && parse_url("http://".$link, PHP_URL_HOST)))
            return false;
        if(strpos(parse_url($link, PHP_URL_HOST), "="))
            return false;
        
        return true;
    }
    
	if (isset($_REQUEST['url']) && validLink($_REQUEST['url'])) {
    	$url   = $_REQUEST['url'];
    	$path  = $_REQUEST['path'] ?? null ;

		if(isset($path) && !empty($path))
		    $return = json_decode(file_get_contents("http://y-link.ml/api.php?method=custom&password=SITECREATE&path=".$path."&link=".urlencode($url)), true);
		else
		    $return = json_decode(file_get_contents("http://y-link.ml/api.php?method=create&password=SITECREATE&link=".urlencode($url)), true);
		
		$shorturl = $return['res']['link'] ?? '';
		$message  = $return['ok'] ? '' : $return['error'];
		$status   = $return['ok'] ?? false;
	}
?>

	
<?php if( isset($status) && $status ):  ?>

	<?php $url = preg_replace("(^https?://)", "", $shorturl );  ?>

	<section class="success-screen">
		<div class="container verticle-center">
			<div class="main-content">
				<div class="close noselect">
				    <a href="<?php echo siteURL ?>"><i class="material-icons">close</i></a>
				</div>
				<section class="head">
					<h2>YOUR SHORTENED LINK:</h2>
				</section>
				<section class="link-section">
					<input type="text" class="short-url" disabled style="text-transform:none;" value="<?php echo $shorturl; ?>">
					<button class="short-url-button noselect" data-clipboard-text="<?php echo $shorturl; ?>">Copy</button>
					<?php /*<span class="info">View info &amp; stats at <a href="<?php echo $shorturl; ?>+"><?php echo $url; ?>+</a></span>*/ ?>
				</section>
			</div>
	</section>

    <script>
	    var clipboard = new Clipboard('.short-url-button');
    </script>

<?php else: ?>

	<div class="container verticle-center main">
		<div class="main-content">
			<div class="above">
				<img class="noselect" src="<?php echo siteURL ?><?php echo logo ?>" alt="Logo" width="95px">
			</div>
			<section class="head">
				<p><?php echo HelloText ?></p>
			</section>
			<section class="field-section">
				<?php if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ): ?>
					<?php if (!empty($message)): ?>
						<div id="error" class="alert alert-warning error" role="alert">
							<h5>Oh no, <?php echo $message; ?>!</h5>
						</div>	    
					<?php endif; ?>
				<?php endif; ?>
				<form method="post" action="">
					<input type="url" name="url" class="url" id="url" placeholder="PASTE URL, SHORTEN &amp; SHARE" required>
					<input type="submit" value="Shorten">
					<?php if (enableCustomURL): ?>
						<span class="customise-button noselect" id="customise-toggle"><img src="<?php echo siteURL ?>/theme/assets/svg/custom-url.svg" alt="Options"> Custom Link</span>
						<div class="customise-container" id="customise-link" style="display:none;">
							<span><?php echo preg_replace("(^https?://)", "", siteURL ); ?>/</span>
							<input type="text" name="path" class="custom" placeholder="CUSTOM URL">
							<!--<input type="text" name="token" class="custom" placeholder="TOKEN">-->
						</div>
					<?php endif; ?>
				</form>
			</section>
			<section class="footer">
		<div>
			<span class="light">&copy; <?php echo date("Y"); ?> <?php echo shortTitle ?></span>
			<div class="footer-links">
				<?php foreach ($footerLinks as $key => $val): ?>
					<a href="<?php echo $val ?>"><span><?php echo $key ?></span></a>
				<?php endforeach ?>
			</div>
		</div>
	</section>
		</div>
	</div>
<?php endif; ?>
<?php include 'theme/footer.php'; ?>

<!-- Remove 000webhost banner

</body>

Remove 000webhost banner-->
</body>
</html>
