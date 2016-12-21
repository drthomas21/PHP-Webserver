<?php
namespace App\Signz\Controller;
class AuthController extends \Framework\Controller\BaseController {
	const AUTH_MSG_UNAVAILABLE = "Authenication is unavailable for the moment";
	const AUTH_MSG_MISSING_ARGUMENTS = "Request is missing required fields";
	const AUTH_MSG_FAILURE = "Invalid username/password";
	const AUTH_MSG_INVALID_TOKEN = "Invalid token";
	const AUTH_MSG_INVALID_USERNAME = "Invalid Username";
	const AUTH_MSG_INVALID_EMAIL = "Invalid Email";
	const AUTH_MSG_ACCOUNT_EXISTS = "Username/email is already registered";
	
	protected $Auth;
	protected $UserModel;
	
	protected function init() {
		$this->Auth = new \App\Signz\Model\AuthModel($this->app->Database);
		$this->UserModel = new \App\Signz\Model\UserModel($this->app->Database,$this->app->Memcached);
	}
	
	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTION) {
			$this->app->setHeader("allow",$Request::POST.",".$Request::PUT.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}
		
		elseif($Request->method == $Request::PUT) {
			if(preg_match("/\/login\/?$/",$Request->path)) {
				if($this->decrypt("", $Request)) {
					$this->login($Request);
				}
			}
			elseif(preg_match("/\/logout\/?$/",$Request->path)) {
				if($this->decrypt("", $Request)) {
					$this->logout($Request);
				}
			}
			else {
				$this->app->setResponse(404);
			}
		}
		
		elseif($Request->method == $Request::POST) {
			if(preg_match("/\/register\/?$/",$Request->path)) {
			if($this->decrypt("", $Request)) {
					$this->register($Request);
				}
			} else {
				$this->app->setResponse(404);
			}
		}
		
		else {
			$this->app->setResponse(403);
		}
	}
	
	protected function decrypt(string $key, \Framework\Model\Inet\Request\Request $Request): bool {
		return true;
	}
	
	protected function login(\Framework\Model\Inet\Request\Request $Request) {
		$info = json_decode($Request->data);		
		$retMessage = "";
		$retToken = "";
		
		if(!$info) {
			$this->app->setResponse(406);
			return;
		}
		
		if(!empty($info->username) && !empty($info->password)) {
			$username = $info->username;
			$password = \App\Signz\Model\AuthModel::hashPassword($info->password);
			
			
			$ret = $this->Auth->authenticateUser($username, $password);
			
			if($ret == $this->Auth::AUTH_RESULT_SUCCESS) {
				//Get Token
				$User = $this->UserModel->getByUsername($username);
				if($User != null) {
					$retToken = $this->UserModel->getNewToken($User, $Request);
				} else {
					$retMessage = self::AUTH_MSG_UNAVAILABLE;
				}
			} else {
				$retMessage = $ret == $this->Auth::AUTH_RESULT_FAILED ? self::AUTH_MSG_UNAVAILABLE:self::AUTH_MSG_FAILURE;
			}
		} else {
			$retMessage = self::AUTH_MSG_MISSING_ARGUMENTS;
		}
		
		$this->app->setData(json_encode(array("login"=>empty($retMessage),"message"=>$retMessage,"token"=>$retToken)));
	}
	
	protected function logout(\Framework\Model\Inet\Request\Request $Request) {
		$info = json_decode($Request->data);		
		$retMessage = "";
		
		if(!$info) {
			$this->app->setResponse(406);
			return;
		}
		
		if(!empty($info->username) && !empty($info->token)) {
			$username = $info->username;
			$token = $info->token;
			
			$User = $this->UserModel->getByUsername($username);
			if($User != null) {
				if($this->UserModel->validateToken($User, $Request, $token)) {
					$User->token = '';
					$this->UserModel->updateUser($User);
				} else {
					$retMessage = self::AUTH_MSG_INVALID_TOKEN;
				}
			} else {
				$retMessage = self::AUTH_MSG_UNAVAILABLE;
			}
		} else {
			$retMessage = self::AUTH_MSG_MISSING_ARGUMENTS;
		}
		
		$this->app->setData(json_encode(array("message"=>$retMessage)));
	}
	
	protected function register(\Framework\Model\Inet\Request\Request $Request) {
		$info = json_decode($Request->data);
		$retMessages = array();
		$retSuccess = true;
		
		if(!$info) {
			$this->app->setResponse(406);
			return;
		}
		
		if(!empty($info->username) && !empty($info->email)) {
			$errors = array();
			if($info->username != $this->UserModel::sanitizeUsername($info->username)) {
				$errors[] = self::AUTH_MSG_INVALID_USERNAME;
				$retSuccess = false;
			}
			
			if(!filter_var($input->email,FILTER_VALIDATE_EMAIL)) {
				$errors[] = self::AUTH_MSG_INVALID_EMAIL;
				$retSuccess = false;
			}
			
			$isUnique = !$this->UserModel->hasUsernameOrEmail($info->username, $info->email);
			if(empty($errors) && $retSuccess && $isUnique) {
				$chars = "0123456789!@#$()*%ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz0123456789!@#$()*%";
				$chars = str_shuffle(str_shuffle(str_shuffle($chars)));
				$info->password = substr($chars,0,10);
				
				if($this->UserModel->createUser($info->username, $info->email, $info->password)) {
					$Email = \App\Signz\Service\Email\SystemEmailService::prepMail($info->email,"noreply@superlunchvote.com","New Account Registration");
					$Email->setBody(\App\Signz\View\ViewManager::getRenderView("email/register",array(
							"username" => $info->username,
							"email" => $info->email,
							"password" => $info->password,
							"siteurl" => $this->app->getWebUrl()
					)));
					if(!$Email->send()) {
						$errors = array_merge($errors,$Email->validate());
						$retSuccess = false;
					} else {
						$retSuccess = true;
					}
				} else {
					$retSuccess = false;
					$retMessages[] = self::AUTH_MSG_UNAVAILABLE;
				}				
			} elseif(!$isUnique) {
				$errors[] = self::AUTH_MSG_ACCOUNT_EXISTS;
				$retSuccess = false;
			}
			
			$retMessages = empty($errors) ? array() : $errors;
		} else {
			$retSuccess = false;
			$retMessages[]= self::AUTH_MSG_MISSING_ARGUMENTS;
		}
		
		$this->app->setData(json_encode(array("success"=>$retSuccess,"errors"=>$retMessages)));
	}
}