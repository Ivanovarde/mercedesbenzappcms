<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*	ZENBU THIRD-PARTY FIELDTYPE SUPPORT
*	============================================
*	Standard relationship field
*	@author	EllisLab
*	============================================
*	File rel.php
*
*/

class Zenbu_rel_ft extends Rel_ft
{
	var $dropdown_type = "contains_doesnotcontain";

	/**
	*	Constructor
	*
	*	@access	public
	*/
	function __construct()
	{
		$this->EE =& get_instance();
	}

	/**
	*	======================
	*	function zenbu_display
	*	======================
	*	Set up display in entry result cell
	*
	*	@param	$entry_id			int		The entry ID of this single result entry
	*	@param	$channel_id			int		The channel ID associated to this single result entry
	*	@param	$data				array	Raw data as found in database cell in exp_channel_data
	*	@param	$table_data			array	Data array usually retrieved from other table than exp_channel_data
	*	@param	$field_id			int		The ID of this field
	*	@param	$settings			array	The settings array, containing saved field order, display, extra options etc settings
	*	@param	$rules				array	An array of entry filtering rules
	*	@param	$upload_prefs		array	An array of upload preferences (optional)
	*	@param 	$installed_addons	array	An array of installed addons and their version numbers (optional)
	*	@param	$fieldtypes			array	Fieldtype of available fieldtypes: id, name, etc (optional)
	*	@return	$output		The HTML used to display data
	*/
	function zenbu_display($entry_id, $channel_id, $field_data, $rel_data = array(), $field_id, $settings, $rules = array(), $upload_prefs = array(), $installed_addons)
	{
		$output = '&nbsp;';

		$this_entry_id = $entry_id;
		if( empty($rel_data))
		{
			return $output;
		}

		// --------------
		// Relationships
		// --------------
		// @param $field_data is the data in the field, which contains the rel_id
		if(isset($rel_data['parent_id_'.$this_entry_id]['rel_id_'.$field_data]) && ! isset($rel_data['parent_id_'.$this_entry_id]['field_id_'.$field_data]))
		{
			$output = "<ul>";
			$related_entries = $rel_data['parent_id_'.$this_entry_id]['rel_id_'.$field_data];
			$extra_settings = $settings['setting'][$channel_id]['extra_options'];
			foreach($related_entries as $child_entry_id => $entry_data_array)
			{
				$entry_title = highlight($entry_data_array['title'], $rules, 'field_'.$field_id);
				$entry_id = $entry_data_array['entry_id'];
				$entry_id_prefix = (isset($extra_settings['field_'.$field_id]['rel_option_1']) && $extra_settings['field_'.$field_id]['rel_option_1'] == 'y') ? $entry_data_array['entry_id'] . ' - ' : '';
				$channel_id = $entry_data_array['channel_id'];
				$output .= '<li>'.anchor(BASE.AMP."C=content_publish".AMP."M=entry_form".AMP."channel_id=".$channel_id.AMP."entry_id=".$entry_id, $entry_id_prefix . $entry_title);
				$output .= (count($related_entries) > 1) ? '</li>' : '';
			}
			$output .= '</ul>';
			return $output;
		}

		return $output;

	}


	/**
	*	===================================
	*	function zenbu_field_extra_settings
	*	===================================
	*	Set up display for this fieldtype in "display settings"
	*
	*	@param	$table_col			string	A Zenbu table column name to be used for settings and input field labels
	*	@param	$channel_id			int		The channel ID for this field
	*	@param	$extra_options		array	The Zenbu field settings, used to retieve pre-saved data
	*	@return	$output		The HTML used to display setting fields
	*/
	function zenbu_field_extra_settings($table_col, $channel_id, $extra_options)
	{
		// Retrieve previous results if present
		$rel_option_1 = (isset($extra_options['rel_option_1']) && $extra_options['rel_option_1'] == 'y') ? TRUE : FALSE;

		// Option: Show related entry ID with related entry title
		$output['rel_option_1'] = form_label(form_checkbox('settings['.$channel_id.']['.$table_col.'][rel_option_1]', 'y', $rel_option_1) . '&nbsp;' . $this->EE->lang->line('show').NBS.$this->EE->lang->line('entry_id'));

		// Output
		return $output;

	}


	/**
	*	=============================
	*	function zenbu_get_table_data
	*	=============================
	*	Retrieve data stored in other database tables
	*	based on results from Zenbu's entry list
	*	@uses	Instead of many small queries, this function can be used to carry out
	*			a single query of data to be later processed by the zenbu_display() method
	*
	*	@param	$entry_ids				array	An array of entry IDs from Zenbu's entry listing results
	*	@param	$field_ids				array	An array of field IDs tied to/associated with result entries
	*	@param	$channel_id				int		The ID of the channel in which Zenbu searched entries (0 = "All channels")
	*	@param	$output_upload_prefs	array	An array of upload preferences
	*	@param	$settings				array	The settings array, containing saved field order, display, extra options etc settings
	*	@param	$rel_array				array	A simple array useful when using related entry-type fields (optional)
	*	@return	$output					array	An array of data (typically broken down by entry_id then field_id) that can be used and processed by the zenbu_display() method
	*/
	function zenbu_get_table_data($entry_ids, $field_ids, $channel_id, $output_upload_prefs, $settings, $rel_array)
	{
		$output = array();

		if( empty($entry_ids) || empty($field_ids) || empty($rel_array))
		{
			return $output;
		}


		// STANDARD RELATIONSHIPS
		// Build rel_id data for query
		$rel_data = array();
		foreach($rel_array as $entry_id => $field_id)
		{
			foreach($field_id as $key => $rel_ids)
			{
				if($rel_ids != 0)
				{
					$rel_data[] = $rel_ids;
				}
			}
		}
		if( ! empty($rel_data))
		{
			$this->EE->db->select(array("exp_channel_titles.entry_id", "exp_relationships.rel_parent_id", "exp_relationships.rel_id", "exp_channel_titles.title", "exp_channel_titles.channel_id"));
			$this->EE->db->from("exp_channel_titles");
			$this->EE->db->join("exp_relationships", "exp_channel_titles.entry_id = exp_relationships.rel_child_id");
			$this->EE->db->where_in("exp_relationships.rel_parent_id", $entry_ids);
			$this->EE->db->where_in("exp_relationships.rel_id", $rel_data);
			$query = $this->EE->db->get();

			foreach($query->result_array() as $row)
			{
				$output['parent_id_'.$row['rel_parent_id']]['rel_id_'.$row['rel_id']]['child_id_'.$row['entry_id']]['title'] = $row['title'];
				$output['parent_id_'.$row['rel_parent_id']]['rel_id_'.$row['rel_id']]['child_id_'.$row['entry_id']]['entry_id'] = $row['entry_id'];
				$output['parent_id_'.$row['rel_parent_id']]['rel_id_'.$row['rel_id']]['child_id_'.$row['entry_id']]['channel_id'] = $row['channel_id'];
			}
			$query->free_result();
		}



		return $output;
	}

	/**
	*	===================================
	*	function zenbu_result_query
	*	===================================
	*	Extra queries to be intergrated into main entry result query
	*
	*	@param	$rules				int		An array of entry filtering rules
	*	@param	$field_id			array	The ID of this field
	*	@param	$fieldtypes			array	$fieldtype data
	*	@param	$already_queried	bool	Used to avoid using a FROM statement for the same field twice
	*	@param	$installed_addons	array	An array of installed addons and their version numbers (optional)
	*	@return					A query to be integrated with entry results. Should be in CI Active Record format ($this->EE->db->…)
	*/
	function zenbu_result_query($rules = array(), $field_id = "", $fieldtypes)
	{
		// Let's not even go through this if there isn't a word to search in th first place
		if(empty($rules))
		{
			return;
		}
		// Get Playa version given differences between versions 3 and 4
		$rel_linker = "";

		foreach($rules as $rule)
		{
			$in = $rule['cond'];
			$keyword = $rule['val'];
			$rule_field_id = (strncmp($rule['field'], 'field_', 6) == 0) ? substr($rule['field'], 6) : '';

			if(strncmp($rule['field'], 'field_', 6) == 0 && ! empty($keyword))
			{


				if(isset($fieldtypes['fieldtype'][$rule_field_id]) && $fieldtypes['fieldtype'][$rule_field_id] == "rel")
				{
					$rel_keyword_query = $this->EE->db->query("/* Zenbu: Rel query for entries */\nSELECT exp_channel_titles.entry_id, exp_channel_titles.title, exp_relationships.rel_id FROM exp_channel_titles JOIN exp_relationships ON exp_channel_titles.entry_id = exp_relationships.rel_child_id WHERE exp_channel_titles.title LIKE '%".$this->EE->db->escape_like_str($keyword)."%' ");
					$rel_linker = "rel_id";
				} else {
					return;
				}


				$rel_entry_array = array();
				if($rel_keyword_query->num_rows() > 0)
				{
					foreach($rel_keyword_query->result_array() as $row)
					{
						$rel_entry_array[] = $row[$rel_linker];
					}
				}

				$rel_keyword_query->free_result();

				if( ! empty($rel_entry_array))
				{
					$count = 1;
					$where = "(";

					foreach($rel_entry_array as $key => $val)
					{
						// Data is stored in custom fields as either [rel_id] or [entry_ids] (along with possibly non-up-to-date entry titles).
						// Do a match search based on rel_id/entry_id data above
						if($in == "doesnotcontain")
						{
							$where .= ($count == 1) ? 'exp_channel_data.field_id_'.$field_id.' NOT LIKE "%'.$this->EE->db->escape_like_str($val).'%"' : ' AND exp_channel_data.field_id_'.$field_id.' NOT LIKE "%'.$this->EE->db->escape_like_str($val).'%"';

						} else {
							$where .= ($count == 1) ? 'exp_channel_data.field_id_'.$field_id.' LIKE "%'.$this->EE->db->escape_like_str($val).'%"' : ' OR exp_channel_data.field_id_'.$field_id.' LIKE "%'.$this->EE->db->escape_like_str($val).'%"';
						}
						$count++;
					}

					if($in == "doesnotcontain")
					{
						$where .= ' OR exp_channel_data.field_id_'.$field_id.' IS NULL';
					}

					$where .= ")";
					( isset($where) && ! is_null($where) && ! empty($where)) ? $this->EE->db->where($where) : '';
				} else {
					if($in == "doesnotcontain")
					{
						$where = "(exp_channel_data.field_id_".$field_id." NOT LIKE '%".$this->EE->db->escape_like_str($keyword)."%' OR exp_channel_data.field_id_".$field_id." IS NULL)";
						$this->EE->db->where($where);
					} else {
						 $this->EE->db->like("channel_data.field_id_".$field_id, $keyword);
					}
				}

			} //if
		} // foreach
	}


} // END CLASS

/* End of file rel.php */
/* Location: ./system/expressionengine/third_party/zenbu/fieldtypes/rel.php */
?>