<?php
namespace Framework\App\Threaded;
abstract class BaseThreadedApp extends \Framework\App\BaseApp {
	abstract public function run();
}
