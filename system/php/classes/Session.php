<?php
class Session extends ABM{

	public static $inactiveTime;
	public static $sessionExists = 				false;

	public function __construct($id=''){
		$this->readDBFields(Tables::SESSIONS);
		$this->read($id);

	}

	public static function start($timeOut = 20){

		self::$inactiveTime = $timeOut;

		if(isset($_SESSION['start']) ) {
			$session_life = time() - $_SESSION['start'];
			if($session_life > self::$inactiveTime){
				self::end();
			}
		}else{
			session_start();
			self::$sessionExists = true;
		}
		$_SESSION['start'] = time();
	}

	public static function end(){
		self::$sessionExists = false;
		unset($_SESSION['u']);
		unset($_SESSION);
		session_unset();
		session_destroy();
	}

	public static function checkUserSession($id=''){
		Log::l('Session::checkUserSession', $id, false);

		$idU = $id;

		if($idU == '' && isset($_SESSION['u'])){
			$idU = $_SESSION['u']->id;
		}

		//Log::l('',ini_get("session.gc_maxlifetime"));
		Log::l('Session checkUserSession $idU', $idU, false);
		Log::l('Session checkUserSession $_SESSION["u"]', (isset($_SESSION['u']) ? $_SESSION['u'] : ''), false);

		if($idU != ''){

			Settings::set_globals('session_username', $_SESSION['u']->username);
			Settings::set_globals('no_session_class', '');

			return true;
		} else{

			Settings::set_globals('session_username', '');
			Settings::set_globals('no_session_class', 'session-hidden');

			return false;
		}

	}

	public static function checkSessionActive(){

		Log::l('Session::checkSessionActive', self::$sessionExists, false);

		if(self::$sessionExists){
			return true;
		}else{
			return false;
		}
	}

}
?>
