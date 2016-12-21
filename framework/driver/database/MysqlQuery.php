<?php
namespace Framework\Driver\Database;
class MysqlQuery extends BaseDatabaseQuery {
	private $stmt; 
	private $result;
	function __construct(\mysqli_stmt $stmt) {
		$this->stmt = $stmt;
		$this->result = null;
	}
	
	public function execute():bool {
		$ret = $this->stmt->execute();
		if($ret) {
			$this->result = $this->stmt->get_result();
		} elseif(!empty($this->stmt->error)){
			throw new \Framework\Exception\QueryExecutionException($this->stmt->error,$this->stmt->errno);
		}
		return $ret;
	}
	
	public function getNextRow(int $fetchType = -1) {
		if($this->result == null) {
			throw new \Framework\Exception\NoQueryExecutedException();
		}
		
		$ret = null;
		if($fetchType == self::ARRAY_A) {
			$ret = $this->result->fetch_assoc();
		} elseif ($fetchType ==  self::OBJECT) {
			$ret = $this->result->fetch_object();
		} else {
			$ret = $this->result->fetch_row();
		}
		
		if(!$ret) {
			$this->result = null;
		}
		return $ret;
	}
	
	public function getRows(int $fetchType = -1): array {
		if($this->result == null) {
			throw new \Framework\Exception\NoQueryExecutedException();
		}
		
		$rows = array();
		if($fetchType == self::ARRAY_A) {
			while($row = $this->result->fetch_assoc()) {
				$rows[] = $row;
			}
		} elseif ($fetchType ==  self::OBJECT) {
			while($row = $this->result->fetch_object()) {
				$rows[] = $row;
			}
		} else {
			while($row = $this->result->fetch_row()) {
				$rows[] = $row;
			}
		}
		
		$this->result = null;
		return $rows;		
	}
	
	public function cleanup() {
		if($this->stmt) {
			$this->stmt->close();
		}
	}
}