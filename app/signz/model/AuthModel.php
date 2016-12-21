<?php
namespace App\Signz\Model;
class AuthModel extends \Framework\Model\BaseAuthModel {
	const TABLE = "auth";

	public function authenticateUser(string $username, string $passcode): int {
		$ret = self::AUTH_RESULT_FAILED;
		$UserModel = new \App\Signz\Model\UserModel($this->Database,$this->Memcached);
		$User = $UserModel->getByUsername($username);
		
		if(!$User) {
			$ret = self::AUTH_RESULT_USERNAME;	
		} else if($User->password != $passcode) {
			$ret = self::AUTH_RESULT_PASSWORD;
		} else {
			$ret = self::AUTH_RESULT_SUCCESS;
		}
	
		return $ret;
	}
}