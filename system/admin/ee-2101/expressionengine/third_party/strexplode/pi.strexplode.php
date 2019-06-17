<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * String Explode Tag Class
 *
 * @package     ExpressionEngine
 * @category    Plugin
 * @author      Tom Burr
 * @copyright   Copyright (c) 2013, Turned Out Nice Again
 * @link        http://niceagain.co.uk
 */

$plugin_info = array(
	'pi_name'			=> 'Explode String Exp',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Tom Burr',
	'pi_author_url'		=> 'http://niceagain.co.uk/',
	'pi_description'	=> 'Tag for PHP explode() function.',
	'pi_usage'			=>  StrExplode::usage()
);
					
class StrExplode
{
  
	public $return_data = "";

    // --------------------------------------------------------------------

    /**
     * Str Explode creator
     *
     * This function explodes a string into an array and returns the designated index value
     *
     * @access  public
     * @return  string
     */
    public function __construct()
	{
		$this->EE =& get_instance();
        $this->return_data = "";
		
		$string = $this->EE->TMPL->fetch_param('string', '');
		$delimiter = $this->EE->TMPL->fetch_param('delimiter', '');
		$use_ee_count = $this->EE->TMPL->fetch_param('use_eecounter') == 'yes' ? $this->EE->TMPL->fetch_param('use_eecounter') : '';
		$index = intval($this->EE->TMPL->fetch_param('index', '0'));
		
		echo '<br>' . $index . '<br>';
		$index = ($use_ee_count) ? ($index - 1) : $index;
		//echo $use_ee_count . '<br>';
		echo $index . '<br>';
		
		//Output transformed string
		$tempArray = explode($delimiter, $string);
		if(count($tempArray) - 1 < $index)
		{
			echo 'strexplode: 1';
			$this->return_data = $tempArray[count($tempArray) - 1];
		}
		else
		{
			echo 'strexplode: 2: ' . $tempArray[$index];
			$this->return_data = $tempArray[$index];
		}
	}

    // --------------------------------------------------------------------

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access  public
     * @return  string
     */
    public static function usage()
	{
		ob_start(); 
		?>

		Sometimes you need to use PHP functions but don't want to
		enable PHP in the template. This plugin allows you to use 
		the PHP explode function in an EE tag passing the
		delimiter, array index, and string to search as parameters. 

		With this plugin you can pass EE variables to the tag 
		which will process them and return the index value from the array.

		This example returns the string 'value2';

		{exp:strexplode delimiter="|" index="1" string="value1|value2" }

		------

		Thanks to Christopher Reding's ( http://christopherreding.com/ ) "Tag for PHP str_replace() function" for the inspiration for this plugin.

		<?php
		$buffer = ob_get_contents();
			
		ob_end_clean(); 

		return $buffer;
	}

}
/* End of file pi.strexplode.php */ 
/* Location: ./system/expressionengine/third_party/tona_string_explode/pi.strexplode.php */ 