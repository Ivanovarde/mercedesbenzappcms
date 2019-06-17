<?php
class User extends ABM {

	private $valid_userName = 						false;
	private static $valid_userEmail = 			false;
	private static $valid_userPassword = 		false;
	private $valid_user = 							false;

	public static $logged_user =					'';
	public static $logged_user_group = 			'';
	public static $logged_user_id	= 				false;

	public $current_language_id =					0;
	public $aUserGroups =							array();
	public $aUserStatus =							array();


	function __construct($id=""){
		$this->readDBFields(Tables::USERS);
		$this->read($id);

		Log::l('User construct this', $this, false);
		if($id != ''){
			//$this->user_group = UserGroup::byID($this->group_id);
		}
		//$this->setGroups();

		//$this->setStatus();

		//$this->userLocation = new Location(
				//$this->userCountry->id, $this->userCountry->countryData->name,
				//$this->userData->state_id, $this->userData->state,
				//$this->userData->city_id, $this->userData->city
				//);
	}

	public function setGroups(){
		$this->aUserGroups = UserGroup::all();
	}

	public function getGroups(){
		return $this->aUserGroups;
	}

	public function setStatus(){
		$this->aUserStatus = UserStatus::byFrontend();
	}

	public function getStatus(){
		return $this->aUserStatus;
	}

	public static function getUserStatus(){
		return unserialize(self::$aUserStatus);
	}

	public function save(){
		return parent::save();
	}

	private static function getUser($sql){
		$db = new DB();
		$db->setQuery($sql);
		$record = new User($db->executeValue());
		Log::l('User getUser', $record, false);
		return $record;
	}

	private static function getUsers($sql) {
		$db = new DB();
		$db->setQuery($sql);
		$rsAll = $db->execute();
		$vAll = array();
		foreach($rsAll as $record) {
			 $vAll[] = new User($record["id"]);
		}
		return $vAll;
	}

	public function getAddressAsString(){
		$street = str_replace('///',' ',$this->userData->address);
		return $street;
	}

	public function getAddressAsArray(){
		$street = explode('///',$this->userData->address);
		return $street;
	}

	public function getPhoneNumber($type='mobile'){
		$phones = $this->getPhoneNumbersAsArray();
		return $phones[$type];
	}

	public function getPhoneNumbersAsArray(){
		$phones = array(
			'home'=>$this->userData->homephone,
			'work'=>$this->userData->workphone,
			'mobile'=>$this->userData->mobile
		);

		foreach($phones as $type=>$phone){
			$tmp = explode('///',$phone);
			$phones[$type] = '(' . $tmp[0] . ') ' . $tmp[1];
		}

		return $phones;
	}

	public static function byAuthCode($code){
		$sql = "SELECT id FROM " . Tables::USERS . " WHERE auth_code = '" . $code . "'; ";
		return self::getUser($sql);
	}

	public static function by_reset_code($code){
		$sql = "SELECT id FROM " . Tables::USERS . " WHERE reset_code = '" . $code . "'; ";
		return self::getUser($sql);
	}

	public static function byUsername($username){
		$sql = "SELECT " . Tables::USERS . ".id FROM " . Tables::USERS . " WHERE username = '" . $username . "'; ";
		Log::l('User byUsername', $sql, false);
		return self::getUser($sql);
	}

	public static function byEmail($email){
		$sql = "SELECT " . Tables::USERS . ".id FROM " . Tables::USERS . " WHERE email = '" . $email . "'; ";

		Log::l('User byEmail', $sql, false);

		return self::getUser($sql);
	}

	public static function byBDay(){
		$sql = "SELECT *, CONCAT(nombre,' ',apellido) AS nombreCompleto FROM " . Tables::USERS .
				" WHERE DAYOFMONTH(NOW()) = DAYOFMONTH(nacimiento) " .
				" AND MONTH(NOW()) = MONTH(nacimiento) " .
				//" WHERE nacimiento like '%" . date('m') . "-" . date('d') . "'" .
				" AND usrhab = 1;" ;

		return self::getUsers($sql);
	}

	public static function all($filter=''){
		$sql = "SELECT id FROM " . Tables::USERS . ' ' . $filter;
		return self::getUsers($sql);
	}

	public static function checkUsername($username){
		$db = new DB();
		$sql = "SELECT username FROM " . Tables::USERS . " WHERE username = '" . $username . "'";
		$db->setQuery($sql);
		$usernameExists = $db->executeRecord();
		Log::l("User::checkUsername: ",$usernameExists, false);
		if(!$usernameExists){
			return false;
		}else{
			return true;
		}
	}

	public static function emailExists($userEmail){
		return self::checkUserEmail($userEmail);
	}

	public static function checkUserEmail($userEmail){
		$db = new DB();
		$sql = "SELECT email FROM " . Tables::USERS . " WHERE email = '" . $userEmail . "'";
		$db->setQuery($sql);
		$userEmailExists = $db->executeRecord();
		if(!$userEmailExists){
			return false;
		}else{
			return true;
		}
	}

	public function generatePassword(){
		$minlen = 5;
		$maxlen = 32;
		$pw = new String();
		$password = $pw->random_text(rand($minlen,$maxlen));
		return $password;
	}

	public function valid_userName($username){
		$db = new DB();
		$sql = "SELECT username FROM " . Tables::USERS . " WHERE username = '" . $username . "' ";
		$db->setQuery($sql);

		$this->valid_userName = $db->executeValue();

		Log::l("User->valid_userName",$this->valid_userName, false);

		if(!$this->valid_userName){
			return false;
		}else{
			return $this->valid_userName;
		}
	}

	public static function valid_userEmail($email){
		$db = new DB();
		$sql = "SELECT email FROM " . Tables::USERS . " WHERE email = '" . $email . "' ";
		$db->setQuery($sql);

		self::$valid_userEmail = $db->executeValue();

		Log::l("User->valid_userEmail",self::$valid_userEmail, false);

		if(!self::$valid_userEmail){
			return false;
		}else{
			return true;
		}
	}

	public static function valid_userPassword($password){
		$db = new DB();
		$sql = "SELECT password FROM " . Tables::USERS . " WHERE password = '" . $password . "' ";
		$db->setQuery($sql);

		self::$valid_userPassword = $db->executeValue();

		Log::l("User->valid_userPassword",self::$valid_userPassword, false);

		if(!self::$valid_userPassword){
			return false;
		}else{
			return true;
		}
	}

	public function validateUser(){

		$db = new DB();
		$sql = "SELECT id FROM " .
				Tables::USERS .
				" WHERE username = '" . $this->username . "' " .
				" AND password = '" . $this->password . "' " .
				" AND status = 1 AND is_admin = 1 LIMIT 1;";

		Log::l('User validateUser', $sql, false);

		$this->valid_user = self::getUser($sql);

		Log::l('User validateUser', $this->valid_user, false);

		if($this->valid_user->id != ''){
			return true;
		}else{
			return false;
		}
	}

	public function login(){

		if(!$this->valid_user){
			Log::l('User login valid_user', $this->valid_user, false);
			Session::end();
			return false;
		}else{
			Session::start();
			self::$logged_user = $this->valid_user;
			self::$logged_user_id = $this->valid_user->id;
			self::$logged_user_group = isset($this->valid_user->user_group->group_title) ? $this->valid_user->user_group->group_title : '';

			$_SESSION['u'] = self::$logged_user;
			$_SESSION['logged_user_fields'] = $this->getLoggedUserFieldsArray();

			Log::l('User login SESSION["u"]', $_SESSION['u'], false);

			$this->addSessionLog();

			return true;
		}
	}

	public function logout($url){
		Server::redirect($url);
		exit;
	}

	public function delete(){
		// Elimino la entrada actual
		return self::deleteUser($this->id);
	}

	private function getLoggedUserFieldsArray(){
		$fields = array();

		foreach(self::$logged_user->tableFields as $field){
			Log::l('User getLoggedUserFieldsArray tableFields', $field, false);
			$fields['user_' . $field] = $this->$field;
		}
		$fields['user_initial'] = strtoupper($fields['user_firstname'][0]);

		return $fields;
	}

	public static function deleteUser($id){
		if(!$id){
			return;
		}
		$db = new DB();
		$sql = "DELETE FROM " . Tables::USERS . " WHERE id=" . $id;
		$db->setQuery($sql);
		return $db->executeNonQuery();
	}

	private function addSessionLog(){
		$s = new Session();
		$s->user_id = $this->id;
		$s->ip = Server::getIP();
		$s->datetime = date('Y-m-d H:i:s');
		$s->save();
	}
}

?>
