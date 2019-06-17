<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================
 This ExpressionEngine plugin was created by Ivano Vardé
 - http://devot-ee.com/developers/ivano-varde
=====================================================
 Copyright (c) Ivano Vardé 2016
=====================================================
This is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IV Structure parent ID is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

Read the terms of the GNU General Public License
at <http://www.gnu.org/licenses/>.
=====================================================
*/

$plugin_info = array(
	'pi_name'			=> 'IV Structure parent ID',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Ivano Vardé',
	'pi_author_url'		=> 'http://neomedia.com.ar',
	'pi_description'	=> 'Returns the structure\'s parent entry_id of a given entry, or 0 if the entry is at Structure Top Level. Good to be used in conditionals.',
	'pi_usage'			=> iv_structure_parent_id::usage()
);
					
class Iv_structure_parent_id {

	var $return_data = "";

	static function is_valid_param($p)
	{
		$invalidchars = array('(', ')', '|', '~', '#', '*', '[', ']', '/', '\\', '<', '>', '\'', '\"');
		$is_valid = true;

		for($i = 0; $i < strlen($p); $i++)
		{
			if (in_array($p[$i], $invalidchars)) 
			{
				$is_valid = false;
			}
		}
		return $is_valid;
	}

	function __construct()
	{
		$this->EE =& get_instance();

		// Fetch the tagdata
		//$tagdata = $this->EE->TMPL->tagdata;

		// Fetch params
		$weblog = $this->EE->TMPL->fetch_param('weblog') ? $this->EE->TMPL->fetch_param('weblog') : $this->EE->TMPL->fetch_param('channel');
		$urltitle = $this->EE->TMPL->fetch_param('url_title');
		$entryid = $this->EE->TMPL->fetch_param('entry_id');
		$siteid = $this->EE->TMPL->fetch_param('site_id');
		$field = $this->EE->TMPL->fetch_param('get_field') ? $this->EE->TMPL->fetch_param('get_field') : 'parent_id';

		// Define variables
		$found_invalid = false;
		$a_params = array('weblog' => $weblog, 'url_title' => $urltitle, 'entry_id' => $entryid, 'site_id' => $siteid, 'get_field' => $field);

		foreach($a_params as $param_name => $param_value)
		{
			if(!self::is_valid_param($param_value))
			{
				$found_invalid = true;
				echo '<div style="position: static; border: 2px solid #f00; padding: 15px; margin: 15px auto; width: 100%; background: #fff; color: #333;">Error! Parameter "<strong style="color: #f00;">' . $param_name . '</strong>" of <strong style="color: #f00;">exp:iv_structure_parent_id</strong> tag contains illegal character/s.</div>' . PHP_EOL;
			}
		}

		if ($found_invalid === false)
		{
			$weblogclause = $weblog ? " AND exp_channels.channel_name = '" . trim($weblog) . "' " : '';
			$siteclause = $siteid ? " AND exp_structure.site_id = " . trim($siteid) . " " : '';
			$urltitleclause = $urltitle ? " AND exp_channel_titles.url_title = '" . trim($urltitle) . "' " : '';
			$entryidclause = $entryid ? " AND exp_channel_titles.entry_id = " . trim($entryid) . " " : '';

			// Create SQL query string
			$query = "SELECT * 
					FROM exp_structure, exp_channel_titles, exp_channels 
					WHERE 
					exp_channel_titles.channel_id = exp_channels.channel_id 
					AND exp_channel_titles.entry_id = exp_structure.entry_id" .
					$siteclause .
					$weblogclause .
					$urltitleclause .
					$entryidclause . ";";

			//echo '$query: '.$query.'<br><br>';
			//exit;

			// Perform SQL query
			$query = $this->EE->db->query($query);

			//Find number of entries
			if($query->num_rows() == 1){;

				$result = $query->result_array();
				//echo $result[0][$field];
				$this->return_data = $result[0][$field];
										
			}
		}

	}
	// END CONSTRUCT

	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------
	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	static function usage()
	{
		ob_start(); 
		?>

		Returns the structure's parent entry_id of a given entry, or 0 if the entry is at Structure Top Level. Good to be used in conditionals.

		PARAMETERS:

		url_title = Optional / Required. The url_title of a Structure\'s entry
		entry_id = Optional / Required. The entry_id of a Structure\'s entry
		weblog / channel = Optional. The weblog or Channel name
		site_id = Optional. The site_id of a Structure's entry

		At least one of these: url_title, entry_id must be provided.

		EXAMPLES:

		{exp:iv_structure_parent_id entry_id="22"}

		{exp:iv_structure_parent_id url_title="my_entry"}

		{exp:iv_structure_parent_id url_title="{segment_1}"}


		{if {exp:iv_structure_parent_id url_title="{segment_1}"} == 0}

		    // this IS A TOP LEVEL structure entry.

		{/if}


		{if {exp:iv_structure_parent_id url_title="{segment_1}"}}

		    // this IS NOT a top level structure entry.
		    // The tag will return the Structure's entry parent_id
		    parent_id = {exp:iv_structure_parent_id url_title="{segment_1}"}

		{/if}


		<?php
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}
	// END USAGE

}
// END CLASS
?>