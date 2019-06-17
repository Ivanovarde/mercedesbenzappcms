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

/* Environmental Variables */

switch ( $_SERVER['SERVER_NAME'] ) {
    
    // local
	case 'panamfragrances.nmd' :
        $db['expressionengine']['hostname'] = "localhost";
        $db['expressionengine']['username'] = "root";
        $db['expressionengine']['password'] = "root";
        $db['expressionengine']['database'] = "panamfragrances";

        $config['site_short_name'] = 'Pan Am Fragrances Local';

    break;
		
	// Development
	case 'dev.panamericanfragrances.com' :
        $db['expressionengine']['hostname'] = "localhost";
        $db['expressionengine']['username'] = "paname2_dbuser";
        $db['expressionengine']['password'] = "p4n4mfr4g4nc3s";
        $db['expressionengine']['database'] = "paname2_devdb";

        $config['site_short_name'] = 'Pan Am Fragrances Dev';

    break;
    
    // live
    default :
    	$db['expressionengine']['hostname'] = "localhost";
    	$db['expressionengine']['username'] = "paname2_dbuser";
    	$db['expressionengine']['password'] = "p4n4mfr4g4nc3s";
    	$db['expressionengine']['database'] = "paname2_proddb";
        
		$config['site_short_name'] = 'Pan Am Fragrances';
		
    break;
    
}

/* Universal Variables */
$config['config_debug'] = false;
$config['app_version'] = '2.10.1';
$config['license_number'] = '';
$config['license_contact'] = '';
$config['debug'] = '1';
$config['install_lock'] = "";
$config['system_folder'] = "system/admin";
$config['ee_folder'] = 'ee-2101';
$config['is_system_on'] = 'y';
$config['cookie_prefix'] = 'EE_';
$config['allow_extensions'] = 'y';
$config['cache_driver'] = 'file';
$config['cookie_httponly'] = 'y';
$config['enable_db_caching'] = 'n';


/* General
-------------------------------------------------------------------*/
$config['site_name'] = "panamfragrances";
$config['site_label'] = $config['site_short_name'];
$config['site_index'] = "";
$config['site_404'] = 'templates/page_error';
$config['server_protocol'] = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
$config['site_url'] = $config['server_protocol'] . $_SERVER['HTTP_HOST'];
$config['server_path'] = $_SERVER['DOCUMENT_ROOT'];
$config['cp_url'] = $config['site_url'] . "/system/admin-ee.php";
$config['doc_url'] = $config['site_url'] . "/docs/";


/* Universal database connection settings
-------------------------------------------------------------------*/
$active_group = 'expressionengine';
$active_record = TRUE;
$db['expressionengine']['dbdriver'] = "mysql";
$db['expressionengine']['dbprefix'] = "exp_";
$db['expressionengine']['pconnect'] = FALSE;
$db['expressionengine']['swap_pre'] = "exp_";
$db['expressionengine']['db_debug'] = FALSE;
$db['expressionengine']['cache_on'] = FALSE;
$db['expressionengine']['autoinit'] = FALSE;
$db['expressionengine']['char_set'] = "utf8";
$db['expressionengine']['dbcollat'] = "utf8_general_ci";
$db['expressionengine']['cachedir'] = $config['server_path'] . $config['system_folder'] . "/expressionengine/cache/db_cache/";


/* Images
-------------------------------------------------------------------*/
$config['upload_preferences'] = array(
    1 => array(
        'name'        => 'Images / Categories',
        'server_path' => $config['server_path'] . '/images/categories/',
        'url'         => $config['site_url'] . '/images/categories/'
    ),
    2 => array(
        'name'        => 'Images / Pages',
        'server_path' => $config['server_path'] . '/images/pages/',
        'url'         => $config['site_url'] . '/images/pages/'
    ),
    3 => array(
        'name'        => 'Images / Products',
        'server_path' => $config['server_path'] . '/images/products/',
        'url'         => $config['site_url'] . '/images/products/'
    ),
	4 => array(
        'name'        => 'Images / Homepage',
        'server_path' => $config['server_path'] . '/images/homepage/',
        'url'         => $config['site_url'] . '/images/homepage/'
    )
);

/*
|-----------------------------
| CE Image Basic Config Items
|-----------------------------
|
| The following items are for use with CE Image. They are all optional,
| as the defaults in the actual plugin will be used if not specified below.
*/

/*
| The default quality to save a jpg/jpeg file. The quality can range from
| 0 (lowest) to 100 (highest) and should be a whole number.
*/
$config['ce_image_quality'] = 100;
$config['ce_lossless_enabled'] = 'smushit';
$config['ce_lossless_log_output'] = 'y';
$config['ce_image_cache_dir'] = '/images/made/local/';
$config['ce_image_remote_dir'] = '/images/made/remote/';


/* Member directory paths and urls
-------------------------------------------------------------------*/
$config['avatar_url'] = $config['site_url'] . "/system/images/avatars/";
$config['avatar_path'] = $config['server_path'] . "/system/images/avatars/";
$config['photo_url'] = $config['site_url'] . "/system/images/member_photos/";
$config['photo_path'] = $config['server_path'] . "/system/images/member_photos/";
$config['sig_img_url'] = $config['site_url'] . "/system/images/signature_attachments/";
$config['sig_img_path'] = $config['server_path'] . "/system/images/signature_attachments/";
$config['prv_msg_upload_path'] = $config['server_path'] . "/system/images/pm_attachments/";

$config['memberlist_order_by'] = 'join_date'; //'total_posts' / 'total_entries' / 'total_forum_posts' / 'screen_name' / 'total_comments';
$config['memberlist_row_limit'] = '100';
$config['memberlist_sort_order'] = 'desc';
$config['member_theme'] = 'default';
$config['enable_avatars'] = 'y';
$config['allow_avatar_uploads'] = 'y';



/* Misc directory paths and urls
-------------------------------------------------------------------*/
$config['theme_folder_url'] = $config['site_url'] . "/system/themes/" . $config['ee_folder']."/";
$config['theme_folder_path'] = $config['server_path'] . "/system/themes/" . $config['ee_folder']."/";


/* Templates preferences
-------------------------------------------------------------------*/
$config['save_tmpl_files'] = "y";
$config['tmpl_file_basepath'] = $config['server_path'] . "/assets/templates/";
$config['strict_urls'] = "n";
$config['libraree_basepath'] = $config['server_path'] . '/assets/templates/';

$config['cp_theme'] = 'default';
$config['avatar_default_superadmin'] = $config['theme_folder_url'] . 'cp_themes/default/images/avatar_default_superadmin.png';
$config['avatar_default_admin'] = $config['theme_folder_url'] . 'cp_themes/default/images/avatar_default_admin.png';
$config['avatar_default_member'] = $config['theme_folder_url'] . 'cp_themes/default/images/avatar_default_member.png';


/* Minimee cache path
-------------------------------------------------------------------*/
//// basic preferences
//$config['minimee_cache_path'] = $config['server_path'] . '/assets/cache';
//$config['minimee_cache_url'] = $config['site_url'] . '/assets/cache';
//// advanced prefernces
//$config['minimee_base_path'] = '';
//$config['minimee_base_url'] = '';
//$config['minimee_debug'] = 'n'; // 'n' or 'y'
//$config['minimee_disable'] = 'n'; // 'n' or 'y'
//$config['minimee_remote_mode'] = 'auto'; // 'auto', 'curl' or 'fgc'




// END EE config items

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
*/
$config['base_url']	= $config['site_url'] . "/";

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = "";

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of "AUTO" works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'AUTO'			Default - auto detects
| 'PATH_INFO'		Uses the PATH_INFO
| 'QUERY_STRING'	Uses the QUERY_STRING
| 'REQUEST_URI'		Uses the REQUEST_URI
| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
|
*/
$config['uri_protocol']	= 'AUTO';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/urls.html
*/

$config['url_suffix'] = "";

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= "spanish";
$config['deft_lang'] = "spanish";


/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the "hooks" feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = true;


/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/core_classes.html
| http://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'EE_';

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify which characters are permitted within your URLs.
| When someone tries to submit a URL with disallowed characters they will
| get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = "a-z 0-9~%.:_\-";

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The two other items let you set the query string "words" that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['enable_query_strings'] = FALSE;
$config['directory_trigger'] = "D";
$config['controller_trigger'] = "C";
$config['function_trigger'] = "M";

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to
| determine what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 0;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the
| default system/expressionengine/logs/ directory. Use a full server path
| with trailing slash.
|
| Note: You may need to create this directory if your server does not
| create it automatically.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the
| default system/expressionengine/cache/ directory. Use a full server path
| with trailing slash.
|
*/
$config['cache_path'] = $config['server_path'] . "/cache/";

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Sessions class with encryption
| enabled you MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = '';

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
|
| Determines whether Cross Site Request Forgery protection is enabled.
| For more info visit the security library page of the user guide
|
*/
$config['csrf_protection'] = FALSE;

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not "echo" any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are "local" or "gmt".  This pref tells the system whether to use
| your server's local time as the master "now" reference, or convert it to
| GMT.  See the "date helper" page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = "local";

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
*/
$config['rewrite_short_tags'] = TRUE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = "";

/* End of file config.php */
/* Location: ./system/expressionengine/config/config.php */


if($config['config_debug'] == true){
    foreach($config as $k => $v){
        echo 'nombre: ' . $k . ' | valor: ' . $v . '<br>';
    }
    echo 'Final-'; exit;
}