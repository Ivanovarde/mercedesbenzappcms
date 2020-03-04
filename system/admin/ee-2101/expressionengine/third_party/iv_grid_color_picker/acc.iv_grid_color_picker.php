<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Developer Accessory
 *
 * @package     IV Grid Color Picker
 * @category    Accessory
 * @description Convert an input text to a color picker inside a grid field
 * @author      Ivano Varde
 * @link        http://neomedia.com.ar
 */


class iv_grid_color_picker_acc
{
	 var $name           = 'IV Grid Color Picker';
	 var $id             = 'iv_grid_color_picker';
	 var $version        = '1.0';
	 var $description    = 'Converts an input text to a colorpicker inside a grid field. The text field has to be placed inside a grid column, its Publish Label must be "Color" and its Field Name must be "color"';
	 var $sections       = array();

	 // --------------------------------------------------------------------

	 /**
	* Constructor
	*/
	 function __construct()
	 {
		$this->EE =& get_instance();
	 }

	 // --------------------------------------------------------------------


	/**
	 * admin URL
	 */
	private function _iv_colorpicker_url()
	{
		if (! isset($this->cache['iv_colorpicker_url']))
		{
			$this->cache['iv_colorpicker_url'] = $this->EE->config->slash_item('ee_third_party_folder') . 'iv_grid_color_picker/';
		}

		return $this->cache['iv_colorpicker_url'];
	}

	/**
	 * Include Theme CSS
	 */
	private function _include_file_css($file)
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="/' . $this->_iv_colorpicker_url() . $file . '" />');
	}

	/**
	 * Include Theme JS
	 */
	private function _include_file_js($file)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="/' . $this->_iv_colorpicker_url() . $file . '"></script>');
	}

	 /**
	 * Set Sections
	 */
	 function set_sections()
	 {
		// hide accessory from footer tabs
		$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.' . $this->id . '").parent("li").hide();</script>';

		//$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.default_value").parent().remove();</script>';

		// If we're not in the content publish form, bail out
		if($this->EE->router->fetch_class() != 'content_publish'){
			return;
		}

		 $this->_include_file_css('css/colorpicker.css');

		 $this->_include_file_js('javascript/colorpicker.js');
		 $this->_include_file_js('javascript/iv_grid_color_picker.js');

	 }

}
// END CLASS

/* End of file acc.iv_empty_expresso_fields.php */
/* Location: ./system/expressionengine/third_party/iv_empty_expresso_fields/acc.iv_empty_expresso_fields.php */
