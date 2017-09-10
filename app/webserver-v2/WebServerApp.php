<?php
namespace App\Webserver_V2;
class WebServerApp extends \Framework\App\Threaded\BaseThreadedApp {
    private $socket;
	private $childProcs;
    private $childThreads;
    private $jobs;
	private $run;
    private $time_limit;

	public function init() {
        $port = $this->config->port;
        $this->time_limit = property_exists($this->config,"time_limit") ? intval($this->config->time_limit) : 0;
		$this->socket = @socket_create_listen($port,SOMAXCONN);
		if(!$this->socket) {
			$num = socket_last_error();
			$msg = socket_strerror($num);

			throw new \App\Webserver_V2\Exception\ServerSocketException($msg,$num);
		}
		socket_set_nonblock($this->socket);
        $this->childThreads = array();
        $this->jobs = array();
        for($i = 0; $i < $this->config->thread_limit; $i++) {
            $this->childThreads[] = new ChildThread();
        }
	}

    public function getAppName(): string {
        return __CLASS__;
    }

    public function run() {
        $this->run = true;
		do {
            $client = socket_accept($this->socket);
            if(!empty($this->jobs)) {
                foreach($this->childThreads as $Thread) {
                    if(!$Thread->isWorking() && !empty($this->jobs)) {
                        $job = array_pop($this->jobs);
                        $Thread->run($job,$this->socket);
                    }
                }
            }

			if($client) {
                $this->jobs[] = $client;
			}
            usleep(1);
		} while($this->run);

		socket_close($this->socket);
    }

    public function shutdown() {
        $this->run = false;
    }
}
