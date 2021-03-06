<?php

/**
 * A simple PHP Login Script / ADVANCED VERSION
 * For more versions (one-file, minimal, framework-like) visit http://www.php-login.net
 *
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 */

// check for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit('Sorry, this script does not run on a PHP version smaller than 5.3.7 !');
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once('libraries/password_compatibility_library.php');
}
// include the config
require_once('config/config.php');

// include the to-be-used language, english by default. feel free to translate your project and include something else
require_once('translations/en.php');

// include the PHPMailer library
require_once('libraries/PHPMailer.php');

// load the login class
require_once('classes/Login.php');

// include the functions
require_once('tools/functions.php');

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process.
$login = new Login();

if(isMobile()) {
	include("views/mobile.php");
}
else {
	if (new DateTime() < new DateTime("2015-10-16 16:00:00")) {
		//Contest hasn't started yet, less that the 12th of october at 4pm UTC
		include("wait.php");
	}
	// ... ask if we are logged in here:
	else if ($login->isUserLoggedIn() == true) {
		// the user is logged in. you can do whatever you want here.
		// for demonstration purposes, we simply show the "you are logged in" view.
		include("views/panel.php");

	} else {
		// the user is not logged in. you can do whatever you want here.
		// for demonstration purposes, we simply show the "you are not logged in" view.
		include("views/not_logged_in.php");
	}
}

