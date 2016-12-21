<?php
namespace Framework\Utility;
class Logger {
	protected static function fileWriter(string $level, string $message) {
		$filename = BASE_DIR.'/logs/'.date("Y-m-d").".log";
		file_put_contents($filename, "[{$level} ".date("H:i:s:")."] {$message}".PHP_EOL, FILE_APPEND);
	}
	public static function logMessage($message) {
		self::fileWriter("NOTICE", $message);
	}
	
	public static function logWarning($message) {
		self::fileWriter("WARNING", $message);
	}
	
	public static function logCritical($message) {
		self::fileWriter("CRITICAL", $message);
	}
}