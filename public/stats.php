<?php

/**
 * router file
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

if(!defined('SITE_DOMAIN')){
    http_response_code(404);
    include 'apache-errors/404.html';
    die();
}

if((isset($_GET['pass']) && getLinkPass(SITE_URL . "/" . $uri) != $_GET['pass']) || (!isset($_GET['pass']) && getLinkPass(SITE_URL . "/" . $uri) != DEFUALT_PASSWORD))
    http_response_code(403);

?>
<html lang="he">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Link Stats ~ Yehuda Link | Url Shortner | Y-Link.ml</title>
        <meta name="keywords" content="url shortner, קיצור קישורים, מקצר קישורים, קישור, bit.ly, katzr.net, קישור מקוצר, קיצור קישור, הקטנת קישור, short url, url Shortener, url short">
        <meta name="description" content="קיצור קישורים בקלות ובמהירות (כולל סטטיסטיקות על כניסה לקישור)">
        <meta name="author" content="Yehuda Eisenberg">
        <link rel="icon" type="image/x-icon" href="logo.png">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha256-t9UJPrESBeG2ojKTIcFLPGF7nHi2vEc7f5A2KpH/UBU=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
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
        <div class="container not-select z-1">
            <div class="row">
            	<div class="col-sm-7 col-md-7 col-lg-12 mx-auto">
            		<div class="card card-shorten my-5">
            		    <div class="text-center mt-n5">
            		        <img src="logo.png" width="95px"/>
            		    </div>
            			<div class="card-body">
            				<h5 class="card-title text-center">Link stats (beta)</h5>
<?php
    if((isset($_GET['pass']) && getLinkPass(SITE_URL . "/" . $uri) != $_GET['pass']) || 
        (!isset($_GET['pass']) && getLinkPass(SITE_URL . "/" . $uri) != DEFUALT_PASSWORD)){     
?>
                            <div class="alert alert-danger text-center" role="alert">
                                This is private link
                            </div>
<?php
    }
    else {
        addVisitor($uri);
?>
                            <div class="row">
                                <div class="col-sm-3"><strong class="text-center">Short link:</strong></div><div class="col-sm-9"><a href="<?php echo SITE_URL . "/" . htmlspecialchars($uri);?>" rel="noreferrer nofollow"><?php echo SITE_URL . "/" . htmlspecialchars($uri);?></a></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"><strong class="text-center">Long link:</strong></div><div class="col-sm-9"><a href="<?php echo htmlspecialchars($longLink);?>" rel="noreferrer nofollow"><?php echo htmlspecialchars($longLink);?></a></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"><strong class="text-center">Sum of clicks:</strong></div><div class="col-sm-9"><?php echo countClicks(SITE_URL . "/" . $uri); ?></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6"><strong class="text-center">browser:</strong><canvas id="browser"></canvas></div><div class="col-sm-6"><strong class="text-center">device:</strong><canvas id="device"></canvas></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6"><strong class="text-center">os:</strong><canvas id="os"></canvas></div><div class="col-sm-6"><strong class="text-center">referral:</strong><canvas id="referral"></canvas></div>
                            </div>
                            <script>
                                var backgroundColors = ['rgba(75, 192, 192, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)','rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)']
                                var borderColors = ['rgba(75, 192, 192, 1)','rgba(255,99,132,1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)','rgba(255,99,132,1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)']
                            <?php foreach (getStatsOfLink(SITE_URL . "/" . $uri) as $tableName => $tableData){ ?>

                                /* <?php echo $tableName; ?> */
                                var ctx = document.getElementById("<?php echo $tableName; ?>");
                                var myChart = new Chart(ctx, {
                                    type: '<?php echo (($tableName == "referral") ? 'pie' : 'bar'); ?>',
                                    data: {
                                        labels: [<?php echo '"' . implode('","', array_keys($tableData)) . '"' ?>],
                                        plugins: [ChartDataLabels],
                                        datasets: [{
                                            label: '<?php echo $tableName; ?>s',
                                            data: [<?php echo implode(",", array_values($tableData)) ?>],
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {responsive: true,tooltips:{intersect : false}}
                                });
                            <?php } ?></script>
<?php } ?>
            			</div>
            		</div>
            	</div>
            </div>
        </div>
        <br><br>
        <footer class="font-small fixed-bottom not-select z-0">
            <div class="text-center py-3 text-light">
                &copy; 2018-<?php echo date("Y"); ?> <a href="https://y-link.ml/admin" target="_blank" class="text-light"><u>Yehuda Eisenberg</u></a>
            </div>
        </footer>
    </body>
</html>
