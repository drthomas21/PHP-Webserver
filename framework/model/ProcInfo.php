<?php
namespace Framework\Model;

/**
 * 
 * @author drthomas21
 * @param int $id
 * @param int $clients
 * @param int $pendingClientId
 * @param int $status
 */
class ProcInfo {
	var $id;
	var $clients;
	var $pendingClientId;
	var $status;
	
	public function __construct() {
		$this->intId = posix_getpid();
		$this->intClients = 0;
		$this->pendingClientResc = null;
		$this->status = \Framework\Utility\ProcStatus::OKAY;
	}
}