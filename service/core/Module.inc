<?php
namespace Core;
abstract class Module {
	protected $moduleDirectory;
	protected $avaliableModules = array();
	
	public final function loadModule($moduleName) {
		if(array_key_exists($moduleName, $this->avaliableModules)) {
			require_once($this->moduleDirectory."/{$this->avaliableModules[$moduleName]}");
		}
	}
}