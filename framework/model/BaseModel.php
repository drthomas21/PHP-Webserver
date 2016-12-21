<?php
namespace Framework\Model;

abstract class BaseModel {	
	protected $Database;
	protected $Memcached;
	
	public function __construct(\Framework\Driver\Database\BaseDatabaseDriver $Database = null, \Framework\Driver\Memcached\BaseMemcachedDriver $Memcached = null) {
		$this->Database = $Database;
		$this->Memcached = $Memcached;
	}
}