<?php 
// CONFIG - These control the look and details on your site. Consult documentation for more details.

// GENERAL

// Site URL (no trailing slash)
if(!defined('siteURL'))
    define('siteURL', 'https://y-link.ml');

// Page title for your site
if(!defined('title'))
    define('title', 'Yehuda Link | קיצור קישורים'); 

// The short title of your site, used in the footer and in some sub pages
if(!defined('shortTitle'))
    define('shortTitle', '<a href="https://yehudae.net">Yehuda Eisenberg</a>');

// A description of your site, meta tag.
if(!defined('description'))
    define('description', 'y-link.ml - האתר הרשמי לקיצור קישורים מאת יהודה אייזנברג'); 

// A description of your site, shown on the homepage.
if(!defined('HelloText'))
    define('HelloText', 'Welcome! <br>new design!'); 

// The favicon for your site
if(!defined('favicon'))
    define('favicon', '/include/assets/img/logo.png');

// Logo for your site, displayed on home page
if(!defined('logo'))
    define('logo', '/include/assets/img/logo.png');

// Enables the custom URL field
// true or false
if(!defined('enableCustomURL'))
    define('enableCustomURL', true);

if(!defined('DataBase'))
    define('DataBase', array(
    "ServerName" => "localhost",
    "Username" => "root",
    "Password" => "",
    "DBName" => "link"
));
if(!defined('USE_TOKEN'))
    define('USE_TOKEN', false);
if(!defined('LINK_REGEX'))
    define('LINK_REGEX', '/^[a-zA-Z0-9\x{0590}-\x{05fe}_.\s\-]+$/u');

if(!defined('SITE_DOMAIN'))
    define('SITE_DOMAIN', preg_replace("(^https?://)", "", siteURL));

// Optional
// Set a primary color to be used. Default: #007bff
// Here are some other colors you could try:
// #f44336: red, #9c27b0: purple, #00bcd4: teal, #ff5722: orange
if(!defined('color'))
    define('color', '#007bff');

// Optional
// Set a background image to be used.
// default: unsplash.com random daily photo of the day
// More possibilities of photo embedding from unsplash could be found at: https://source.unsplash.com
// define('backgroundImage', 'https://source.unsplash.com/daily');

// FOOTER

// These are the links in the footer. Add a new link for each new link.
// The array follows a title link structure:
// "TITLE" => "LINK",
$footerLinks = [
    //"About"       => "/",
    "GitHub"        => "https://github.com/YehudaEi/Y-Link",
    "free api"      => "https://yehudaei.github.io/Y-Link/#api-methods",
    "Contact"       => "mailto:yehuda.telegram@gmail.com",
    "Telegram bot"  => "https://t.me/YLinkBot"
    //"Admin"   =>  "/admin"
];

?>
