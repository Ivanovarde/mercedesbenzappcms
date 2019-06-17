<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default config
 *
 * @package		Default module
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

//contants
if ( ! defined('AUTO_FIELD_NAME'))
{
	define('AUTO_FIELD_NAME', 'Auto Field');
	define('AUTO_FIELD_CLASS', 'Auto_field');
	define('AUTO_FIELD_MAP', 'auto_field');
	define('AUTO_FIELD_VERSION', '1.1.1');
	define('AUTO_FIELD_DESCRIPTION', 'Create a special field that hold a combination of other fields.');
	define('AUTO_FIELD_DOCS', '');
	define('AUTO_FIELD_DEVOTEE', '');
	define('AUTO_FIELD_AUTHOR', 'Rein de Vries');
	define('AUTO_FIELD_DEBUG', true);
	define('AUTO_FIELD_STATS_URL', 'http://reinos.nl/index.php/module_stats_api/v1'); 
}

//configs
$config['name'] = AUTO_FIELD_NAME;
$config['version'] = AUTO_FIELD_VERSION;

//load compat file
require_once(PATH_THIRD.AUTO_FIELD_MAP.'/compat.php');

/* End of file config.php */
/* Location: /system/expressionengine/third_party/auto_field/config.php */