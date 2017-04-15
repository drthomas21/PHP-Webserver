<?php
namespace App\Webserver;
class ChildThread {
	const LOOP_LIMIT = 5;
	const MAX_BUFFER = 2048;
	private $id;
	private $shm_key;
	private $Memory;
	private $client;
	private $isConnected;

	public function __construct($client) {
		$this->client = $client;
		$this->isConnected = false;
	}

	public function run(int $loop = 0) {
		$loop++;
		$Request = null;
		$Response = null;

		$start = microtime(true);
		$raw = "";
		do {
			$bytes = socket_recv($this->client,$buff,self::MAX_BUFFER,MSG_DONTWAIT);
			if($bytes && $bytes > 0) {
				$raw .= $buff;
				$start = microtime(true);
			}
			usleep(1);
		} while(microtime(true) - $start < 0.01 && strlen($raw) < self::MAX_BUFFER);

		if(strlen($raw) > 0) {
			$lines = preg_split("/[\r\n]{1,2}/",$raw);
			$head = array();
			$body = array();

			$switch = 0;
			foreach($lines as $line) {
				if(empty($line)) {
					$switch = 1;
				}

				if($switch == 0) $head[] = $line;
				else $body[] = $line;
			}

			preg_match("/(GET|POST|PUT|DELETE|OPTIONS) ([A-Za-z0-9\-\.\_\~\:\/\?\#\[\]\@\!\S\&\'\(\)\*\+\,\;\=]+) (HTTP\/[0-9\.]+)/",implode("\r\n",$head),$matches);
			if(!empty($matches)) {
				$this->isConnected = true;
				$Request = \Framework\Factory\Inet\RequestBuilder::buildRequest($matches[3]);
				$Request->method = constant(get_class($Request)."::{$matches[1]}");
				$Request->path = $matches[2];

				$props = array_keys(get_object_vars($Request));
				foreach($head as $line) {
					$parts = explode(":",$line,2);
					$head = str_replace('-','_',strtolower($parts[0]));
					if(in_array($head,$props)) {
						$Request->$head = trim($parts[1]);
					}
				}

				$body = trim(implode("\r\n",$body));
				$Request->data = $body;

				socket_getpeername($this->client,$address,$port);
				$Request->client = new \stdClass();
				$Request->client->address = $address;
				$Request->client->port = $port;

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
			return $this->run($loop);
		}

		if($Response != null) {
			$Response->sendResponse($this->client);
		}

		socket_close($this->client);
	}

	public function writeToSocket(string $content) {
		if($this->isClientConnected()) {
			$num = socket_write($this->client,$content,strlen($content));
			if($num === false) {
				$this->isConnected = false;
			}
		}
	}

	public function isClientConnected(): bool {
		return $this->isConnected;
	}
}
