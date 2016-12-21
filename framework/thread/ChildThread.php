<?php
namespace Framework\Thread;
class ChildThread {
	const LOOP_LIMIT = 20;
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
		$bytes = socket_recv($this->client,$buff,2048,MSG_DONTWAIT);
		if($bytes !== false) {
			$lines = preg_split("/[\r\n]+/",$buff);
			$head = array_shift($lines);
			preg_match("/(GET|POST|PUT|DELETE|OPTIONS) ([A-Za-z0-9\-\.\_\~\:\/\?\#\[\]\@\!\S\&\'\(\)\*\+\,\;\=]+) (HTTP\/[0-9\.]+)/",$head,$matches);
			if(!empty($matches)) {
				$this->isConnected = true;
				$Request = \Framework\Factory\Inet\RequestBuilder::buildRequest($matches[3]);
				$Request->method = constant(get_class($Request)."::{$matches[1]}");
				$Request->path = $matches[2];

				$props = array_keys(get_object_vars($Request));
				foreach($lines as $line) {
					$parts = explode(":",$line,2);
					$head = str_replace('-','_',strtolower($parts[0]));
					if(in_array($head,$props)) {
						$Request->$head = trim($parts[1]);
					}
				}

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
		} elseif($bytes === false) {
			$num = socket_last_error($this->client);
			$msg = socket_strerror($num);

			if($num != 11 || $loop > self::LOOP_LIMIT) {
				throw new \Framework\Exception\ServerSocketException($msg,$num);
			} else {
				usleep(1);
				return $this->run($loop);
			}
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
