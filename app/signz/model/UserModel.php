<?php
namespace App\Signz\Model;
class UserModel extends \Framework\Model\BaseModel {
	const TABLE = "users";	
	
	public static function sanitizeUsername(string $value): string {
		return preg_replace("/[^A-Za-z0-9_]/","",$value);
	}
	
	public function getById(int $id): \App\Signz\Record\User {
		$id = intval($id);
		$key = __NAMESPACE__.'\\'.__CLASS__.':ID_'.$id;
	
		$User = $this->Memcached->get($key);
		if(!$User) {
			try {
				$Stmt = $this->Database->prepare("SELECT * FROM ".self::TABLE." WHERE id = ? LIMIT 1", "i", array($id));
				if($Stmt->execute()) {
					$Obj = $Stmt->getNextRow($Stmt::OBJECT);
					if(!$Obj) {
						$User = null;
					} else {
						$User = new \App\Signz\Record\User($Obj);
					}
					$this->Memcached->set($key,$User);
				}
				$Stmt->close();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				$User = null;
			} catch(\Framework\Exception\QueryExecutionException $e) {
				$User = null;
			}
		}
	
		return $User;
	}
	
	public function getByUsername(string $username): \App\Signz\Record\User {
		$username = self::sanitizeUsername($username);
		$key = __NAMESPACE__.'\\'.__CLASS__.':USERNAME_'.$username;
		
		$User = null;		
		$id = intval($this->Memcached->get($key));
		if(!$id) {
			try {
				$Stmt = $this->Database->prepare("SELECT * FROM ".self::TABLE." WHERE username = ? LIMIT 1", "s", array($username));
				if($Stmt->execute()) {
					$Obj = $Stmt->getNextRow($Stmt::OBJECT);
					if(!$Obj) {
						$User = null;
					} else {
						$User = new \App\Signz\Record\User($Obj);
						$this->Memcached->set($key,$User->id);
			
						$key = __NAMESPACE__.'\\'.__CLASS__.':ID_'.$id;
						$this->Memcached->set($key, $User);
					}
				}
				$Stmt->close();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				$User = null;
			} catch(\Framework\Exception\QueryExecutionException $e) {
				$User = null;
			}
		} else {
			$User = $this->getById($id);
		}
		
		return $User;
	}
	
	public function updateUser(\App\Signz\Record\User $User):bool {
		$ret = true;
		$key = __NAMESPACE__.'\\'.__CLASS__.':USERNAME_'.$User->username;
		$ret = $ret && $this->Memcached->set($key, $User->id);
		
		$key = __NAMESPACE__.'\\'.__CLASS__.':ID_'.$User->id;
		$ret = $ret && $this->Memcached->set($key, $User);
		
		try {
			$values = array();
			$types = array();
			$data = get_object_vars($User);
			foreach($data as $param => $value) {
				$values[] = "{$param} = ?";
				$types[] = is_int($value) || is_bool($value) ? 'i' : (is_double($value) ? 'd' : 's');
			}
			$Stmt = $this->Database->prepare("UPDATE " . self::TABLE ." SET " . implode(', ',$values) . " WHERE id = " . $User->id, implode('',$types), array_values($data));
			$ret = $ret && $Stmt->execute();
			$Stmt->close();
		} catch(\Framework\Exception\NoQueryExecutedException $e) {
			$ret = false;
		} catch(\Framework\Exception\QueryExecutionException $e) {
			$ret = false;
		}
		
		return $ret;
	}
	
	public function getNewToken(\App\Signz\Record\User $User, \Framework\Model\Inet\Request\Request $Request):string {
		$token = sha1(date('Y-m-d').$Request->client->address);
		if($User->token != $token) {
			$User->token = $token;
			$this->updateUser($User);
		}
		
		return $token;		
	}
}