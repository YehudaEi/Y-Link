<?php

/**
 * The Index file (UI)
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

require_once(__DIR__ . "/YLinkClient.php");

if(isset($_POST['url'])){    
    $client = new YLink((isset($_POST['privateMode']) && $_POST['privateMode'] == "on") ? "HIDDEN_".uniqid() : "DefaultPasswords");
    try{
        $res = $client->CreateLink($_POST['url'], empty($_POST['path']) ? null : $_POST['path']);
    }
    catch(Exception $e){
        $res['ok'] = false;
        $res['error']['message'] = $e->getMessage();
    }
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Yehuda Link | Url Shortner | Y-Link.ml</title>
        <meta name="keywords" content="url shortner">
        <meta name="description" content="free url shortner">
        <meta name="author" content="Yehuda Eisenberg">
        <link rel="icon" type="image/x-icon" href="logo.png">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.js" integrity="sha512-tjW2dLIvxBrQWtbL7npJzlMVxznKMrkEJtRX5ztkEP6RC5oJdVkmAfFNHTSNrqv7++hAza+dvV4Bijf8rHeC0Q==" crossorigin="anonymous"></script>
        <style>:root{--input-padding-x:1.5rem;--input-padding-y: .75rem}body{background:#007bff;background:linear-gradient(to right,#0062E6,#33AEFF)}.card-shorten{border:0;border-radius:1rem;box-shadow:0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1)}.card-shorten .card-title{margin-bottom:2rem;font-weight:300;font-size:1.5rem}.card-shorten .card-body{padding:2rem}.form-shorten{width:100%}.form-shorten .btn{font-size:80%;border-radius:5rem;letter-spacing: .1rem;font-weight:bold;padding:1rem;transition:all 0.2s}.form-label-group{position:relative;margin-bottom:1rem}.form-label-group input{height:auto;border-radius:2rem}.form-label-group>input,.form-label-group>label{padding:var(--input-padding-y) var(--input-padding-x)}.form-label-group>label{position:absolute;top:0;left:0;display:block;width:100%;margin-bottom:0;line-height:1.5;color:#495057;border:1px solid transparent;border-radius: .25rem;transition:all .1s ease-in-out}.form-label-group input::-webkit-input-placeholder{color:transparent}.form-label-group input:-ms-input-placeholder{color:transparent}.form-label-group input::-ms-input-placeholder{color:transparent}.form-label-group input::-moz-placeholder{color:transparent}.form-label-group input::placeholder{color:transparent}.form-label-group input:not(:placeholder-shown){padding-top:calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));padding-bottom:calc(var(--input-padding-y) / 3)}.form-label-group input:not(:placeholder-shown)~label{padding-top:calc(var(--input-padding-y) / 3);padding-bottom:calc(var(--input-padding-y) / 3);font-size:12px;color:#777}.btn-google{color:white;background-color:#ea4335}.btn-facebook{color:white;background-color:#3b5998}@supports (-ms-ime-align: auto){.form-label-group>label{display:none}.form-label-group input::-ms-input-placeholder{color:#777}}@media all and (-ms-high-contrast: none), (-ms-high-contrast: active){.form-label-group>label{display:none}.form-label-group input:-ms-input-placeholder{color:#777}}.not-select {-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;}</style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light not-select">
            <a class="navbar-brand text-light" href="#"><strong>Yehuda Link</strong></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link text-light" href="/">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link text-light" href="https://y-link.ml/API_Docs">Docs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="https://y-link.ml/Source">GitHub</a>
                    </li>
                </ul>
            </div>
        </nav>
        <br><br>
        <div class="container">
            <div class="row">
            	<div class="col-sm-7 col-md-7 col-lg-7 mx-auto">
            		<div class="card card-shorten my-5">
            		    <div class="text-center mt-n5 not-select">
            		        <img src="logo.png" width="95px"/>
            		    </div>
            			<div class="card-body">
            				<h5 class="card-title text-center not-select">Url Shortener</h5>
<?php

if(isset($res)){
    if($res['ok']){
?>
                            <div class="alert alert-success" role="alert">
                                Your shortened link is <code id="shortenedLink"><?php echo $res['res']['link']; ?></code><br><a href="#" data-clipboard-target="#shortenedLink" class="alert-link">Click here to copy</a>.
                            </div>
                            <script>new ClipboardJS('.alert-link');</script>
<?php
    }
    else{
?>
                            <div class="alert alert-danger" role="alert">
                                You have error: <code><?php echo $res['error']['message']; ?></code>
                            </div>
<?php
    }
}

?>
            				<form class="form-shorten not-select" method="post">
            					<div class="form-label-group">
            						<input type="text" id="url" name="url" class="form-control" placeholder="https://example.com" required autofocus>
            						<label for="url">Long URL</label>
            					</div>
            					<div class="row">
                					<div class="form-label-group col-md-4">
                						<button class="btn btn-outline-info" type="button" aria-controls="customLink" data-toggle="collapse" data-target="#customLink" aria-expanded="false">Advance link</button>
                					</div>
                					<div class="collapse col-md-8" id="customLink">
                    					<div class="form-label-group">
                    						<input type="text" id="path" name="path" class="form-control" placeholder="https://y-link.ml/path" aria-describedby="addon-domain">
                    						<label for="path">Custom Path</label>
                    					</div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="privateMode" name="privateMode">
                                            <label class="custom-control-label" for="privateMode">Private Mode (hidden stats)</label>
                                        </div>
                                        <br>
                					</div>
            					</div>
            					<button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Shorten</button>
            				</form>
            			</div>
            		</div>
            	</div>
            </div>
        </div>
        <br><br>
        <footer class="font-small fixed-bottom not-select">
            <div class="text-center py-3 text-light">
                &copy; 2018-<?php echo date("Y"); ?> <a href="https://y-link.ml/admin" target="_blank" class="text-light"><u>Yehuda Eisenberg</u></a>
            </div>
        </footer>
    </body>
</html>