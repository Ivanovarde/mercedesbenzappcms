<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name'        => 'Low Random',
	'pi_version'     => '2.3.0',
	'pi_author'      => 'Lodewijk Schutte ~ Low',
	'pi_author_url'  => 'http://gotolow.com/addons/low-random',
	'pi_description' => 'Returns randomness.',
	'pi_usage'       => low_random::usage()
);

/**
 * < EE 2.6.0 backward compat
 */
if ( ! function_exists('ee'))
{
	function ee()
	{
		static $EE;
		if ( ! $EE) $EE = get_instance();
		return $EE;
	}
}

/**
 * Low Random Plugin class
 *
 * @package        low_random
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-nice-date
 * @license        http://creativecommons.org/licenses/by-sa/3.0/
 */
class Low_random {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Set of items to choose from
	 *
	 * @var	array
	 */
	private $set = array();

	/**
	 * Debug mode
	 *
	 * @var	bool
	 */
	private $debug = FALSE;

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Randomize given items, pipe delimited
	 *
	 * @param	string	$str
	 * @return	string
	 */
	public function item($str = '')
	{
		if ($str == '')
		{
			$str = ee()->TMPL->fetch_param('items', '');
		}

		$this->set = explode('|', $str);

		return $this->_random_item_from_set();
	}

	// --------------------------------------------------------------------

	/**
	 * Randomize tagdata
	 *
	 * @since	2.1
	 * @param	string	$str
	 * @return	string
	 */
	public function items($str = '')
	{
		// get tagdata
		if ($str == '')
		{
			$str = ee()->TMPL->tagdata;
		}

		// trim if necessary
		if (ee()->TMPL->fetch_param('trim', 'yes') != 'no')
		{
			$str = trim($str);
		}

		// get separator
		$sep = ee()->TMPL->fetch_param('separator', "\n");

		// create array from tagdata
		$this->set = explode($sep, $str);

		return $this->_random_item_from_set();
	}

	// --------------------------------------------------------------------

	/**
	 * Randomize the given letter range
	 *
	 * @param	string	$from
	 * @param	string	$to
	 * @return	string
	 */
	public function letter($from = '', $to = '')
	{
		// Parameters
		if ($from == '')
		{
			$from = ee()->TMPL->fetch_param('from', 'a');
		}

		if ($to == '')
		{
			$to	= ee()->TMPL->fetch_param('to', 'z');
		}

		// no from? Set to a
		if (!preg_match('/^[a-z]$/i', $from))
		{
			$from = 'a';
		}

		// no to? Set to z
		if (!preg_match('/^[a-z]$/i', $to))
		{
			$to = 'z';
		}

		// fill set
		$this->set = range($from, $to);

		return $this->_random_item_from_set();
	}

	// --------------------------------------------------------------------

	/**
	 * Random number between 2 values
	 *
	 * @param	string	$from
	 * @param	string	$to
	 * @return	string
	 */
	public function number($from = '', $to = '')
	{
		// Parameters
		if ($from == '')
		{
			$from = ee()->TMPL->fetch_param('from', '0');
		}

		if ($to == '')
		{
			$to	= ee()->TMPL->fetch_param('to', '9');
		}

		// no from? Set to 0
		if (!is_numeric($from))
		{
			$from = '0';
		}

		// no to? Set to 9
		if (!is_numeric($to))
		{
			$to = '9';
		}

		// return random number
		return strval(rand(intval($from), intval($to)));
	}

	// --------------------------------------------------------------------

	/**
	 * Get random file from file system
	 *
	 * @param	string	$folder
	 * @param	string	$filter
	 * @return	string
	 */
	public function file($folder = '', $filter = '')
	{
		// init var
		$error = FALSE;

		// Parameters
		if ($folder == '')
		{
			$folder = ee()->TMPL->fetch_param('folder');
		}

		if ($filter == '')
		{
			$filter = ee()->TMPL->fetch_param('filter', '');
		}

		// Convert filter to array
		$filters = strlen($filter) ? explode('|', $filter) : array();

		// is folder a number?
		if (is_numeric($folder))
		{
			// get server path from upload prefs
			ee()->load->model('file_upload_preferences_model');
			$upload_prefs = ee()->file_upload_preferences_model->get_file_upload_preferences(1, $folder);

			// Do we have a match? get path
			if ($upload_prefs)
			{
				$folder = $upload_prefs['server_path'];
			}
		}

		// Simple folder check
		if (!strlen($folder))
		{
			$error = TRUE;
		}
		else
		{
			// check for trailing slash
			if (substr($folder, -1, 1) != '/')
			{
				$folder .= '/';
			}
		}

		// Another folder check
		if (!is_dir($folder))
		{
			$error = TRUE;
		}
		else
		{
			// open dir
			$dir = opendir($folder);

			// loop through folder
			while($f = readdir($dir))
			{
				// no file? skip
				if (!is_file($folder.$f)) continue;

				// set addit to 0, check filters
				$addit = 0;

				// check if filter applies
				foreach ($filters AS $filter)
				{
					if (strlen($filter) && substr_count($f, $filter))
					{
						$addit++;
					}
				}

				// if we have a match, add file to array
				if ($addit == count($filters))
				{
					$this->set[] = $f;
				}
			}

			// close dir
			closedir($dir);
		}

		// return data
		return $error ? $this->_invalid_folder($folder) : $this->_random_item_from_set();
	}

	// --------------------------------------------------------------------

	/**
	 * Display invalid folder if debug is on
	 *
	 * @param	string	$folder
	 * @return	string
	 */
	private function _invalid_folder($folder = '')
	{
		// return error message if debug-mode is on
		return $this->debug ? "{$folder} is an invalid folder" : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Random item from set (array)
	 *
	 * @return	string
	 */
	private function _random_item_from_set()
	{
		return $this->set[array_rand($this->set)];
	}

	// --------------------------------------------------------------------

	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------
	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	static function usage()
	{
		ob_start();
	?>
		See http://gotolow.com/addons/low-random for more info.

		File tag#
		Parameter 	Description
		folder 	Either the server path of the folder, or the numeric Upload Destination id. Required.
		filter 	Any number of sub strings the file name should contain, separated by vertical bars.

		{exp:low_random:file folder="/some/path/to/images" filter="masthead|.jpg"}
		{exp:low_random:file folder="3" filter=".pdf"}

		Item tag#
		Parameter 	Description
		items 	Any number of items, separated by vertical bars.

		{exp:low_random:item items="cat|dog|ferret|raptor"}
		{exp:low_random:item items="Yes|No|Maybe|Try again later|You're kidding, right?"}

		Items tag#
		Parameter 	Description
		separator 	Character used to separate values. Defaults to new line.
		trim 	Set to “no” if you don’t want the tagdata to be stripped of white space at the beginning and end.

		{exp:low_random:items}
		  Cat
		  Dog
		  Ferret
		  Raptor
		{/exp:low_random:items}

		{exp:low_random:items separator=","}
		  Cat, Dog, Ferret, Raptor
		{/exp:low_random:items}

		Letter tag#
		Parameter 	Description
		from 	Letter to start the range with, defaults to a.
		to 	Letter to end the range with, defaults to z.

		Note: this tag returns a letter in the same case as the given parameters.

		{exp:low_random:letter}
		{exp:low_random:letter from="A" to="F"}

		Number tag#
		Parameter 	Description
		from 	Number to start the range with, defaults to 0.
		to 	Number to end the range with, defaults to 9.

		{exp:low_random:number}
		{exp:low_random:number from="100" to="999"}

	<?php

		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	// END USAGE

}
// END CLASS

/* End of file pi.low_random.php */
