<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* http://code.tutsplus.com/tutorials/apply-the-dry-principle-to-build-websites-with-expressionengine-2--net-16848 */

//I got MSM to work with this setup by changing $config['site_name'] to $config['site_label'].


/**
* THIS FILE WILL NEED PERMISSIONS SET TO 400 OR SIMILAR SO EE DOESN'T OVERWRITE IT.
*/

/*
|--------------------------------------------------------------------------
| ExpressionEngine Config Items
|--------------------------------------------------------------------------
|
| The following items are for use with ExpressionEngine.  The rest of
| the config items are for use with CodeIgniter.
|
*/

//echo $_SERVER['DOCUMENT_ROOT'] . '/system/php/config_site.php'; exit;

/* Environmental Variables */
//require('../php/config_site.php');
require($_SERVER['DOCUMENT_ROOT'] . '/system/php/config_site.php');

/* General (paths)
-------------------------------------------------------------------*/
//require('../php/config.php');
require($_SERVER['DOCUMENT_ROOT'] . '/system/php/config.php');
