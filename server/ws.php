<?php

// VideoViewer server

// this file is supposedly reachable at this endpoint: http://videoviewer.com/ws.php

interface VideoViewerV1 {
	public function index($user_id);
	public function fulfill($session_id, $user_id);
	public function close_session($session_id, $user_id);
}

class VideoViewerV1Impl implements VideoViewerV1 {
	
	private $ip;
	
	function __construct(){
		$this->ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$this->log_ip_action();
	}
	
	function __call($method, $args){
		die();
	}	
	
	public function index($user_id){
		echo $this->html($user_id);
	}
	
	public function create_session($user_id){
		// if the user already has an open session then its session_id is returned
		// otherwise a new session is created and its session_id is returned
		
		// INTERACTION WITH MYSQL 1
		
		echo $session_id;
	}
	
	public function fulfill($session_id, $user_id){
		// gets executed everytime a user watches a video to the end
		
		// INTERACTION WITH MYSQL 2
		
		if ($is_error)
			echo "0";
		else
			echo "1";
	}
	
	public function close_session($session_id, $user_id){
		// gets executed everytime a user finishes watching all videos in a session (20 videos)
		// after executing this method the session is closed, so the user has to open a new session
		// to watch more videos
		// for every closed session the user gets 1$
		
		// INTERACTION WITH MYSQL 3
		
		$this->send_user_money();
	}
	
	private function log_ip_action(){ 
		// log actions made from current IP address (in $this->ip)
	}
	
	private function html($user_id){ 
		// returns generic html from external file
	}
	
	private function send_user_money(){
		// INTERACTION WITH 3rd PARTY SERVER
	}
}

if (!isset($_GET["method"]))
	$method = "index";
else
	$method = $_GET["method"];

$controller = "VideoViewerV1Impl";

$reflectionMethod = new ReflectionMethod($controller, $method);
$reflectionMethod->invokeArgs(new $controller(), $params);
