<?php 
spl_autoload_register(function ($class_name) {
	$path = preg_replace_callback('!^(.*)?\\\([A-Za-z0-9_]+)$!',function(array $matches): string {
		return strtolower(str_replace('\\','/',$matches[1])).'/'.$matches[2];
	},$class_name);
	
	
	include_once(__DIR__.'/'.$path.".php");
});