<?php
namespace App\Webserver_V2;
class ChildThread {
	const LOOP_LIMIT = 5;
	const MAX_BUFFER = 2048; //Bytes
	const TIME_LIMIT = 0.005;

	private $id;
	private $shm_key;
	private $Memory;
	private $client;
	private $childProcs;
	private $isConnected;
	private $isWorking;

	public function __construct() {
		$client = null;
		$this->childProcs = array();
		$this->isConnected = false;
		$this->isWorking = false;
	}

	public function run($client,$scoket) {
		$this->isWorking = true;

		$pid = pcntl_fork();
		if($pid == -1) {
			\Framework\Utility\Logger::logCritical("Failed to fork");
			exit(EXIT_FORKING);
		} elseif($pid == 0) {
			socket_close($scoket);
			global $isChild;
			$isChild = true;
			try {
				$this->processClient($client);
			} catch(\Exception $e) {
				\Framework\Utility\Logger::logCritical($e->getMessage());
			}
			exit(EXIT_CHILD_OKAY);
		} else {
			\Framework\Utility\Logger::logMessage("Starting Proc {$pid}");
			$this->childProcs[] = array(
				"pid" => $pid,
				"timestamp" => time()
			);

			while(count($this->childProcs) > 0) {
				foreach($this->childProcs as $idx => $info) {
					$pid = $info['pid'];

					//Check if the proc is finished
					pcntl_waitpid($pid,$status,WNOHANG);
					if($status == -1 || $status > 0) {
						\Framework\Utility\Logger::logMessage("Finished Proc {$pid}");
						unset($this->childProcs[$idx]);
					} elseif($this->time_limit > 0 && time() - $info['timestamp'] > $this->time_limit) {
						\Framework\Utility\Logger::logCritical("Proc {$pid} has exceeded the set time limit");
						posix_kill($pid);
						unset($this->childProcs[$idx]);
					}
				}
			}
		}

		$this->isWorking = false;
	}

	protected function processClient($client,int $loop = 0) {
		$loop++;
		$Request = null;
		$Response = null;

		$start = microtime(true);
		$raw = "";
		do {
			$bytes = socket_recv($client,$buff,self::MAX_BUFFER,MSG_DONTWAIT);
			if($bytes && $bytes > 0) {
				$raw .= $buff;
				$start = microtime(true);
			}
			usleep(1);
		} while(microtime(true) - $start < self::TIME_LIMIT && strlen($raw) < self::MAX_BUFFER);

		if(strlen($raw) > 0) {
			preg_match("/(GET|POST|PUT|DELETE|OPTIONS) ([A-Za-z0-9\-\.\_\~\:\/\?\#\[\]\@\!\S\&\'\(\)\*\+\,\;\=]+) (HTTP\/[0-9\.]+)/",$raw,$matches);
			if(!empty($matches)) {
				$this->isConnected = true;

				//setup meta data
				$Request = \Framework\Factory\Inet\RequestBuilder::buildRequest($matches[3]);
				$Request->method = constant(get_class($Request)."::{$matches[1]}");
				$Request->path = $matches[2];
				socket_getpeername($client,$address,$port);
				$Request->client = new \stdClass();
				$Request->client->address = $address;
				$Request->client->port = $port;

				$lines = preg_split("/[\r\n]{1,2}/",$raw);
				$head = array();
				$body = array();

				//process head
				$props = array_keys(get_object_vars($Request));
				while(!empty($lines)) {
					$line = array_shift($lines);
					if(empty($line)) break;

					$parts = explode(":",$line,2);
					$head = str_replace('-','_',strtolower($parts[0]));
					if(in_array($head,$props)) {
						$Request->$head = trim($parts[1]);
					}
				}

				//process body
				$Request->data = implode("\r\n",$lines);

				try {
					$Response = \Framework\Provider\Routing\RoutingProvider::processRequest($Request,$this);
				} catch(\Exception $e) {
					$Response = \Framework\Factory\Inet\ResponseBuilder::buildResponse("HTTP/1.1");
					\Framework\Utility\Logger::logCritical($e->getMessage());
					$Response->setStatus(500);
				}
			} else {
				$Response = \Framework\Factory\Inet\ResponseBuilder::buildResponse("HTTP/1.1");
				$Response->setAsForbiden();
			}
		} elseif($loop < self::LOOP_LIMIT) {
			usleep(1);
			return $this->processClient($client,$loop);
		}

		if($Response != null) {
			$Response->sendResponse($client);
		}

		socket_close($client);
	}

	public function isWorking(): bool {
		return $this->isWorking;
	}

	public function writeToSocket(string $content) {
		if($this->isClientConnected()) {
			$num = socket_write($client,$content,strlen($content));
			if($num === false) {
				$this->isConnected = false;
			}
		}
	}

	public function isClientConnected(): bool {
		return $this->isConnected;
	}
}
