<?php
namespace Framework\Driver\Database;
abstract class BaseDatabaseQuery {
	const OBJECT = 3;
	const ARRAY_A = 2;
	const ARRAY_N = 1;
	
	abstract public function execute():bool;
	abstract public function getNextRow(int $fetchType = -1);
	abstract public function getRows(int $fetchType = -1): array;
	abstract public function cleanup();
}