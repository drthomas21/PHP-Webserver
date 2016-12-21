<?php
namespace Framework\App;
interface class BaseApp {
	private function __construct();
	public function setResponse(\Framework\Model\Inet\Response\Response $Response);
	public function setResponseCode(int $code);
	public function setHeader(string $property, string $value);
	public function setData(string $data);
	public function getAppName(): string;
	protected function config(\stdClass $config);
	public function processRequest(\Framework\Model\Inet\Request\Request $Request);
	public function getAppUrl(string $path = ""): string;
	public function getPath(string $path = ""):string;
}
