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
use \Framework\Thread\ParentThread;
use \Framework\Provider\Routing\RoutingProvider;

// Setup environment
set_time_limit ( 0 );
error_reporting ( E_ALL );
ob_implicit_flush ();

register_shutdown_function ( function () {
	global $ParentThread, $isChild;
	if($ParentThread && (!isset($isChild) || !$isChild)) {
		$ParentThread->shutdown();
		Logger::logMessage("Server shutting down");
	}
} );

function signalHandler($signo) {
	switch ($signo) {
		default:
			Logger::logMessage("Child #{$c} received signal {$signo}");
			break;
	}
}

pcntl_signal(SIGUSR1, "signalHandler");
Logger::logMessage ( "Server starting up" );

//Load Apps
foreach($config->apps as $app) {
	RoutingProvider::registerApp(\Framework\App\BaseAppFactory::getInstance($app->app,$app->config));
}

//Parent Start Socket
$ParentThread = new ParentThread($config->port);
$ParentThread->run();
