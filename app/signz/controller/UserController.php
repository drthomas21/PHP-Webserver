<?php
namespace App\Signz\Controller;
class UserController extends \Framework\Controller\BaseController {
	protected $Auth;
	
	protected function init() {
		$this->Auth = new \App\Signz\Model\AuthModel($this->app->Database);
	}
	
	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTION) {
			$this->app->setHeader("allow",$Request::POST.",".$Request::PUT.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}
		
		elseif($Request->method == $Request::PUT) {
			if(preg_match("/^\/auth\/login\/?/",$Request->path)) {
				if($this->decrypt("", $Request)) {
					$this->login($Request);
				}
			}
			elseif(preg_match("/^\/auth\/logout\/?/",$Request->path)) {
				
			}
			else {
				$this->app->setResponse(404);
			}
		}
		
		elseif($Request->method == $Request::POST) {
			if(preg_match("/^\/auth\/register\/?/",$Request->path)) {
			
			} elseif(preg_match("/^\/auth\/token\/?/",$Request->path)) {
			
			}
			else {
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
		$username = $info->username;
		$password = password_hash($info->password,PASSWORD_BCRYPT);
		
		$ret = $this->Auth->authenticateUser($username, $password);
		if($ret == $this->Auth::AUTH_RESULT_SUCCESS) {
			//Get Token
			$UserModel = new \App\Signz\Model\UserModel($this->app->Database,$this->app->Memcached);
			$token = $UserModel->getNewToken($UserModel->getByUsername($username), $Request);
			
			$this->app->setData(json_encode(array("login"=>true,"message"=>"","token"=>$token)));
		} else {
			$this->app->setData(json_encode(array("login"=>false,"message"=>($ret == $this->Auth::AUTH_RESULT_FAILED ? "Authenication is unavailable for the moment":"Invalid username/password"))));
		}
	}
}