<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Developer Accessory
 *
 * @package     IV Empty Expresso Fields
 * @category    Accessory
 * @description Removes all kind of html or chars from the Expresso fields (if empty) when submitting the publish form
 * @author      Ivano Varde
 * @link        http://neomedia.com.ar
 */


class iv_empty_expresso_fields_acc
{
    var $name           = 'IV Empty Expresso Fields';
    var $id             = 'iv_empty_expresso_fields';
    var $version        = '1.0';
    var $description    = 'Removes all kind of html or chars from the Expresso fields (if empty) when submitting the publish form';
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
    * Set Sections
    */
    function set_sections()
    {
        // hide accessory from footer tabs
        $this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.' . $this->id . '").parent("li").hide();</script>';

        // If we're not in the content publish form, bail out
		if($this->EE->router->fetch_class() != 'content_publish') return;

		$this->EE->load->library('javascript');

		$this->EE->cp->load_package_js('iv_empty_expresso_fields');

    }

}
// END CLASS

/* End of file acc.iv_empty_expresso_fields.php */
/* Location: ./system/expressionengine/third_party/iv_empty_expresso_fields/acc.iv_empty_expresso_fields.php */