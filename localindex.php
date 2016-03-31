<?php
// Define application environment
ini_set('open_basedir','');
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'live'));

define('HOME_DIR', __DIR__);
define('IS_SECURE', false);

if(IS_SECURE)
{
    define('APPLICATION_URL', 'http://'.$_SERVER['HTTP_HOST'].'/mtg/zend');
    define('HTTP_PATH', 'http://'.$_SERVER['HTTP_HOST']);
}
else
{
    $url = (APPLICATION_ENV === 'live')
        ? 'http://'.$_SERVER['HTTP_HOST'].'/mtg/zend'
        : 'http://'.$_SERVER['HTTP_HOST'];

    define('APPLICATION_URL', $url);
    define('HTTP_PATH', 'http://'.$_SERVER['HTTP_HOST']);
}

if(APPLICATION_ENV === 'development') {
    error_reporting(E_ALL ^ E_DEPRECATED);
}

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
defined('LIBRARY_PATH')
|| define('LIBRARY_PATH', realpath(APPLICATION_PATH . '/../../zf1/library'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIBRARY_PATH),
    realpath(APPLICATION_PATH . '/modules'),
    realpath(HOME_DIR . '/libraries'),
    get_include_path(),
)));
#var_dump(is_readable(realpath(HOME_DIR . '/libraries/Kardigan')));
#var_dump(get_include_path());die;

/** Zend_Application */
//This will call KD.PHP of custom library which is used for overwrite default bootstrat
require_once 'KD/KD.php';
require 'Kardigan/Utilities/Debug.php';
// Create application, bootstrap, and run
$application = new KD(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);



$application->bootstrap()
    ->run();