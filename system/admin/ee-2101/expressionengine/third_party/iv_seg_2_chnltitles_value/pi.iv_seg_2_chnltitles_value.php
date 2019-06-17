<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

IV Segment to Channel Value is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

Read the terms of the GNU General Public License
at <http://www.gnu.org/licenses/>.
=====================================================
*/

$plugin_info = array(
	'pi_name'			=> 'IV Segment to Channel Titles Value',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Ivano Vardé',
	'pi_author_url'		=> 'http://neomedia.com.ar',
	'pi_description'	=> 'Returns a specified value from exp_channel_titles table for the given URL title',
	'pi_usage'			=> iv_seg_2_chnltitles_value::usage()
);

/**
 * Pull the Entry ID for a given URL Title.
 *
 * @package		ExpressionEngine
 * @category	Plugin
 * @author		Ivano Varde
 * @copyright	Copyright (c) 2016, Neomedia Comunicacion
 * @link		http://neomedia.com.ar
 */
class Iv_seg_2_chnltitles_value {
	
	function __construct() {
		
		$this->EE =& get_instance();
		
		$url_title = $this->EE->TMPL->fetch_param('url_title');
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		$field = $this->EE->TMPL->fetch_param('field', 'entry_id');
		$debug = $this->EE->TMPL->fetch_param('debug', 'false');
		
		if(!empty($url_title) || !empty($entry_id))
		{
			$queryfield_name = $entry_id ? 'entry_id' : 'url_title';
			$query_field_value = $entry_id ? $entry_id : $url_title;
			$this->EE->db->select('*')
				->from('channel_titles')
				->where($queryfield_name, $query_field_value)
				->limit(1);
			
			$query = $this->EE->db->get();
			
			if ($query->num_rows() == 0) {
				if ($debug == 'false') {
					$this->return_data = '';
				} else {
					$this->return_data = 'No matching entry found';
				}
			} else {
				$this->return_data = $query->row($field);
			}
			
		}
	}
		
	static function usage() {
		ob_start();
		?>
You have to specify the url_title or the entry_id and the field you want to get from the exp_channel_titles table. Pretty easy.
Returns a specified value from exp_channel_titles table for the given URL title

Parameters:

field: (required | default: entry_id) - The field you want to obtain the value (it must belong to the exp_channel_titles table).
entry_id: The entry_id of the entry you want to get data from.
url_title: The url_title to be used to search the results.
debug: (optional) - If set to yes will display some info about the process of the search.

Either entry_id or url_title must be provided.

Example of usage: 

{exp:iv_seg_2_chnltitles_value field="title" url_title="{segment_2}" debug="yes"}

{exp:iv_seg_2_chnltitles_value field="status" entry_id="22" debug="yes"}

		<?php
		return ob_get_clean();
	}
	// END
}
/* End of file pi.iv_seg_2_chnltitles_value.php */
/* Location: ./system/expressionengine/third_party/iv_seg_2_chnltitles_value/pi.iv_seg_2_chnltitles_value.php */
