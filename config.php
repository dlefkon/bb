<?php 

ini_set('display_errors','on');
error_reporting(E_ALL);

require('functions.php');

date_default_timezone_set('America/New_York');

ini_set('mysql.trace_mode', '0');

ini_set('session.gc_maxlifetime', 5184000);  

// ini_set('session.save_path', '/tmp');

define('MONGO', false);
define('DB_NAME', 'dlbb');
define('MAX_HOME_LINKS', 7);
define('MAX_ITEM_NAME_CHARS', 25);
define('ITEM_LIMIT', 500);
define('COOKIE_DURATION', 253800000); // 2538000000 = 30000 days	 // 2538000 = 30 days (84600*30)
define('TAG_LIMIT', 500);  

if(checkIfMobile() || isset($_GET['mode']) && $_GET['mode'] == 'mobile') {
	define('MODE', 'mobile');
} else {
	define('MODE', 'full');
}

if(MODE == 'full'){
	define('THEME', 'full_screen');
	define('LAYOUT', 'layout');
} elseif(MODE == 'mobile') {
	define('THEME', 'blue_grey');
	define('LAYOUT', 'mobile_layout');
} else {
	echo 'There was a problem detecting or setting the browser mode.';
	exit;
}
