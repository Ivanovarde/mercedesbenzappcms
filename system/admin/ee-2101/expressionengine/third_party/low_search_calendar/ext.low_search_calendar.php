<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Search Calendar Extension class
 *
 * @package        low_search_calendar
 * @author         Lodewijk Schutte ~ Low <hi@gotolow.com>
 * @link           https://github.com/low/low_search_calendar
 * @copyright      Copyright (c) 2014, Low
 */
class Low_search_calendar_ext {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Extension name
	 *
	 * @access      public
	 * @var         string
	 */
	public $name = 'Low Search Calendar';

	/**
	 * Extension version
	 *
	 * @access      public
	 * @var         string
	 */
	public $version = '1.0.2';

	/**
	 * Extension description
	 *
	 * @access      public
	 * @var         string
	 */
	public $description = 'Enables Calendar compatibility to Low Search';

	/**
	 * Do settings exist?
	 *
	 * @access      public
	 * @var         bool
	 */
	public $settings_exist = FALSE;

	/**
	 * Documentation link
	 *
	 * @access      public
	 * @var         string
	 */
	public $docs_url = 'https://github.com/low/low_search_calendar';

	// --------------------------------------------------------------------

	/**
	 * Current class name
	 *
	 * @access      private
	 * @var         string
	 */
	private $class_name;

	/**
	 * Hooks used
	 *
	 * @access      private
	 * @var         array
	 */
	private $hooks = array(
		'low_search_channel_entries'
	);

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @param      mixed     Array with settings or FALSE
	 * @return     null
	 */
	public function __construct()
	{
		// Set Class name
		$this->class_name = ucfirst(get_class($this));
	}

	// --------------------------------------------------------------------

	/**
	 * Call Calendar module instead of Channel
	 *
	 * @access     public
	 * @param      array
	 * @return     array
	 */
	public function low_search_channel_entries()
	{
		// -------------------------------------------
		// Get the latest version of return value; FALSE is fine, too
		// -------------------------------------------

		$tagdata = ee()->extensions->last_call;

		// -------------------------------------------
		// Check 'calendar:method' parameter
		// -------------------------------------------

		if ( ! ($method = ee()->low_search_params->get('calendar:method')))
		{
			return $tagdata;
		}

		// -------------------------------------------
		// Load Calendar module
		// -------------------------------------------

		if ( ! class_exists('Calendar'))
		{
			$this->_log('Including Calendar files');
			require_once(PATH_THIRD.'calendar/mod.calendar.php');
		}

		$Cal = new Calendar();

		// -------------------------------------------
		// Check if method is callable
		// -------------------------------------------

		if ( ! method_exists($Cal, $method))
		{
			$this->_log($method . ' is not a valid method in Calendar');
			return $Cal->no_results();
		}

		// -------------------------------------------
		// If LS set entry_id, make sure event_id is the same
		// -------------------------------------------

		if ($fixed = ee()->low_search_params->get('fixed_order'))
		{
			ee()->low_search_params->apply('event_id', $fixed);
			ee()->low_search_params->delete('fixed_order');
		}
		elseif ($entry_id = ee()->low_search_params->get('entry_id'))
		{
			ee()->low_search_params->apply('event_id', $entry_id);
			ee()->low_search_params->delete('entry_id');
		}

		// -------------------------------------------
		// Call it
		// -------------------------------------------

		$this->_log('Calling Calendar::'.$method.'()');
		$tagdata = $Cal->$method();

		// -------------------------------------------
		// Now try and determine the total results generated
		// -------------------------------------------

		switch ($method)
		{
			case 'occurrences':
				// TODO: find a reliable way to get the total # of occurrences returned
				$total = isset($Cal->total_occurrences) ? $Cal->total_occurrences : FALSE;
			break;

			default:
				$total = isset($Cal->event_timeframe_total) ? $Cal->event_timeframe_total : FALSE;
			break;
		}

		// Log it, even if it's 0
		if ($total !== FALSE && version_compare(LOW_SEARCH_VERSION, '3.3.0', '>='))
		{
			ee()->low_search_log_model->add_num_results($total);
		}

		// -------------------------------------------
		// Return the tagdata
		// -------------------------------------------

		return $tagdata;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate extension
	 *
	 * @access     public
	 * @return     null
	 */
	public function activate_extension()
	{
		foreach ($this->hooks AS $hook)
		{
			$this->_add_hook($hook);
		}
	}

	/**
	 * Update extension
	 *
	 * @access     public
	 * @param      string    Saved extension version
	 * @return     null
	 */
	public function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}

		// init data array
		$data = array();

		// Update to 1.0.0
		// if (version_compare($current, '1.0.0', '<'))
		// {
		// 	// Nothing here yet
		// }

		// Add version to data array
		$data['version'] = $this->version;

		// Update records using data array
		ee()->db->where('class', $this->class_name);
		ee()->db->update('extensions', $data);
	}

	/**
	 * Disable extension
	 *
	 * @access     public
	 * @return     null
	 */
	public function disable_extension()
	{
		// Delete records
		ee()->db->where('class', $this->class_name);
		ee()->db->delete('extensions');
	}

	// --------------------------------------------------------------------
	// PRIVATE METHODS
	// --------------------------------------------------------------------

	/**
	 * Add hook to table
	 *
	 * @access     private
	 * @param      string
	 * @return     void
	 */
	private function _add_hook($hook)
	{
		ee()->db->insert('extensions', array(
			'class'    => $this->class_name,
			'method'   => $hook,
			'hook'     => $hook,
			'settings' => '',
			'priority' => 5,
			'version'  => $this->version,
			'enabled'  => 'y'
		));
	}

	/**
	 * Log something to the TMPL log
	 *
	 * @access     private
	 * @param      string
	 * @return     void
	 */
	private function _log($msg)
	{
		ee()->TMPL->log_item($this->name.': '.$msg);
	}
}
// END CLASS

/* End of file ext.low_search_calendar.php */