<?php
// []/php/actions.php
// Ivano 06/2019

header('Access-Control-Allow-Origin: *'); //for all

include('actions_config.php');


$_GET = Functions::array_sanitize($_GET);
$_POST = Functions::array_sanitize($_POST);
$datetime = date('Y-m-d H:i:s');
$hour = date('H');
$relative_path = Server::getRelativeRootPath();

Log::l('actions.php $_POST', $_POST, false);
Log::l('actions.php $_GET', $_GET, false);

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

$allowed_no_session_actions = array('store');

if(!in_array($action, $allowed_no_session_actions)){
	$r['status'] = false;
	$r['url'] = '';
	$r['msg'] = 'La sesión expiró';
	$r['expired'] = true;
	$r['error'] = '';

	echo json_encode($r);
	exit;
}


switch($action){

	case 'store':

		//$received_record = json_decode($_POST['stored_leads']);
		//$record_data = $received_record[0];

		$total_records = count($_POST['stored_leads']);
		$a_failed_records = array();
		$errors = false;
		$c = 0;


		Log::l('actions.php $record_data ', $_POST['stored_leads'], false);

		foreach($_POST['stored_leads'] as $lead){
			Log::l('actions.php $lead[0] ', $lead[0]['nombre'], false);
			Log::l('actions.php json_decode($lead[0]) ', json_encode($lead[0]), false);

			$record_data = json_decode(json_encode($lead[0]));
			Log::l('actions.php r->nombre ', $record_data->nombre, false);

			$m = new Member();

			Log::l('actions.php', $m, false);

			$nombre = 'Usuario';
			$apellido = 'M Benz App ' . date('Ymdhis');

			if(isset($record_data->nombre) ){
				$nombre = ucfirst(strtolower($record_data->nombre));
			}

			if(isset($record_data->apellido) ){
				$apellido = ucfirst(strtolower($record_data->apellido));
			}

			$m->username = $nombre . ' ' . $apellido;
			$m->screen_name = $m->username;

			$m->group_id = 7;
			$m->salt = '';
			$m->unique_id = ''; //random_string('unique', 16);
			$m->crypt_key = Functions::random_string('encrypt', 16);
			$m->password = md5('mercedesbenzapp2019');
			$m->join_date = time();


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

			$m->member_data->m_field_id_20 = 0;

			// Comienzo la TRANSACTION
			//mysql_query("BEGIN");

			if(!$m->save()){
				//mysql_query("ROLLBACK");

				array_push($a_failed_records, $record_data);
				$errors = true;


				$r['status'] = false;
				$r['html'] = '';
				$r['msg'] = 'Fallo en la Sincronización.\nRegistros exportados correctamente: {e}\nRegistros remanentes en memoria: {m}.\nRecuerde volver a exportar en otro momento.';
				$r['error'] = '';
				$r['class'] = 'danger';
				$r['failed_records'] = $a_failed_records;
			}else{

				Log::l('actions.php', $m, false);

				//mysql_query("COMMIT");

				$r['status'] = true;
				$r['html'] = '';
				$r['msg'] = 'Sincronización finalizada con éxito.';
				//$r['modal'] = '#modal-edit';
				$r['error'] = '';
				$r['class'] = 'success';
			}

		}

		if($errors){
			$r['msg'] = str_replace('{e}', ($total_records - count($a_failed_records)), $r['msg']);
			$r['msg'] = str_replace('{m}', count($a_failed_records), $r['msg']);
		}

		echo json_encode($r);
		exit;

	break;

}

exit;
//////////////////////////
