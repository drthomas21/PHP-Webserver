<?php
namespace App\Webserver;
class WebServerApp extends \Framework\App\Threaded\BaseThreadedApp {
    private $socket;
	private $childProcs;
	private $run;
    private $time_limit;

	public function init() {
        $port = $this->config->port;
        $this->time_limit = property_exists($this->config,"time_limit") ? intval($this->config->time_limit) : 0;
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
                    \Framework\Utility\Logger::logMessage("Starting Proc {$pid}");
					$this->childProcs[] = array(
                        "pid" => $pid,
                        "timestamp" => time()
                    );

					if(count($this->childProcs) > 0) {
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
