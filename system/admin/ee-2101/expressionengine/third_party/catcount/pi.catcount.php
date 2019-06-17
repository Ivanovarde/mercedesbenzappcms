<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'Category Count',
  'pi_version' =>'2.0',
  'pi_author' =>'Zac Gordon',
  'pi_author_url' => 'http://dabrook.org/',
  'pi_description' => 'Returns the number of entries for a given category.',
  'pi_usage' => Catcount::usage()
  );


class Catcount {

	var $return_data = '';

	function Catcount()
	{
		$this->EE =& get_instance(); 

		//Get parameters
    	$cat_id = $this->EE->TMPL->fetch_param('cat_id');
    	$status = $this->EE->TMPL->fetch_param('status');
    	$expired = $this->EE->TMPL->fetch_param('expired');
		$channel = $this->EE->TMPL->fetch_param('channel');		
		
		//Parse status
		$status = (!$status) ? $status = 'open': explode('|', $status);
		
		//Parse channel
		$channel = (!$status) ? $status = 0: explode('|', $channel);
		
		//Database query
		$this->EE->db->select('exp_category_posts.entry_id');
		$this->EE->db->join('exp_channel_titles', 'exp_category_posts.entry_id = exp_channel_titles.entry_id' );
		if($channel != 0){
			$this->EE->db->join('exp_channels', 'exp_channel_titles.channel_id = exp_channels.channel_id' );
		}
		$this->EE->db->where('cat_id', $cat_id);

		if ($expired != 'yes'){
			$this->EE->db->where("(expiration_date = 0 OR expiration_date > " . $this->EE->localize->now .")");
		}
		
		
		
		$this->EE->db->where_in('status', $status);
		
		if($channel != 0){
			$this->EE->db->where_in('channel_name', $channel);
		}
		
		$this->return_data = $this->EE->db->count_all_results('exp_category_posts');
	}

	  function usage()
	  {
	  ob_start(); 
	  ?>
		Description:

		Returns the number of entries for a given category.

		------------------------------------------------------
		
		Example:


		{exp:catcount cat_id="33" status="open|closed"}

		Returns
		3

		------------------------------------------------------
		
		Parameters:
        
        channel=“posts|events”
		Filters the results for only the channels you provide. Optional.

		cat_id="1"
		The id for the category that you want to output the number of entries for.

		status="open|closed"
		Determines the status of entries you want to count.  Default setting is "open"
		
		expired="yes"
		Will include entries that have expired. 
		
	  <?php
	  $buffer = ob_get_contents();

	  ob_end_clean(); 

	  return $buffer;
	  }
	  // END

	}


/* End of file pi.category_count.php */ 
/* Location: ./system/expressionengine/third_party/plugin_name/pi.category_count.php */
