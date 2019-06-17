<?php
abstract class ABM {
	protected $tableName;
	protected $priKeyField = 								'';
	protected $queryType = 									'';
	protected $tableFields = 								array();
	protected $fieldValues = 								array();
	protected $isNullAllowedField = 						array();
	protected $isAutoIncrementField = 					array();
	protected $fieldDefaultValue = 						array();
	protected $insert_id;

	protected static $searchByDataParameters = 		'';
	protected static $searchByDataJoinParameters = 	'';
	protected static $searchByDataField = 				'';
	protected static $searchByDataIDF = 				'';
	protected static $searchByDataIDL = 				'';
	protected static $cache = 								array();

	public function __get($field) {
		Log::l('ABM __get ($field)', $field, false);
		Log::l('ABM __get (this->fieldVaues)', $this->fieldValues, false);

		if (isset($this->$field)) {

			$f = $this->$field;

			if (is_null($f)) {
				return NULL;
			} else {
				return $this->$field;
			}
		}
		//if (isset($this->fieldValues[$field])) {
		//	if (is_null($this->fieldValues[$field])) {
		//		return NULL;
		//	} else {
		//		return $this->fieldValues[$field];
		//	}
		//}
	}

	public function __set($field, $value) {
		//$this->fieldValues[$field] = $value;
		//Log::l('ABM __set ($field) | $value', $field . ' | ' . $value, false);
		if(!empty($field)){
			$this->{$field} = $value;
			//Log::l('ABM __set $this->{$field}', $this->{$field}. ' | ' . $value, false);
		}
	}

	/**
	 * Reads the fields from the table and assigns each field as
	 * a var with its value
	 *
	 * @param integer $id The id of current record
	 * @param integer $idB The blog id
	 * @param integer $idF The frontend id
	 * @param integer $idL The language id
	 */
	public function read($id, $idB='', $idF='', $idL='') {

		Log::l('ABM read var $id', $id, false);

		if ((!is_null($id)) && ($id != null) && ($id != "") && !is_object($id)) {
			$this->__set($this->priKeyField, $id);
			$db = new DB();

			$b = ($idB != '') ? " AND blog_id = " . $idB : "";
			$f = ($idF != '') ? " AND frontend_id = " . $idF : "";
			$i = ($idL != '') ? " AND language_id = " . $idL : "";

			Log::l('ABM read var $this->priKeyField', $this->priKeyField, false);

			$sql = "SELECT * FROM " . $this->tableName . " WHERE " .
					$this->priKeyField . " = " . $this->getValueDB($this->priKeyField) .
					$b . $f . $i . ";";

			Log::l('ABM read var $sql', $sql, false);

			$db->setQuery($sql);

			$record = $db->executeRecord();

			for($i = 0 ; $i < count($this->tableFields); $i++){
				if(isset($this->tableFields[$i])){
					$this->__set($this->tableFields[$i], $record->{$this->tableFields[$i]});
					Log::l('ABM read ','i = ' . $i . ', campo = ' . $this->tableFields[$i] . ', value = ' . $record->{$this->tableFields[$i]}, false);
				}
			}
		}
	}

	protected function readDBFields($table) {
		$this->tableName = $table;

		Log::l('ABM readDBFields this->tableName', $this->tableName, false);

		if(!isset(self::$cache[$table])){

			$db = new DB();
			$db->setQuery("SHOW FULL COLUMNS FROM " . $table);

			$fields = $db->execute();
			Log::l('ABM readDBFields', $fields, false);

			foreach ($fields as $field) {

				if($field->Key == "PRI"){
					$this->priKeyField = $field->Field;
				}

				// Set NULL fields
				if ($field->Null == "NO" || $field->Null == "") {
					$this->isNullAllowedField[$field->Field] = false;
				}else{
					$this->isNullAllowedField[$field->Field] = true;
				}

				// Set Auto Increment fields
				if ($field->Extra == "auto_increment") {
					$this->isAutoIncrementField[$field->Field] = true;
				}else{
					$this->isAutoIncrementField[$field->Field] = false;
				}

				// Set Default field value
				if ($field->Default != "") {
					$this->fieldDefaultValue[$field->Field] = $field->Default;
				}else{
					$this->fieldDefaultValue[$field->Field] = '';
				}

				// Set an array with the table fields
				$this->tableFields[] = $field->Field;
			}

			self::$cache[$table] = array();
			self::$cache[$table]["priKeyField"] = $this->priKeyField;
			self::$cache[$table]["tableFields"] = $this->tableFields;
			self::$cache[$table]["isNullAllowedField"] = $this->isNullAllowedField;
			self::$cache[$table]["isAutoIncrementField"] = $this->isAutoIncrementField;
			self::$cache[$table]["fieldDefaultValue"] = $this->fieldDefaultValue;

		}else{
			$this->priKeyField = self::$cache[$table]["priKeyField"];
			$this->tableFields = self::$cache[$table]["tableFields"];
			$this->isNullAllowedField = self::$cache[$table]["isNullAllowedField"];
			$this->isAutoIncrementField = self::$cache[$table]["isAutoIncrementField"];
			$this->fieldDefaultValue = self::$cache[$table]["fieldDefaultValue"];
		}

		Log::l('ABM readDBFields $this->priKeyField', $this->priKeyField, false);
		Log::l('ABM readDBFields $this->tableFields', $this->tableFields, false);
		Log::l('ABM readDBFields $this->isNullAllowedField', $this->isNullAllowedField, false);
		Log::l('ABM readDBFields $this->isAutoIncrementField', $this->isAutoIncrementField, false);
		Log::l('ABM readDBFields $this->fieldDefaultValue', $this->fieldDefaultValue, false);
	}

	protected function generateInsert() {
		$sql = "INSERT INTO " . $this->tableName . " (";

		Log::l('ABM generateInsert ', $this->tableFields, false);

		// inser query: fields section
		foreach($this->tableFields as $field) {
			$sql .= $field . ", ";
		}

		$sql = substr($sql, 0, strlen($sql)-2);
		$sql .= ") VALUES (";

		// inser query: values section
		foreach($this->tableFields as $field) {
			if($field == $this->priKeyField || $this->isAutoIncrementField[$field]){
				$sql .= "NULL, ";
			}else{
				$sql .= $this->getvalueDB($field) . ", ";
			}
		}

		$sql = substr($sql, 0, strlen($sql)-2);
		$sql .= ");";

		$this->queryType = 'INSERT';

		return $sql;
	}

	protected function generateUpdate() {
		$sql = "UPDATE " . $this->tableName . " SET ";

		foreach($this->tableFields as $field) {
			$sql .= $field . " = " . $this->getvalueDB($field) . ", ";
		}

		$sql = substr($sql, 0, strlen($sql)-2);

		$sql .= " WHERE " . $this->priKeyField . ' = ' . $this->getvalueDB($this->priKeyField) . ';';

		$this->queryType = '';

		return $sql;
	}

	public function save() {
		$db = new DB();

		Log::l("ABM->save", $this->getKeyValue(), true);

		if (is_null($this->getKeyValue()) || $this->getKeyValue() == "") {
			$sql = $this->generateInsert();
		} else {
			$sql = $this->generateUpdate();
		}
		Log::l('ABM->save', $sql, true);

		$db->setQuery($sql);

		$result = $db->executeNonQuery($this->queryType);
		$this->queryType = '';

		$this->insert_id = $db->getInsertID();

		return ($result === false ? false : true);
	}

	public function isNew() {
		return (is_null($this->__get($this->priKeyField)) == true);
	}

	public function getValueDB($field) {

		$value = $this->__get($field);

		Log::l('ABM getValueDB var $value', $value, false);
		Log::l('ABM getValueDB var $field', $field, false);
		Log::l('ABM getValueDB $this->priKeyField', $this->priKeyField, false);

		//if($field == $this->priKeyField || $this->isAutoIncrementField[$field]){
		//	Log::l('ABM getValueDB: field is pryKey or autoincrement', true, false);
		//	return 'NULL';
		//}

		if (is_string($value)) {
			return "'" . addslashes($value) . "'";
		} else if (is_numeric($value)) {
			return $value;
		} else if (is_null($value)) {

			if ($this->isNullAllowedField[$field]){
				return "NULL";
			}else{
				if($this->fieldDefaultValue[$field] != ''){
					return "'" . $this->fieldDefaultValue[$field]. "'";
				}else{
					return "''";
				}
			}

		} else if (is_object($value)) {
			Log::l('ABM getValueDB var $value', $value, false);
			return $value->getValue($field);
		}
	}

	public function getValue($field) {
		return $this->__get($field);
	}

	public function getInsertId(){
		return $this->insert_id;
	}

	public function getPriKey(){
		return $this->priKeyField;
	}

	public function getTableName(){
		return $this->tableName;
	}

	public function getFields() {
		return $this->tableFields;
	}

	public function getKeyValue() {
		return $this->__get($this->priKeyField);
	}

	public function byData($data){

		Log::l('ABM byData param $data', $data, false);

		$parameters = explode(' ',$data['parameters']);
		Log::l('ABM byData', $data['dataTable'], false);

		switch($data['dataTable']){
			case 'entries':
				$frontend = ' AND ' . Tables::ENTRIES . '.frontend_id = ' . Frontend::$frontendId;
				$language = ' AND ' . Tables::ENTRIESDATA . '.language_id = ' . Frontend::$frontendLangId ;
			break;
			case 'blogs':
				$frontend = ' AND ' . Tables::BLOGS . '.frontend_id = ' . Frontend::$frontendId;
				$language = ' AND ' . Tables::BLOGSDATA . '.language_id = ' . Frontend::$frontendLangId ;
			break;
		}

		foreach($parameters as $p){

			if($p != ''){

				Log::l('ABM byData $p', $p, false);

				switch($p){
					case (strpos($p, 'frontend') !== false):
						$tmpFrontend = explode('=',$p);
						$frontend = ' AND ' . Tables::ENTRIES . '.frontend_id = ' . $tmpFrontend[1];
					break;
					case (strpos($p, 'lang') !== false):
						$tmpLang = explode('=',$p);
						$language = ' AND ' . Tables::ENTRIESDATA . '.language_id = ' . $tmpLang[1];
					break;
					case (strpos($p, 'orderby') !== false):
						$tmpOrderBy = explode('=',$p);
						$orderBy = ' ORDER BY ' . $tmpOrderBy[1];
					break;
					case (strpos($p, 'sort') !== false):
						$tmpSort = explode('=',$p);
						$sort = ' ' . str_replace(array('"','\''), '', strtoupper($tmpSort[1])) . ' ';
					break;
					case (strpos($p, 'limit') !== false):
						$tmpLimit = explode('=',$p);
						$limit = ' LIMIT ' . $tmpLimit[1];
					break;
					case (strpos($p, 'categorytitle') !== false):
						$tmpCategorytitle = explode('=',$p);
						$categorytitle = ' AND ' . Tables::CATEGORIESDATA . '.title = \'' . str_replace(array('"','\''), '', strtoupper($tmpCategorytitle[1])) . '\' ';
					break;
					case (strpos($p, 'groupby') !== false):
						$tmpGroupby = explode('=',$p);
						$groupby = ' GROUP BY  ' . str_replace(array('"','\''), '', strtoupper($tmpGroupby[1])) . ' ';
					break;
					/*case (strpos($p, 'join') !== false):
						//$join .= ''
					break;*/
					case (strpos($p, 'field') !== false):
						$tmpField = explode('=',$p);
						self::$searchByDataField = end($tmpField);
					break;

					default:
						$where .= ' AND ' . $p . ' ';
				}
			}
		}

		$idF = ($tmpFrontend[1]) ? $tmpFrontend[1] : Frontend::$frontendId;
		$idL = ($tmpLang[1]) ? $tmpLang[1] : Frontend::$frontendLangId;

		//self::$searchByDataJoinParameters =
		self::$searchByDataParameters = $frontend . $language . $where . $categorytitle . $orderBy . $sort . $groupby .$limit . " ";
		self::$searchByDataIDF = $idF;
		self::$searchByDataIDL = $idL;
	}
}
?>
