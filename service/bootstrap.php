<?php
use Core\System;
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
ini_set('display_errors',FALSE);
set_time_limit(0);

//Constants
define('__CONFIG_DIR__',__DIR__.'/core/config');
define('__CORE_DIR__',__DIR__.'/core');

//Dependency Check
require_once(__CORE_DIR__.'/dependency-checks.inc');

//Setup Environment
$config = parse_ini_file(__CONFIG_DIR__.'/environment.ini');
if(!$config) {
	exit("Cannot parse config file\r\n");
}

//Load System
require_once(__CORE_DIR__.'/exceptions.inc');
require_once(__CORE_DIR__.'/Module.inc');
require_once(__CORE_DIR__.'/System.inc');
$System = System::getInstance($config);

date_default_timezone_set($config['timezone']);
$log =$config['log_directory'].'/'.date("Y-m-d").'.log';
ini_set('log_errors',TRUE);
ini_set("error_log",$log);

error_log("Starting at " . date("Y-m-d H:i:s") . "\r\n");
