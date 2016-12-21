<?php
global $config;
$errors = array();
foreach(array("pcntl_fork","pcntl_wait","pcntl_waitpid","socket_accept","socket_bind") as $func) {
	if(!function_exists($func)) {
		$errors[] = "Missing function: {$func}";
	}
}

foreach(array("mysqli","Memcached") as $classname) {
	if(!class_exists($classname)) {
		$errors[] = "Missing class: {$classname}";
	}
}

$userData = posix_getpwnam($config->user);
$rootData = posix_getpwnam("root");
if(!$userData) {
	$errors[] = "The user {$config->user} does not exists";
} elseif(posix_getuid() == $rootData['uid']) {
	$errors[] = "You cannot run this service as root";
} elseif(posix_getuid() != $userData['uid']) {
	$errors[] = "You are not running this service under the correct user";
}

if(!empty($errors)) {
	error_log(print_r($errors,true));
	exit(EXIT_REQUIREMENTS);
}