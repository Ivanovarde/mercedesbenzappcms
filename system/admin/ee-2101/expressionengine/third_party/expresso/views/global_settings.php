<div id="expresso_global_settings">

	<div id="expresso_export_settings">
		<h3><?=lang('export_settings')?></h3>
		<textarea readonly="readonly"><?=$export_settings?></textarea>
		<a href="#" class="expresso_close_settings" />Close</a>
	</div>

	<div id="expresso_import_settings">
		<h3><?=lang('import_settings')?></h3>
		<textarea name="import_settings"></textarea>
		<input type="submit" name="import" class="submit" value="<?=lang('import')?>" />
		<?=lang('or')?> <a href="#" class="expresso_close_settings" /><?=lang('close')?></a>
	</div>


<?php
$docs = array(
	'custom_toolbar' => 'http://docs.ckeditor.com/#!/guide/dev_toolbar',
	// IVANO
	'lists_toolbar' => 'http://docs.ckeditor.com/#!/guide/dev_toolbar',
	'text_toolbar' => 'http://docs.ckeditor.com/#!/guide/dev_toolbar',
	// IVANO
	'styles' => 'http://docs.ckeditor.com/#!/guide/dev_howtos_styles'
);

$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    array('data' => lang('global_settings'), 'width' => '15%'),
    '',
    array('width' => '15%')
);

foreach ($settings as $key => $val)
{
	switch ($key)
	{
		case 'license_number':
			$license = ($license == 'invalid_license') ? '<i class="notice">'.lang($license).'</i>' : '<i>'.lang($license).'</i>';
			$this->table->add_row('<label>'.lang($key).'&nbsp;<strong class="notice">*</strong></label>', $val, $license);
			break;

		case 'uiColor':
		case 'height':
		case 'autoGrow_maxHeight':
			break;

		case 'contentsCss':
		case 'custom_toolbar':
		//IVANO
		case 'lists_toolbar':
		case 'text_toolbar':
		// IVANO
		case 'styles':
			$this->table->add_row('<label>'.lang($key).'</label>'.(isset($docs[$key]) ? ' (<i><a href="'.$docs[$key].'" target="_blank">docs</a></i>)' : ''), $val, '<i><a href="#" class="add_sample_code" id="'.$key.'">'.lang('add_sample_code').'</a></i>');
			break;
			
		// IVANO
			// Ademas de este caso, tambien hay que setear esta variable en la pagina settings.php de expresso
			// hay que agregar el texto de ayuda en la pagina expresso_lang.php de cada idioma
		case 'bodyClass':
			$this->table->add_row('<label>'.lang($key).'</label>', $val, '<i>' . lang('bodyClass_help') . '</i>');
			break;
		// IVANO

		default:
			$this->table->add_row('<label>'.lang($key).'</label>', $val, '');
			break;
	}
}

echo $this->table->generate();
?>

</div>
