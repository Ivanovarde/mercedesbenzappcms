<?php
class MemberField extends ABM {

	function __construct($id=""){
		$this->readDBFields(Tables::MEMBERFIELDS);
		$this->read($id);

	}

	private static function getMemberField($sql){
		$db = new DB();
		$db->setQuery($sql);
		$record = new MemberField($db->executeValue());
		return $record;
	}

	private static function getMemberFields($sql) {
		$db = new DB();
		$db->setQuery($sql);
		$record_set = $db->execute();
		$a_elements = array();
		Log::l('getMemberField', $record_set, true);
		foreach($record_set as $record) {
			Log::l('getMemberField', $record, false);
			array_push($a_elements, new MemberField($record->m_field_id));
		}
		return $a_elements;
	}

	public static function all($filter=''){
		$sql = "SELECT * FROM " . Tables::MEMBERFIELDS . ' ' . ($filter ? ' WHERE ' . $filter : '') . '; ';
		Log::l(Tables::MEMBERFIELDS, 'MemberField all', false);
		Log::l('MemberField all', $sql, false);
		return self::getMemberFields($sql);
	}

	/*
	public static function byTitle($title){
		$sql = "SELECT id FROM " . Tables::PROMOS . " WHERE title = '" . $title . "'; ";
		Log::loguear('MemberField byTitle', $sql, false);
		return self::getMemberField($sql);
	}
	*/

	/*
	public static function byLanguage($lang, $showInactive=false){
		$status = (!$showInactive) ? ' AND status = 1 ' : '';
		$sql = "SELECT " . Tables::PROMOS . ".id FROM " . Tables::PROMOS .
		" LEFT JOIN " . Tables::LANGUAGES . " ON " . Tables::LANGUAGES . ".id = " . Tables::PROMOS . ".lang_id " .
		" WHERE lang_code = '" . $lang . "' " . $status . ' ' .
		" ORDER BY creation DESC LIMIT 1; ";
		Log::loguear('MemberField byLanguage', $sql, false);
		return self::getMemberField($sql);
	}
	*/


}

