<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------
 
/**
 * Low Reorder for Structure
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Accessory
 * @author		Roi Kingon
 * @link		
 */
 
class Low_reorder_structure_acc {
	
	public $name			= 'Low Reorder for Structure';
	public $id				= 'low_reorder_structure';
	public $version			= '1.1';
	public $description		= 'Change a structure listings Add or Edit -> Add, Edit or Reorder';
	public $sections		= array();
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();
	}

	/**
	 * Set Sections
	 */
	public function set_sections()
	{
		// get db prefix
		$dbp = $this->EE->db->dbprefix;
		
		// make sure Structure is installed
		$query = $this->EE->db->query("SELECT module_id FROM ".$dbp."modules WHERE module_name = 'Structure'");		
		if ($query->num_rows() == 0) { return; }

		// make sure Low Reorder is installed
		$query = $this->EE->db->query("SELECT module_id FROM ".$dbp."modules WHERE module_name = 'Low_reorder'");
		if ($query->num_rows() == 0) { return; }

		// hide accessory tab, we dont need it
		$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.low_reorder_structure").parent().remove();</script>';

		// get reorder sets
		$query = $this->EE->db->query("SELECT set_id,channels FROM ".$dbp."low_reorder_sets");
		if ($query->num_rows() == 0) { return; }
		$js_output = "";
		foreach($query->result_array() as $row) {
			$channels = array_filter(explode("|",$row['channels']));
			$set = $row['set_id'];
			foreach($channels as $channel){
				//ORIGINAL  
				//$js_output .= '$(".page-listing").has("a[href$=\'channel_id='.$channel.'\']").html("<a href=\''.BASE.'&C=content_publish&M=entry_form&channel_id='.$channel.'\'>Add</a>, <a href=\''.BASE.'&C=content_edit&channel_id='.$channel.'\'>Edit</a> or <a href=\''.BASE.'&C=addons_modules&M=show_module_cp&module=low_reorder&method=reorder&set_id='.$set.'\'>Reorder</a>");';
				
				//IVANO HAGO QUE SOLO AGREGUE EL LINK PARA REORDER. PARA QUE FUNCIONE ZENBU EN LOS LINKS DE EDIT
				$js_output .= '$(".page-listing").has("a[href$=\'channel_id='.$channel.'\']").append(" or <a href=\''.BASE.'&C=addons_modules&M=show_module_cp&module=low_reorder&method=reorder&set_id='.$set.'\'>Reorder</a>");';
				//IVANO
			}
		}
		// add some JS to put LV into Structure
		$this->EE->cp->add_to_head('
		<!-- Low Reorder for Structure begin -->
		<script type="text/javascript">
			$(document).ready(function(){
				'.$js_output.'
			});
		</script>');
		
	}
	
	// ----------------------------------------------------------------
	
}
 
/* End of file acc.low_reorder_structure.php */
/* Location: /system/expressionengine/third_party/low_reorder_structure/acc.low_reorder_structure.php */