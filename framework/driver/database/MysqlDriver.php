<?php
namespace Framework\Driver\Database;
class MysqlDriver extends BaseDatabaseDriver {
	protected $mysql;
	
	protected $host;
	protected $schema;
	protected $username;
	protected $password;
	protected $port;
	
	public function connect(string $host, string $schema, string $username, string $password, int $port = 3306) {
		$this->host = $host;
		$this->schema = $schema;
		$this->username = $username;
		$this->password = $password;
		$this->port = $port;
		
		$this->mysql = new \mysqli($host,$username,$password,$schema,$port);
	}
	
	public function reconnect() {
		$this->mysql = new \mysqli($this->host,$this->username,$this->password,$this->schema,$this->port);
	}
	
	public function query(string $query): BaseDatabaseQuery {
		$stmt = $this->mysql->prepare($query);
		if(!$stmt) {
			throw new \RuntimeException($this->getLastError());
		}
		return new MysqlQuery($stmt);
	}
	
	public function prepare(string $query, string $types, array $arguments): BaseDatabaseQuery {
		$stmt = $this->mysql->prepare($query);
		if(!$stmt) {
			throw new \RuntimeException($this->getLastError());
		}
		$args = array("types" => $types);
		$i = 1;
		foreach($arguments as &$value) {
			$args["var{$i}"] = &$value;
			$i++;
		}
		call_user_func_array(array($stmt,"bind_param"),$args);
		return new MysqlQuery($stmt);
	}
	
	public function isConnected(): bool {
		if($this->mysql->connect_error) {
			return false;
		}
		return $this->mysql->ping();
	}
	
	public function getLastError(): string {
		if($this->mysql->connect_error) {
			return $this->mysqli->connect_error;
		}
		
		return $this->mysql->error;
	}
}