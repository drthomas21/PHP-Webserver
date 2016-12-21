<?php
namespace Framework\Model\Inet\Request;
abstract class Request {
	const GET = "GET";
	const PUT = "PUT";
	const POST = "POST";
	const DELETE = "DELETE";
	const OPTIONS = "OPTIONS";
	
	var $method;
	var $host;
	var $path;
	var $user_agent;
	var $cookies;
	var $data;
	var $token;
	var $content_type;
	var $client;
}