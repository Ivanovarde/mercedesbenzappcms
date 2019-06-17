<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
// --------------------------------------------------------------------
// Dt Class
//
// @package		ExpressionEngine
// @category	Plugin
// @author		Jerry Price
// @copyright	Copyright (c) 2011 Vim Interactive Inc., viminteractive.com
// @link		http://viminteractive.com/ee_addons/docs/dt
//
// @updated 3/14/2014
//
*/


$plugin_info = array(
  'pi_name' => 'DT (date plugin)',
  'pi_version' => '1.4.3',
  'pi_author' => 'Jerry Price',
  'pi_author_url' => 'http://viminteractive.com/ee_plugins/dt',
  'pi_description' => 'many date functions',
  'pi_usage' => dt::usage()
  );

class Dt
{

	var $_dt;
	var $_dp;
	var $_format;
	var $_ee_format = true;
	// -----
	var $_d=0; // add day value
	var $_m=0; // add month value
	var $_y=0; // add year value
	// -----
	var $_h=0; // add hour value
	var $_i=0; // add minute value
	var $_s=0; // add second value
	// ----
	var $return_data = null;

	// --------------------------------------------------------------------
	//      Construct
	//
	//      @access public
	//      @return string
	//
	function Dt()
	{

	
		$this->_format = (ee()->TMPL->fetch_param('format') ? ee()->TMPL->fetch_param('format') : '%m/%d/%y');

		$this->_ee_format = (ee()->TMPL->fetch_param('ee_format') == "false" ? false : true);
		
		// set month . day . year	
		$this->_d = (ee()->TMPL->fetch_param('day')	? ee()->TMPL->fetch_param('day')	: 0);
		$this->_m = (ee()->TMPL->fetch_param('month')	? ee()->TMPL->fetch_param('month')	: 0);
		$this->_y = (ee()->TMPL->fetch_param('year')	? ee()->TMPL->fetch_param('year') 	: 0);

		// set hour . minute . second
		$this->_h = (ee()->TMPL->fetch_param('hour')	? ee()->TMPL->fetch_param('hour')	: 0);
		$this->_i = (ee()->TMPL->fetch_param('minute')	? ee()->TMPL->fetch_param('minute'): 0);
		$this->_s = (ee()->TMPL->fetch_param('second')	? ee()->TMPL->fetch_param('second'): 0);

		// localize date
		$this->localize = (ee()->TMPL->fetch_param('localize') == "false" ? false : true);
		$this->offset 	= strtotime(gmdate("M d Y H:i:s")) - strtotime(date("M d Y H:i:s"));

		$dt = (ee()->TMPL->fetch_param('set') ? ee()->TMPL->fetch_param('set') : '');

		$this->_set($dt);

		$this->_add();

		if( ! $this->localize )
		{	
		//	$this->_dt += $this->offset;
		}

		$this->return_data = $this->_return();
	}
	// --------------------------------------------------------------------

	
	
	// --------------------------------------------------------------------
	// _return string()
	//
	function _return()
	{
		if ($this->_ee_format == true)
		{
			return ee()->localize->format_date($this->_format,$this->_dt);
		}

		return strftime($this->_format,$this->_dt);
		

	}
	// --------------------------------------------------------------------


	// --------------------------------------------------------------------
	// convert to timestamp and set variable $this->_dt
	//
	function _set($dt="")
	{

		$oldStamp = $this->_dt;

		if ($dt=="" || $dt=="//")
			$this->_dt=time();
		else
			$this->_dt = $this->_parseDate($dt);

		if ($oldStamp != $this->_dt)
		{
			$this->_dp = getdate($this->_dt);
		}
		return $this;
	}
	// --------------------------------------------------------------------


	// --------------------------------------------------------------------
	//      _parseDate
	//
	function _parseDate($dt="")
	{
		$ts = 0; // timestamp of parsed date
			
		if(gettype($dt) == 'string')    $ts=strtotime($dt);
		if(gettype($dt) == 'integer')   $ts=$dt;
		
		return $ts;	
	}
	// --------------------------------------------------------------------


	// --------------------------------------------------------------------
	//  add()
	//
	function _add()
	{
			$year   = $this->_dp['year'];
			$month  = $this->_dp['mon'];
			$day    = $this->_dp['mday'];

			$hour   = $this->_dp['hours'];
			$min    = $this->_dp['minutes'];
			$sec    = $this->_dp['seconds'];
			
		
			$this->_set(mktime(
				$hour+$this->_h,
				$min+$this->_i,
				$sec+$this->_s,
				$month+$this->_m,
				$day+$this->_d, 
				$year+$this->_y
				));

			$this->return_data = $this->_return();
	}
	// --------------------------------------------------------------------

	// --------------------------------------------------------------------
	//  Used as tag pair
	//
	function wrap()
	{

		$vars = array(
			'year' 	=> $this->_dp['year'],
			'month' => $this->_dp['mon'],
			'day' 	=> $this->_dp['mday'],
			'dt'	=> $this->_dt,
			'dt_ee'	=> ee()->localize->format_date('%Y-%m-%d %H:%i',$this->_dt)
			);
		
		// $_return = ee()->TMPL->parse_variables( ee()->TMPL->tagdata, array($vars) );
		$_return = ee()->TMPL->parse_variables_row(ee()->TMPL->tagdata, $vars); 

		$_return = str_replace('dt_no_results', 'no_results', $_return);

		return $_return;

	}

	// --------------------------------------------------------------------
	//  Return part of date
	//
	function time()
	{
		return $this->_dt;
	}
	// --------------------------------------------------------------------
	
	// --------------------------------------------------------------------
	//  Return part of date
	//
	function year()
	{
			return $this->_dp['year'];
	}
	function month()
	{
			return $this->_dp['mon'];
	}
	function day()
	{
			return $this->_dp['mday'];
	}
	// --------------------------------------------------------------------

	// --------------------------------------------------------------------
	//      Usage
	//
	//  Make sure and use output buffering
	static function usage()
	{
			ob_start();
	?>

	DT v0.2 beta
	The DT Plugin displays dates with formatting and allows you to add days, months, years to the current date or a static date you set.

	=============================
	The Tag
	=============================

	{exp:dt}

			// Will return the date formatted this way: MM/DD/YYYY

	=============================

	--NEW--

	{exp:dt:wrap}

		<h2>{dt_year}</h2>

		<h2>{dt format="%l %M %j"}</h2>

	{/exp:dt:wrap}

		
		<h2>2012</h2>

		<h2>Saturday Nov 3</h2>

	-- USING WITH ENTRIES LOOP -- 
	
	{exp:dt:wrap day="-1" parse="inward"}  
	
		{exp:channel:entries
			channel="products"
			disable="custom_fields|categories|category_fields|member_data|pagination|trackbacks"
			dynamic="off"
			start_on="{dt_ee}"
		}
			<strong>{entry_date format="%Y-%m-%d %H:%i"}</strong> - {title}<br />
			
		{/exp:channel:entries}

	{/exp:dt:wrap}

	==============
	TAG PARAMETERS
	==============

	set=[optional]
			Sets the static date that the other parameters will reference. If not set will default to the current date.

	{exp:dt set="01/01/2010"}

			// 01/01/2010

	=============================

	format=[optional]
			Sets the format of the returned date using the 	following parameters: 
			http://expressionengine.com/user_guide/templates/date_variable_formatting.html
		
		{exp:dt set="11/03/90" ee_format="true" format="%l %M %j, %Y"}
	
			// Saturday Nov 3, 1990
		
			
	ee_format=[optional]
			if set to 'false' dt will parse the date using php formating:
			http://php.net/manual/en/function.strftime.php 
		
		{exp:dt set="11/03/90" format="%A %b %e, %Y"}

			// Saturday Nov 3, 1990
			

	=============================

	day=[optional]
			Adds or subtracts a value in days to current/set date.

	month=[optional]
			Adds or subtracts a value in months to current/set date.

	year=[optional]
			Adds or subtracts a value in years to current/set date.

	{exp:dt day="3" month="1" year="-1" set="1/1/2010"}
			//      02/04/09


	=============================

	hour=[optional]
			Adds or subtracts a value in hours to current/set date.

	minute=[optional]
			Adds or subtracts a value in minutes to current/set date.

	second=[optional]
			Adds or subtracts a value in seconds to current/set date.

	=============================
	MORE EXAMPLES:
	=============================
	<h1>Dev DT</h1>

	{exp:dt:wrap day="-10" parse="inward"} 

	<p>{dt_ee}</p>

		{exp:channel:entries channel="-channels-*" limit="3" start_on="{dt_ee}"}
			{title} - {entry_date format="%m/%d/%Y"}<br/>

			{if dt_no_results}NOTHING{/if}
		{/exp:channel:entries}
	{/exp:dt:wrap}

	<hr /> 
	current_time: <br />
	{exp:dt format='%Y-%m-%d %h:%i %A' day='2' hour='3' minute='15'}<br />
	{exp:dt:wrap day='2' hour='3' minute='15'}{dt format='%Y-%m-%d %h:%i %A'}{/exp:dt:wrap}<br />


	<hr /> 
	entry_date:<br />
	{exp:channel:entries channel="-channels-" limit="3"}

	{title}<br />
	{exp:dt:wrap set='{entry_date format="%Y-%m-%d %H:%i"}' day='2' hour='3' minute='15'}{dt format='%Y-%m-%d %h:%i %A'}{/exp:dt:wrap} <br />
	{exp:dt set='{entry_date format="%Y-%m-%d %H:%i"}' format='%Y-%m-%d %h:%i %A' day='2' hour='3' minute='15'}<br /><br />

	{/exp:channel:entries}
	<hr /> 
	static date:<br />
	{exp:dt:wrap set='2013-10-25 10:12' day='2' hour='3' minute='15'}{dt format='%Y-%m-%d %h:%i %A'}{/exp:dt:wrap}<br />
	{exp:dt set='2013-10-25 10:12' format='%Y-%m-%d %h:%i %A' day='2' hour='3' minute='15'} <br />


	=============================
	TROUBLESHOOTING:
	=============================

	Please email any bugs or questions to dev@viminteractive.com

	 <?php

			$buffer = ob_get_contents();
			ob_end_clean();
			return $buffer;
	}
	// --------------------------------------------------------------------

}
/* End of file pi.dt.php */
/* Location: ./system/expressionengine/third_party/dt/pi.dt.php */