<?php
namespace Framework\Model\Inet\Response;
class HttpResponse extends Response {
	var $version;
	var $status;
	var $statusMessage;
	var $cache_control;
	var $date;
	var $data;
	var $age;
	var $allow;
	var $content_encoding;
	var $expires;
	var $last_modified;
	var $content_type;
	var $headersSent;

	public function __construct(float $ver = 1.1) {
		$this->version = $ver;
		$this->data = "";
		$this->content_type = "text/html; charset=UTF-8";
		$this->cache_control = "no-cache";
		$this->status = 400;
		$this->headersSent = false;
	}

	public function setAsForbiden() {
		$this->status = 403;
		$this->age = -1;
	}

	public function setAsNotFound() {
		$this->status = 404;
		$this->age = -1;
	}

	public function setStatus(int $num) {
		$this->status = $num;

		if($num >= 511 && $this->version == 1.1) {
			$this->status = 511;
			$this->statusMessage = "Network Authentication Required";
		}

		elseif($num >= 507 && $this->version == 1.1) {
			$this->status = 507;
			$this->statusMessage = "Variant Also Negotiates";
		}

		elseif($num >= 506 && $this->version == 1.1) {
			$this->status = 506;
			$this->statusMessage = "Variant Also Negotiates";
		}

		elseif($num >= 505 && $this->version == 1.1) {
			$this->status = 505;
			$this->statusMessage = "HTTP Version Not Supported";
		}

		elseif($num >= 504 && $this->version == 1.1) {
			$this->status = 504;
			$this->statusMessage = "Gateway Timeout";
		}

		elseif($num >= 503 && $this->version >= 0.9) {
			$this->status = 503;
			$this->statusMessage = "Service Unavailable";
		}

		elseif($num >= 502 && $this->version >= 0.9) {
			$this->status = 502;
			$this->statusMessage = "Bad Gateway";
		}

		elseif($num >= 501 && $this->version >= 0.9) {
			$this->status = 501;
			$this->statusMessage = "Not Implemented";
		}

		elseif($num >= 500 && $this->version >= 0.9) {
			$this->status = 500;
			$this->statusMessage = "Internal Server Error";
		}

		elseif($num >= 431 && $this->version >= 1.1) {
			$this->status = 431;
			$this->statusMessage = "Request Header Fields Too Large";
		}

		elseif($num >= 429 && $this->version >= 1.1) {
			$this->status = 429;
			$this->statusMessage = "Too Many Requests";
		}

		elseif($num >= 428 && $this->version >= 1.1) {
			$this->status = 428;
			$this->statusMessage = "Precondition Required";
		}

		elseif($num >= 426 && $this->version >= 1.1) {
			$this->status = 426;
			$this->statusMessage = "Upgrade Required";
		}

		elseif($num >= 421 && $this->version >= 2.0) {
			$this->status = 421;
			$this->statusMessage = "Misdirected Request";
		}

		elseif($num >= 417 && $this->version == 1.1) {
			$this->status = 417;
			$this->statusMessage = "Expectation Failed";
		}

		elseif($num >= 416 && $this->version == 1.1) {
			$this->status = 416;
			$this->statusMessage = "Requested Range Not Satisfiable";
		}

		elseif($num >= 415 && $this->version == 1.1) {
			$this->status = 415;
			$this->statusMessage = "Unsupported Media Type";
		}

		elseif($num >= 414 && $this->version == 1.1) {
			$this->status = 414;
			$this->statusMessage = "URI Too Long";
		}

		elseif($num >= 413 && $this->version == 1.1) {
			$this->status = 413;
			$this->statusMessage = "Payload Too Large";
		}

		elseif($num >= 412 && $this->version == 1.1) {
			$this->status = 412;
			$this->statusMessage = "Precondition Failed";
		}

		elseif($num >= 411 && $this->version == 1.1) {
			$this->status = 411;
			$this->statusMessage = "Length Required";
		}

		elseif($num >= 410 && $this->version == 1.1) {
			$this->status = 410;
			$this->statusMessage = "Gone";
		}

		elseif($num >= 409 && $this->version == 1.1) {
			$this->status = 409;
			$this->statusMessage = "Conflict";
		}

		elseif($num >= 408 && $this->version == 1.1) {
			$this->status = 408;
			$this->statusMessage = "Request Timeout";
		}

		elseif($num >= 407 && $this->version == 1.1) {
			$this->status = 407;
			$this->statusMessage = "Proxy Authentication Required";
		}

		elseif($num >= 406 && $this->version == 1.1) {
			$this->status = 406;
			$this->statusMessage = "Not Acceptable";
		}

		elseif($num >= 405 && $this->version == 1.1) {
			$this->status = 405;
			$this->statusMessage = "Method Not Allowed";
		}

		elseif($num >= 404 && $this->version >= 0.9) {
			$this->status = 404;
			$this->statusMessage = "Not Found";
		}

		elseif($num >= 403 && $this->version >= 0.9) {
			$this->status = 403;
			$this->statusMessage = "Forbidden";
		}

		elseif($num >= 402 && ($this->version == 0.9 || $this->version == 1.1)) {
			$this->status = 402;
			$this->statusMessage = "Payment Required";
		}

		elseif($num >= 401 && $this->version >= 0.9) {
			$this->status = 401;
			$this->statusMessage = "Unauthorized";
		}

		elseif($num >= 400 && $this->version >= 0.9) {
			$this->status = 400;
			$this->statusMessage = "Bad Request";
		}

		elseif($num >= 308 && $this->version == 2.1) {
			$this->status = 308;
			$this->statusMessage = "Permanent Redirect";
		}

		elseif($num >= 307 && $this->version == 1.1) {
			$this->status = 307;
			$this->statusMessage = "Temporary Redirect";
		}

		elseif($num >= 305 && $this->version == 1.1) {
			$this->status = 305;
			$this->statusMessage = "Use Proxy";
		}

		elseif($num >= 304 && $this->version >= 0.9) {
			$this->status = 304;
			$this->statusMessage = "Not Modified";
		}

		elseif($num >= 303 && ($this->version == 0.9 || $this->version == 1.1)) {
			$this->status = 303;
			$this->statusMessage = "See Other";
		}

		elseif($num >= 302 && $this->version >= 0.9) {
			$this->status = 302;
			$this->statusMessage = "Found";
		}

		elseif($num >= 301 && $this->version >= 0.9) {
			$this->status = 301;
			$this->statusMessage = "Moved Permanently";
		}

		elseif($num >= 300 && $this->version >= 1.0) {
			$this->status = 300;
			$this->statusMessage = "Multiple Choice";
		}

		elseif($num >= 206 && $this->version == 1.1) {
			$this->status = 206;
			$this->statusMessage = "Partial Content";
		}

		elseif($num >= 205 && $this->version == 1.1) {
			$this->status = 205;
			$this->statusMessage = "Reset Content";
		}

		elseif($num >= 204 && $this->version >= 0.9) {
			$this->status = 204;
			$this->statusMessage = "No Content";
		}

		elseif($num >= 203 && ($this->version == 0.9 || $this->version == 1.1)) {
			$this->status = 203;
			$this->statusMessage = "Non-Authoritative Information";
		}

		elseif($num >= 202 && $this->version >= 0.9) {
			$this->status = 202;
			$this->statusMessage = "Accepted";
		}

		elseif($num >= 201 && $this->version >= 0.9) {
			$this->status = 201;
			$this->statusMessage = "Created";
		}

		elseif($num >= 200 && $this->version >= 0.9) {
			$this->status = 200;
			$this->statusMessage = "OK";
		}

		elseif($num >= 101 && $this->version == 1.1) {
			$this->status = 101;
			$this->statusMessage = "Switching Protocol";
		}

		elseif($num >= 100 && $this->version == 1.1) {
			$this->status = 100;
			$this->statusMessage = "Continue";
		}

		else {
			throw new \Framework\Exception\InvalidResponseCodeException($num);
		}
	}

	protected function buildResponse():string {
		$content = "";
		if(!$this->headersSent) {
			if(empty($this->statusMessage)) $this->setStatus($this->status);
			$content = "HTTP/{$this->version} {$this->status} {$this->statusMessage}".PHP_EOL;

			foreach(get_object_vars($this) as $param => $value) {
				if(!in_array($param,array('version','status','statusMessage','data','headersSent','thread')) && !empty($value)) {
					$content .= str_replace(" ","-",ucwords(str_replace("_"," ",$param))) .": {$value}".PHP_EOL;
				}
			}

			if(!empty($this->data)) {
				$content .= "Length: ".strlen($this->data) .PHP_EOL . PHP_EOL;
			}
		}

		if(!empty($this->data)) {
			$content .= $this->data;
		}

		$this->headersSent = true;
		return $content;
	}

	protected function buildResponse1():string {
		$content = "";
		if(!$this->headersSent) {
			if(empty($this->statusMessage)) $this->setStatus($this->status);
			$content = "HTTP/{$this->version} {$this->status} {$this->statusMessage}".PHP_EOL;

			foreach(get_object_vars($this) as $param => $value) {
				if(!in_array($param,array('version','status','statusMessage','data','headersSent','thread')) && !empty($value)){
					$content .= str_replace(" ","-",ucwords(str_replace("_"," ",$param))) .": {$value}".PHP_EOL;
				}
			}

			$content .= "Date: " . date('r').PHP_EOL;
			$content .= "Server: ".\App\BaseAppFactory::APPLICATION_NAME."/".\App\BaseAppFactory::APPLICATION_VERSION . PHP_EOL;
		}

		$this->headersSent = true;
		return $content;
	}

	protected function buildResponse1_0():string {
		$content = $this->buildResponse1();

		if(!empty($this->data)) {
			$content .= "Content-Length: ".strlen($this->data) .PHP_EOL . PHP_EOL;
			$content .= $this->data;
		} else {
			$content .= "Content-Length: 0" .PHP_EOL;
		}
		return $content;
	}

	protected function buildResponse1_1():string {
		$content = $this->buildResponse1();

		if(!empty($this->data)) {
			$content .= "Length: ".strlen($this->data) .PHP_EOL . PHP_EOL;
			$content .= $this->data;
		} else {
			$content .= "Content-Length: 0" .PHP_EOL;
		}
		return $content;
	}

	public function isClientConnected(): bool {
		return $this->thread->isClientConnected();
	}

	public function sendResponse() {
		if($this->version == 1.1) {
			$content = $this->buildResponse1_1();
		} elseif($this->version == 1.0) {
			$content = $this->buildResponse1_0();
		} else {
			$content = $this->buildResponse();
		}

		$this->thread->writeToSocket($content);
	}

	public function sendContent(string $content) {
		$this->thread->writeToSocket($content);
	}

	public function sendHeaders() {
		$content = $this->buildResponse() . PHP_EOL;
		$this->headersSent = true;

		$this->thread->writeToSocket($content);
	}
}
