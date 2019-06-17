<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * the settings for the module
 *
 * @package		Default module
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

//updates
$this->updates = array(
	//'1.2',
);

//Default Post
$this->default_post = array(
	'license_key'   		=> '',
	'report_date' 			=> time(),
	'report_stats' 			=> true,
);

//overrides
$this->overide_settings = array(
	//'gmaps_icon_dir' => '[theme_dir]images/icons/',
	//'gmaps_icon_url' => '[theme_url]images/icons/',
);

// Backwards-compatibility with pre-2.6 Localize class
$this->format_date_fn = (version_compare(APP_VER, '2.6', '>=')) ? 'format_date' : 'decode_date';


$this->fieldtype_settings = array(
	array(
		'label' => lang('Pattern'),
		'name' => 'pattern',
		'type' => 't', // s=select, m=multiselect t=text
		'def_value' => '{entry_id}-{title}',
		'global' => false, //show on the global settings page
	)

);

/* End of file settings.php  */
/* Location: ./system/expressionengine/third_party/auto_field/settings.php */