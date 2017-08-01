<?php

// VideoViewer server

// this file is supposedly reachable at this endpoint: http://videoviewer.com/ws.php

interface VideoViewerV1 {
	public function index($user_id);
	public function create_session($user_id);
	public function feed($user_id);
	public function fulfill($session_id, $user_id);
	public function close_session($session_id, $user_id);
	public function fetch_last_user_watched_vids($user_id);
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
	
	private function index($user_id){
		echo $this->html($user_id);
	}
	
	public function feed($user_id){
		// returns JSON to the user with a list of videos to watch
		// every request gives a new list with 200 unique and new items - no video is given more than once
		// timer is in seconds

		// videos generator:
		
		$popular_vid_names = array(
			"so_cute_kittens_adorable",
			"my_pink_unicorns",
			"mariah_carey_forever_and_ever",
			"entangled_vs_frozen",
			"simba_and_nala_and_me",
			"p_s_i_love_you_too",
			"leonardo_dicaprio_incessantly",
			"jumpback_mountain",
			"dancing_lovely_ponies"
		);
		$vid_count = count($popular_vid_names);
		$vid_name_id = 0;

		$vids = array();
		for ($i=0; $i<200; $i++, $vid_name_id++){
			
			$rand_val = mt_rand(0, 2147483647);
			$timer = mt_rand(10, 100);
			
			$vid_id = md5(microtime() . $rand_val . $timer);

			if ($vid_name_id >= $vid_count)
				$vid_name_id = 0;
			
			$vid_url = "http://videoviewer.com/videos/" . $popular_vid_names[$vid_name_id] . "_" . $vid_id . ".mp4";
		
			$vids[] = array("id" => $vid_id, "url" => $vid_url, "timer" => $timer);
		}
		
		header('Content-type: application/json');
		echo json_encode($vids);
	}
	
	public function create_session($user_id){
		// if the user already has an open session then its session_id is used
		// otherwise a new session is created with a new session_id

		// $_POST contains the properties of the first video to watch (received earlier with "feed")
		// INTERACTION WITH MYSQL 1 - fetch saved session_id or create a new session_id
		
		echo $session_id;
	}
	
	public function fulfill($session_id, $user_id){
		// user is requesting "fulfill" everytime it finishes watching a video to the end
		
		$is_error = false;

		// $_POST contains the properties of the next video to watch (received earlier with "feed")
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
		// for every closed session (properly) the user gets 1$
		
		$is_error = false;
		
		// INTERACTION WITH MYSQL 3
		
		if ($is_error)
			die("0");
		
		echo $this->send_user_money() ? "1" : "0";
	}
	
	public function fetch_last_user_watched_vids($user_id){
		// returns all the vids the user watched in the last 24 hours
		
		// INTERACTION WITH MYSQL 4
		
		/*
		e.g.:
			[{"start": "2017-01-01 00:05:01", "id": "1231233123", "url": "dancing_lovely_ponies.mp4", "timer": 43},
			 {"start": "2017-01-01 00:06:25", "id": "5435234554", "url": "so_cute_kittens_adorable.mp4", "timer": 23},
			 {"start": "2017-01-01 00:06:59", "id": "5675675645", "url": "mariah_carey_forever_and_ever.mp4", "timer": 67}]
		
		*/
	}
	
	private function log_ip_action(){ 
		// log actions made from current IP address (in $this->ip)
	}
	
	private function html($user_id){ 
		// returns generic html from external file
		return "<html>bla</html>";
	}
	
	private function send_user_money(){
		$is_error = false;

		// INTERACTION WITH 3rd PARTY SERVER
		
		return !$is_error;
	}
}

/*
url rewrite to allow invoking index like this:
http://videoviewer.com/index/user_id
*/

$uri = $_SERVER["REQUEST_URI"];
$route = explode("/", $uri);

$method = urldecode(array_shift($route));

$params = array();
foreach ($route as $v)
	$params[] = urldecode($v);

$controller = "VideoViewerV1Impl";

$reflectionMethod = new ReflectionMethod($controller, $method);
$reflectionMethod->invokeArgs(new $controller(), $params);





