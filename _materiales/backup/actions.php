<?php
// []/php/actions.php
// Ivano 06/2019

header('Access-Control-Allow-Origin: *'); //for all

include('actions_config.php');


//$m = new Member();
//
//$m->icq = '99';
//$m->member_data->m_field_id_1 = 'Sol Jaz';
//$m->member_data->m_field_id_2 = 'Varde Massiach';
//
//Log::l('1 actios.php ', $m, true);
//
//$m->save();
//
//exit;




$_GET = Functions::arraySanitize($_GET);
$_POST = Functions::arraySanitize($_POST);
$datetime = date('Y-m-d H:i:s');
$hour = date('H');
$relative_path = Server::getRelativeRootPath();

Log::l('actions.php $_POST', $_POST, true);
Log::l('actions.php $_GET', $_GET, true);

foreach($_POST as $k => $v){
	if(isset($_POST[$k])){
		$_POST[$k] = Functions::arrayStriptags($v);
		//${$k} = $_POST[$k];
	}
}

foreach($_GET as $k => $v){
	if(isset($_GET[$k])){
		$_GET[$k] = Functions::arrayStriptags($v);
		//${$k} = $_GET[$k];
	}
}

$p = $_POST;
$g = $_GET;

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';
$action = (($action == '') && (isset($_POST['action']) && $_POST['action'] != '')) ? $_POST['action'] : $action;
$action = strtolower($action);

//$allowed_no_session_actions = array('store');
//
//if(!in_array($action, $allowed_no_session_actions)){
//	$r['status'] = false;
//	$r['url'] = '';
//	$r['msg'] = 'La sesión expiró';
//	$r['expired'] = true;
//	$r['error'] = '';
//
//	echo json_encode($r);
//	exit;
//}


switch($action){

	case 'store':

		//$received_record = json_decode($_POST['stored_leads']);
		//$record_data = $received_record[0];


		Log::l('actions.php $record_data ', $_POST['stored_leads'], false);

		foreach($_POST['stored_leads'] as $lead){
			Log::l('actions.php $lead ', $lead[0], true);
		}

		exit;

		//$chars_to_clean = array("\\", "[", "]", "\"");
		//$record_data = str_replace($chars_to_clean, "", $_GET['stored_leads']);
		//$a_record_data = explode(",", $record_data) ;

		//$element_type = $_POST['element_type'];
		//$record = ucfirst($element_type);
		//$record = new $record();

		//include('actions/' . $element_type . '_' . $action . '.php');

		//$m = new Member(1);
		$m = new Member();

		//Log::l('actions.php', $m, true);

		$nombre = 'Usuario';
		$apellido = 'M Benz App';

		if(isset($record_data->nombre) ){
			$nombre = ucfirst(strtolower($record_data->nombre));
		}

		if(isset($record_data->apellido) ){
			$apellido = ucfirst(strtolower($record_data->apellido));
		}

		$m->username = $nombre . ' ' . $apellido;
		$m->screen_name = $m->username . ' ' . date('Ymdhis');

		$m->group_id = 7;
		$m->salt = '';
		$m->unique_id = ''; //random_string('unique', 16);
		$m->crypt_key = Functions::random_string('encrypt', 16);
		$m->password = md5('mercedesbenzapp2019');
		//$m->email = ;
		$m->join_date = time();

		// Member
		//foreach($_GET as $k => $v){
		//	$m->{$k} = $v;
		//}

		foreach($record_data as $k => $v){
			$m->{$k} = str_replace("|", "", $v);
		}

		// Member fields / Data
		foreach($m->member_fields as $field){
			$fid = $field->m_field_id;
			$fname = $field->m_field_name;
			$fdata_key = 'm_field_id_' . $fid;

			if(isset($record_data->{$fname})){
				$m->member_data->{$fdata_key} = str_replace("|", "", $record_data->{$fname});
			}
		}

		// Comienzo la TRANSACTION
		//mysql_query("BEGIN");

		if(!$m->save()){
			//mysql_query("ROLLBACK");

			$r['status'] = false;
			$r['html'] = '';
			$r['msg'] = 'Fallo de Sincronización.';
			//$r['modal'] = '#modal-edit';
			$r['error'] = '';
			$r['class'] = 'danger';
		}else{

			Log::l('actions.php', $m, true);

			//mysql_query("COMMIT");

			$r['status'] = true;
			$r['html'] = '';
			$r['msg'] = 'Sincronización finalizada con éxito.';
			//$r['modal'] = '#modal-edit';
			$r['error'] = '';
			$r['class'] = 'success';
		}

		echo json_encode($r);
		exit;

	break;

}

exit;
//////////////////////////
