<?php
namespace Framework\Driver\Shared;

class Semaphore extends BaseSharedMem {
	private $sizeOfMem;
	private $key;
	private $semId;
	private $MemObj;
	
	protected function __construct() {
		$this->key = ftok(__FILE__, 't');
		$this->sizeOfMem = 0;
		
		$this->MemObj = new \stdClass();
		$this->MemObj->data = array();
	}
	
	protected function init(int $sizeOfMem = 0) {
		$this->sizeOfMem = $sizeOfMem;
		$this->semId = shmop_open($this->key, "c", 0644, $this->sizeOfMem);		
		$this->pushMemObject();		
		shmop_write($this->semId, 0, 0);
	}
	
	public function cleanup() {
		shmop_delete($this->semId);
	}
	
	/**
	 * Adds null value to the end of string
	 * @param string $value
	 * @return string
	 */
	private function strToMem(string $value):string {
		return "$value\0";
	}
	
	/**
	 * Parse the string, looking for the null value 
	 * @param string $value
	 * @return string
	 */
	private function memToStr(string $value):string {
		$i = strpos($value, "\0");
		if ($i === false) {
			return $value;
		}
		$result =  substr($value, 0, $i);
		return $result;
	}
	
	/**
	 * Updates $MemObj
	 */
	protected function pullMemObject() {
		$this->MemObj = unserialize($this->memToStr(shmop_read($this->semId,1,$this->sizeOfMem-1)));
	}
	
	/**
	 * Saves $MemObj
	 * @return bool
	 */
	protected function pushMemObject():bool {
		$value = $this->strToMem(serialize($this->MemObj));
		return shmop_write($this->semId, $value, 1) > 0;
	}
	
	/**
	 * Non-blocking attempt to set lock
	 * @return bool
	 */
	protected function getLock(): bool {
		$lock = shmop_read($this->semId, 0, 1);
		if($lock == 0) {
			return shmop_write($this->semId, 1, 0) > 0;
		} else {
			return false;
		}
	}
	
	/**
	 * Release lock
	 * @return bool
	 */
	protected function releaseLock():bool {
		return shmop_write($this->semId, 0, 0) > 0;		
	}
	
	/**
	 * Returns the value for the given key
	 * @param string $name
	 * @param float &$token
	 * @return multi
	 */
	public function get(string $name,float &$token) {
		$this->pullMemObject();
		
		$token = 0;
		$value = null;
		if(array_key_exists($name, $this->MemObj->data)) {
			$token = $this->MemObj->data[$name]->token;
			$value = $this->MemObj->data[$name]->value;
		}
		
		return $value;
	}
	
	/**
	 * Saves $data to the given key
	 * @param string $name
	 * @param mulit &$data
	 * @param float &$token
	 * @return bool
	 */
	public function set(string $name, &$data, float &$token, int $null = 0):bool {
		while(!$this->getLock()) {
			usleep(1);
		}
		$this->pullMemObject();
		
		if($token == 0 && !array_key_exists($name, $this->MemObj->data)) {
			$this->MemObj->data[$name] = new \stdClass();
			$this->MemObj->data[$name]->token = date('U');
			$this->MemObj->data[$name]->value = $data;			
		} elseif (array_key_exists($name, $this->MemObj->data) && $token == $this->MemObj->data[$name]->token) {
			$this->MemObj->data[$name]->token = date('U');
			$this->MemObj->data[$name]->value = $data;
		} else {
			throw new \Framework\Exception\ExpiredTokenException($name);
		}
		
		$ret = $this->pushMemObject();
		$this->releaseLock();
		
		if($ret) {
			$token = $this->MemObj->data[$name]->token;
		}
		
		return $ret;
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function delete(string $name):bool {
		while(!$this->getLock()) {
			usleep(1);
		}
		$this->pullMemObject();
		if(array_key_exists($name, $this->MemObj->data)) {
			unset($this->MemObj->data[$name]);
		}
		$ret = $this->pushMemObject();
		$this->releaseLock();
		return $ret;
	}
	
	public function config(\stdClass $config) {
		$size = 0;
		if(property_exists($config, "size")) {			
			for($i = 0; $i < strlen($config->size); $i++) {
				$char = substr($config->size,$i,1);
				if(is_int($char)) {
					$size = $size * 10 + intval($char);
				} else {
					switch ($char) {
						case 'G':
						case 'g':
							$szie *= 1000;
						case 'M':
						case 'm':
							$size *= 1000;
						case 'K':
						case 'k':
							$size *= 1000;
						break;
							
					}
					
					switch ($char) {
						case 'b':
							$size /= 8;
							break;
								
					}
				}
			}
		}
		
		$size = intval($size);
		
		if($size == 0) {
			$size = 25 * 1000; //25KB
		}
		
		$this->init($size);
	}
}