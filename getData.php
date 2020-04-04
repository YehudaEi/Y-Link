<?php if(isset($getData) && $getData){ include 'include/header.php'; ?>

<body>
<div style="top:0;right:0;position:fixed;"><span dir="rtl">בס"ד</span></div>
	
	<section class="success-screen">
		<div class="container verticle-center">
			<div class="main-content">
				<div class="close noselect">
				    <a href="<?php echo siteURL ?>/"><i class="material-icons">close</i></a>
				</div>
				<section class="head">
					<h2>Base URL:</h2>
				</section>
				<section class="link-section">
					<input type="text" class="short-url" disabled style="text-transform:none;" value="<?php echo urldecode($link); ?>">
					<button class="short-url-button noselect" data-clipboard-text='short link: "<?php echo siteURL.'/'.$uri ?>" | base url: <?php echo urldecode($link); ?>'>Copy</button>
				</section>
			</div>
	</section>

    <script>
	    var clipboard = new Clipboard('.short-url-button');
    </script>

<?php include 'include/footer.php'; ?>
</body>
<!-- Remove rimon scripts </body> -->
</html>
<?php } else{ http_response_code(404); include 'apache-errors/404.html'; } ?>