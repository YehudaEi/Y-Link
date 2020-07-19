<?php

/**
 * The config file
 *
 * @package    Y-Link
 * @copyright  Copyright (c) 2018-2020 Yehuda Eisenberg (https://YehudaE.net)
 * @author     Yehuda Eisenberg
 * @license    AGPL-3.0
 * @version    2.0
 * @link       https://github.com/YehudaEi/Y-Link
 */

define('DB', array('host' => 'localhost', 'username' => 'root', 'password' => '', 'dbname' => 'YLink'));

define('SITE_DOMAIN', "y-link.ml");

define('HTTPS', ( empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off' ? false : true ));

define('SITE_URL', (HTTPS ? "https" : "http")  . '://' . SITE_DOMAIN);

define('PATH_REGEX', '/^[a-zA-Z0-9\x{0590}-\x{05fe}_.\s\-]+$/u');


