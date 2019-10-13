<?php
  include 'config.php';
  include 'functions.php';
?>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-patible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo description ?>">
    <link rel="icon" href="<?php echo favicon ?>">

    <title><?php echo title ?></title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400|Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo siteURL ?>/include/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo siteURL ?>/include/assets/css/mobile.css">
    <link rel="stylesheet" href="<?php echo siteURL ?>/include/assets/css/tablet.css">
    <link rel="stylesheet" href="<?php echo siteURL ?>/include/assets/css/desktop.css">

    <?php if (defined('backgroundImage')) : ?>
      <style>
        body,
        .success-screen {
          background: url(<?php echo backgroundImage ?>) no-repeat center center fixed; 
          -webkit-background-size: cover;
          -moz-background-size: cover;
          -o-background-size: cover;
          background-size: cover;
        }
      </style>
    <?php elseif (defined('colour')) : ?>
      <style>
        body {
          background-color: <?php echo colour ?>;
        }

        input[type="submit"],
        .short-url-button {
          background-color: <?php echo colour ?>;
        }

        input[type="submit"]:hover,
        .short-url-button:hover {
          background-color: <?php echo adjustBrightness(colour, -15) ?>;
        }

        .success-screen {
          background-color: <?php echo colour ?>;
        }
      </style>
    <?php endif; ?>

    <script src="include/assets/js/clipboard.min.js"></script>

    <!-- Add extra support of older browsers -->
    <!--[if lt IE 9]>
      <script src="include/assets/js/html5shiv.min.js"></script>
      <script src="include/assets/js/respond.min.js"></script>
    <![endif]-->

  </head>

 
