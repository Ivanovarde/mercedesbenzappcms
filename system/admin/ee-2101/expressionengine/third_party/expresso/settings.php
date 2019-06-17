<?php
// define settings
$this->extra_link_modules = array('Navee', 'Structure', 'Pages');
$this->default_settings = array(
	'license_number' => '',
	'autoGrow_maxHeight' => '600',
	'toolbar_icons' => array('List Block', 'Justify Block', 'File', 'Maximize'),
	'headers' => array(),
	'startupOutlineBlocks' => '',
	// IVANO: Comento esto porque #123 y #125 son las llaves. Al parecer, por estar aca no parsea las variables globales
	'entities_additional' => '', //'#39,#123,#125',
	// IVANO
	// IVANO: Agregue esta variable para poder setearla en el CP. Tambien se agrego debajo en $this->field_settings
	'bodyClass' => '',  
	// IVANO: Ademas de setear esta variable, 
	// hay que setearla en  global_settings.php dentro de la extension expresso
	// hay que agregar el texto de ayuda en la pagina expresso_lang.php de cada idioma
	'contentsCss' => '',
	'custom_toolbar' => '',
	// IVANO
	'lists_toolbar' => '',
	'text_toolbar'=> '',
	// IVANO
	'styles' => ''
);
$this->field_settings = array('autoGrow_maxHeight',/*IVANO*/'bodyClass', 'lists_toolbar', 'text_toolbar'/*IVANO*/, 'contentsCss', 'startupOutlineBlocks', 'entities_additional');
$this->all_toolbar_icons = array('Subscript', 'Superscript', 'List Block', 'Indent', 'Blockquote', 'Justify Block', 'PasteFromWord', 'RemoveFormat', 'Anchor', 'MediaEmbed', 'Flash', 'youtube', 'Table', 'Iframe', 'Maximize', 'ShowBlocks', 'Source');
$this->toolbar_options = array('full' => 'Full', 'simple' => 'Basic', 'light' => 'Light', 'custom' => 'Custom', /*IVANO*/ 'lists' => 'Lists', 'text' => 'Text'/*IVANO*/);
// END
