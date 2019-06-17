<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
* Hideee, Hide some stuff!
*
* @package      Hideee
* @version      1.0.0
* @author       Cem Meric <http://webunder.com.au> - Managing Director, Webunder
* @copyright    Copyright (c) 2002-2014 Webunder <http://http://webunder.com.au>
* @license      Attribution-ShareAlike 3.0 Unported <http://creativecommons.org/licenses/by-sa/3.0>
* @subpackage   Accessories
* @category     Accessories
* @purpose      Hide some stuff!
*/


class Hideee_acc
{
    var $name           = 'Hideee';
    var $id             = 'hideee';
    var $version        = '1.0.0';
    var $description    = 'Removes search and accessory tabs. Hide some other stuff by editing the acc.hideee.php file';
    var $sections       = array();


    /**
    * Set Sections
    *
    * Hiding Search div in Control Panel
    *
    */

    function set_sections()
    {
        $EE =& get_instance();

        $this->EE =& get_instance();

        $this->EE->load->library('javascript');
		
		//IVANO
		// get all member groups
		$member_groups = $this->EE->member_model->get_member_groups('*');
		
		foreach($member_groups->result() as $group)
		{
			if($this->EE->session->userdata['group_id'] == $group->group_id){
				
				if($group->can_edit_categories == 'n'){
					$this->js .= "$('td.catEditButtons').find('button[value=\"add_categories\"]').remove();";
				}
				if($group->can_delete_categories == 'n'){
					$this->js .= "$('td.catEditButtons').find('button[value=\"remove_categories\"]').remove();";
				}
				if($group->can_edit_categories == 'n' && $group->can_delete_categories == 'n'){
					$this->js .= "$('td.catEditButtons').remove();";
				}
			
			}
			
		}

        /**
        * Hide some stuff
        *
        */

        $str = '
        $("#search").remove();
        $("#accessoryTabs").find("a.hideee").parent("li").remove();
		$("#siteLogo").find("a").not("#user_avatar").remove();
		$("#user_avatar").removeAttr("href");
		$("#quickLinks").remove();
		$("a[href*=\'/docs/\']").parent().remove();';
		

        $this->EE->javascript->output($str . ' ' . $this->js);
		//IVANO

		// ORIGINAL
        /**
        * Hide some stuff
        *
        */
		/*
        $str = <<<END
        $("#search").remove();
        $("#accessoryTabs").find("a.hideee").parent("li").remove();
		$('#siteLogo').find('a').not('#user_avatar').remove();
		$('#user_avatar').removeAttr('href');
		$('#quickLinks').remove();
		$('a[href*="/docs/"]').parent().remove();
END;
		*/
        //$this->EE->javascript->output($str);
		// ORIGINAL
    }
}
/* Location: ./system/expressionengine/third_party/hideee/acc.hideee.php */