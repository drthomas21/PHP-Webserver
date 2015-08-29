<?php
namespace Core;
class System {
	private static $Instance = null;
	protected $port = 80;
	protected $indexes = "/^(index|default)\.(php|html|htm)$/";
	
	public static function getInstance(array $config = array()) {
		if(self::$Instance == null) {
			self::$Instance = new self($config);
		}
		
		return self::$Instance;
	}
	
	public static function autrun(array $config = array()) {
		self::getInstance($config);
	}
	
	protected function __construct(array $config = array()) {
		$this->port = !empty($config['port']) ? $config['port'] : $this->port; 
		$this->indexes = !empty($config['indexes']) ? $config['indexes'] : $this->indexes; 
	}
	
	public function loadModules() {
		$folders = scandir(__CORE_DIR__);
		foreach($folders as $foldername) {
			if($foldername == "." || $foldername == ".."){
				continue;
			}
			
			if(preg_match("/-module$",$foldername)) {
				include_once(__CORE_DIR__."/{$foldername}/bootstrap.inc");
			}
		}
	}
}