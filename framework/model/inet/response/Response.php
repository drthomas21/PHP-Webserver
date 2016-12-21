<?php
namespace Framework\Model\Inet\Response;
abstract class Response {
	protected $thread;
	abstract function sendResponse();
	abstract function sendContent(string $content);
	abstract function sendHeaders();
	public function setThread(\Framework\Thread\ChildThread $thread) {
		$this->thread = $thread;
	}
	public function isClientConnected(): bool {
		return $this->thread->isClientConnected();
	}
}
