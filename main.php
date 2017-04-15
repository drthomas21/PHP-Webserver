<?php
declare(ticks = 1);

//Globals
define ( "BASE_DIR", __DIR__ );
define ( "EXIT_OKAY",0);
define ( "EXIT_CHILD_OKAY",0);
define ( "EXIT_PARSE_CONFIG",2);
define ( "EXIT_REQUIREMENTS",3);
define ( "EXIT_FORKING",4);

// Read config
$config = json_decode ( file_get_contents ( __DIR__ . '/config.json' ) );
if (! $config) {
	error_log ( "Failed to parse config file" );
	exit ( EXIT_PARSE_CONFIG );
}

// Check requirements
require_once (__DIR__ . '/requirement.inc');

// Load autoload
require_once (__DIR__ . '/autoload.inc');
use \Framework\Utility\Logger;
use \Framework\Provider\Routing\RoutingProvider;

// Setup environment
set_time_limit ( 0 );
error_reporting ( E_ALL );
ob_implicit_flush ();

Logger::logMessage ( "Server starting up" );

//Load Web Apps
if(!empty($config->apps->web)) {
	foreach($config->apps->web as $app) {
		RoutingProvider::registerApp(\Framework\Factory\App\BaseAppFactory::getInstance($app->app,$app->config));
	}
}

//Load Threaded Apps
if(!empty($config->apps->threaded)) {
	foreach($config->apps->threaded as $app) {
		$App = \Framework\Factory\App\BaseAppFactory::getInstance($app->app,$app->config);
		$pid;
        pcntl_fork();
        if($pid == 0) {
            $App->run();
        }
	}
}
