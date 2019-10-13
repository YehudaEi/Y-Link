<?php include 'include/header.php'; ?>

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
    	$url      = $_REQUEST['url'];

	    $return   = json_decode(file_get_contents(siteURL."/api.php?method=get_click&password=SITECREATE&link=".urlencode($url)), true);
		
		$count    = $return['res']['clicks'] ?? '';
		$message  = $return['ok'] ? '' : $return['error'];
		$status   = $return['ok'] ?? false;
	}
?>

	
<?php if( isset($status) && $status ):  ?>

	<section class="success-screen">
		<div class="container verticle-center">
			<div class="main-content">
				<div class="close noselect">
				    <a href="<?php echo siteURL ?>/getClicks.php"><i class="material-icons">close</i></a>
				</div>
				<section class="head">
					<h2>Clicks:</h2>
				</section>
				<section class="link-section">
					<input type="text" class="short-url" disabled style="text-transform:none;" value="<?php echo $count; ?>">
					<button class="short-url-button noselect" data-clipboard-text='click of the link "<?php echo $url ?>": <?php echo $count; ?>'>Copy</button>
				</section>
			</div>
	</section>

    <script>
	    var clipboard = new Clipboard('.short-url-button');
    </script>

<?php else: ?>

	<div class="container verticle-center main">
		<div class="main-content">
		    <div class="close noselect">
			    <a href="<?php echo siteURL ?>" title="home"><i class="material-icons">home</i> Home</a>
			</div>
			<div class="above">
				<img class="noselect" src="<?php echo siteURL ?><?php echo logo ?>" alt="Logo" width="95px">
			</div>
			<section class="head">
				<p>Get Clicks of link, <br>
				Only links created through the site!
				</p>
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
					<input type="url" name="url" class="url" id="url" placeholder="<?php echo siteURL ?> link" required>
					<input type="submit" value="get">
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
<?php include 'include/footer.php'; ?>

<!-- Remove 000webhost banner

</body>

Remove 000webhost banner-->
</body>
</html>
