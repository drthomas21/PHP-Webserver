<?php
namespace Framework\App\Threaded;
abstract class BaseThreaded extends \Framework\App\BaseApp {
	abstract public function run();
}
