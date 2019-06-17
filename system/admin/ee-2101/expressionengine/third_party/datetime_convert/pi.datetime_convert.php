<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================
=====================================================
 File: pi.datetime_convert.php
-----------------------------------------------------
 Purpose: Date Time Converter.  This class an 
 ExpressionEngine Date/Time and converts it to another 
 language for multi-lingual sites.
=====================================================
 A huge thanks to Joris Heyndrickx (@moonbeetle) for
 the EE2 conversion.
=====================================================
*/

$plugin_info = array(
	'pi_name'			=> 'Language Date/Time Convert',
	'pi_version'		=> '1.1',
	'pi_author'			=> 'Made By Hippo',
	'pi_author_url'		=> 'http://www.madebyhippo.com/addon-shack/',
	'pi_description'	=> 'Allows you to convert your EE date/time into a different language',
	'pi_usage'			=> Datetime_convert::usage()
);


class Datetime_convert {

 var $return_data = "";
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

// Thanks to Peter Siska (http://www.designchuchi.ch) for a slight tweak to the date logic.

 function datetime_convert()
 {    
 	$this->EE =& get_instance();



       header('Content-Type: text/html; charset=utf-8');
                
        $language=$this->EE->TMPL->fetch_param('language');
        $format = $this->EE->TMPL->fetch_param('format');
        $convert_html = $this->EE->TMPL->fetch_param('convert_entities','no');
        
   		$olddate = $this->EE->TMPL->tagdata;
   		
   		
      if ( ! is_numeric($olddate)) $olddate = strtotime($olddate); 

	  	setlocale(LC_ALL, $language);
       	
       	$trans = get_html_translation_table(HTML_ENTITIES);
       	
        $newdate = strftime($format,$olddate);
		
		if ($convert_html=="yes")
		{
			$newdate = strtr($newdate, $trans);	
		}
		
		$this->return_data = utf8_encode($newdate);
    }


public static function usage()
{
ob_start(); 
?>
Simply encapsulate your ExpressionEngine Date/Time into the following Tags :

{exp:datetime_convert language="" format="" convert_entities="yes|no"}{/exp:datetime_convert}

Passing the appropriate Language reference into the LANG="" parameter.

* fr_FR : French
* es_ES : Spanish
* nl_NL : Dutch

Depending on what Locales are installed on your server. You can check this by running 'locale -a' from a console window.

Passing the format that you would like the date/time returned in using the FORMAT="" parameter:

Default format is usually "%A %e %B %Y", however, review http://php.net/manual/en/function.strftime.php for full details on formatting
<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */

}
/* End of file pi.datetime_convert.php */ 

/* Location: ./system/expressionengine/third_party/datetime_convert/pi.datetime_convert.php */