<?php
class EntryData extends ABM {

	function __construct($id="") {
		if($id==''){
			$this->priKeyField = "";
		}else{
			$this->priKeyField = "entry_id";
		}
		$this->readDBFields(Tables::ENTRIESDATA);
		$this->read($id);
	}
	
	private static function getEntryData($sql){
		$bd = new DB();
		$bd->setQuery($sql);
		$record = new EntryData($bd->executeValue());
		return $record;
	}

	private static function getEntryDatas($sql){
		$bd = new DB();
		$bd->setQuery($sql);
		$rsAll = $bd->execute();
		$vAll = array();
		foreach($rsAll as $record) {
			 $vAll[] = new EntryData($record["entry_id"]);
		}
		return $vAll;
	}
	
	public static function byDimensions($w, $h){
		if(empty($w) || empty($h)){
			return false;
		}
		$sql = "SELECT " . Tables::ENTRIESDATA . ".entry_id FROM " . Tables::ENTRIESDATA .
		" WHERE " . 
		" channel_id = 9 AND " .
		" site_id = 1 AND " .
		" field_id_59 = '" . $w . "' AND " . /*field_id_59 width*/
		" field_id_60 = '" . $h . "' "; /*field_id_60 height*/
		
		Log::loguear('EntryData byDimensions', $sql, false);
		return self::getEntryData($sql);
	}

}