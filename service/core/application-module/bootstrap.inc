<?php
namespace Core; 
define('__APPLICATION_DIR__',__DIR__);
abstract class Application extends Module {
	abstract public function init();
	
}