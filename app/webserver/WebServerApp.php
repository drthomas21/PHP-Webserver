<?php
namespace App\Webserver;
class WebServerApp extends \Framework\App\Threaded\BaseThreadedApp {
    private $socket;
	private $pids;
	private $run;
	public function init() {
        $port = $this->config->port;
		$this->socket = @socket_create_listen($port,SOMAXCONN);
		if(!$this->socket) {
			$num = socket_last_error();
			$msg = socket_strerror($num);

			throw new \App\Webserver\Exception\ServerSocketException($msg,$num);
		}
		socket_set_nonblock($this->socket);
	}

    public function getAppName(): string {
        return __CLASS__;
    }

    public function run() {
        $this->run = true;
		do {
			$client = socket_accept($this->socket);
			if($client) {
				$pid = pcntl_fork();
				if($pid == -1) {
					\Framework\Utility\Logger::logCritical("Failed to fork");
					exit(EXIT_FORKING);
				} elseif($pid == 0) {
					socket_close($this->socket);
					global $isChild;
					$isChild = true;
					try {
						$Thread = new ChildThread($client);
						$Thread->run();
					} catch(\Exception $e) {
						\Framework\Utility\Logger::logCritical($e->getMessage());
					}
					exit(EXIT_CHILD_OKAY);
				} else {
					$this->pids[] = $pid;
					if(count($this->pids) > 0) {
						foreach($this->pids as $idx => $pid) {
							pcntl_waitpid($pid,$status,WNOHANG);
					        if($status == -1 || $status > 0) {
					        	unset($this->pids[$idx]);
					        }
						}
					}
				}
			} else {
				usleep(1);
			}
		} while($this->run);

		socket_close($this->socket);
    }

    public function shutdown() {
        $this->run = false;
    }
}
