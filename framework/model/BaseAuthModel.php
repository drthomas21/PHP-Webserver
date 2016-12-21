<?php
namespace Framework\Model;

abstract class BaseAuthModel extends BaseModel{
	const AUTH_RESULT_SUCCESS = 0;    //Auth excuted successfully
	const AUTH_RESULT_USERNAME = 1;   //Auth failed with username
	const AUTH_RESULT_PASSWORD = 2;   //Auth failed with password
	const AUTH_RESULT_FAILED = 3;     //Auth failed epically
	
	abstract public function authenticateUser(string $username, string $passcode): int;
}