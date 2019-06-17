<?php

/*
=====================================================
 This ExpressionEngine plugin was created by Laisvunas
 - http://devot-ee.com/developers/laisvunas
=====================================================
 Copyright (c) Laisvunas
=====================================================
 You may use this Software on the SINGLE website.
 Unless you have been granted prior, written consent from Laisvunas, you may not:
 * Reproduce, distribute, or transfer the Software, or portions thereof, to any third party
 * Sell, rent, lease, assign, or sublet the Software or portions thereof
 * Grant rights to any other person
=====================================================
*/

$plugin_info = array(
						'pi_name'			=> 'Single Field',
						'pi_version'		=> '1.5',
						'pi_author'			=> 'Laisvunas',
						'pi_author_url'		=> 'http://devot-ee.com/developers/laisvunas',
						'pi_description'	=> 'Allows you to output value of single field of certain entry without using exp:channel:entries.',
						'pi_usage'			=> single_field::usage()
					);
					
class Single_field {

  var $return_data = '';
  
  function Single_field()
  {
    $this->EE =& get_instance();
    
    $this->EE->load->library('typography');
    $this->EE->typography->initialize();
    
    // Fetch params
    $field	= $this->EE->TMPL->fetch_param('field');
	$entry_id	= $this->EE->TMPL->fetch_param('entry_id');
  	$url_title	= $this->EE->TMPL->fetch_param('url_title');
    $weblog = $this->EE->TMPL->fetch_param('weblog') ? $this->EE->TMPL->fetch_param('weblog') : $this->EE->TMPL->fetch_param('channel');
    $field_format = $this->EE->TMPL->fetch_param('field_format');
    $site	= $this->EE->TMPL->fetch_param('site');
    $invalid_input = $this->EE->TMPL->fetch_param('invalid_input');
    $allow_img_url = $this->EE->TMPL->fetch_param('allow_img_url');
    $auto_links = $this->EE->TMPL->fetch_param('auto_links');
    $encode_email = $this->EE->TMPL->fetch_param('encode_email');
    $encode_type = $this->EE->TMPL->fetch_param('encode_type');
    $html_format = $this->EE->TMPL->fetch_param('html_format');
    $parse_smileys = $this->EE->TMPL->fetch_param('parse_smileys');
    $field_is_date = $this->EE->TMPL->fetch_param('field_is_date') ? $this->EE->TMPL->fetch_param('field_is_date') : 'no';
    $date_format = $this->EE->TMPL->fetch_param('date_format');
    $date_localize = $this->EE->TMPL->fetch_param('date_localize');
	$debug = $this->EE->TMPL->fetch_param('debug', 'no');
    
    
    // Default value for "site" parameter is "1"
	if ($site === FALSE)
	{
      $site = 1;
    }
    
    // Change $date_localize value to TRUE if it is "yes" and to FALSE if it is not
    if ($date_localize === 'yes')
    {
      $date_localize = TRUE;
    }
    else
    {
      $date_localize = FALSE;
    }
    
    // Define variables
    $error_encountered = FALSE;
    $parse_options = array();
    $field_id_clause = '';
    
    // "field" parameter must be defined.
    if ($field === FALSE)
    {
      if ($invalid_input === 'alert')
      {
        echo 'ERROR! "field" parameter of exp:single_field tag must be defined.<br><br>';
      }
      $error_encountered = TRUE;
    }
    
    // Either "entry_id" param or "weblog/channel" and "url_title" params must be defined.
    if ($entry_id === FALSE AND ($weblog === FALSE OR $url_title === FALSE))
    {
      if ($invalid_input === 'alert')
      {
        echo 'ERROR! Either "entry_id" parameter or "channel" and "url_title" parameters of exp:single_field tag must be defined.<br><br>';
      }
      $error_encountered = TRUE;
    }
    
    if ($error_encountered !== TRUE)
    {
      // Create parse options array
      if ($allow_img_url == 'yes')
      {
        $parse_options['allow_img_url'] = 'y';
      }
      if ($auto_links == 'no')
      {
        $parse_options['auto_links'] = 'n';
      }
      if ($encode_email == 'no')
      {
        $parse_options['encode_email'] = 'n';
      }
      if ($encode_type == 'noscript')
      {
        $parse_options['encode_type'] = 'noscript';
      }
      if ($html_format == 'all')
      {
        $parse_options['html_format'] = 'all';
      }
      elseif ($html_format == 'none')
      {
        $parse_options['html_format'] = 'none';
      }
      if ($parse_smileys == 'no')
      {
        $parse_options['parse_smileys'] = 'no';
      }
      
      // I. Find field id
      if ($field == 'title' OR $field == 'url_title')
      {
        $field_id = $field;
      }
      else
      {
        $todo = "SELECT field_id FROM exp_channel_fields WHERE field_name = '".$this->EE->db->escape_str($field)."' AND site_id = '".$this->EE->db->escape_str($site)."' LIMIT 1";
		  
		if ($invalid_input === 'alert')
      {
		  echo 'pi.single_field: $todo: '.$todo.'<br><br>';
		}
		  
        $query = $this->EE->db->query($todo);
        if ($query->num_rows() == 1)
        {
          $field_id = $query->row('field_id');
          $field_id_clause = ", exp_channel_data.field_id_".$this->EE->db->escape_str($field_id).", exp_channel_data.field_ft_".$this->EE->db->escape_str($field_id);
        }
        else
        {
          $field_id = FALSE;
        }
      }
		if ($invalid_input === 'alert')
      {
      echo 'pi.single_field: $field_id: '.$field_id.'<br><br>';
		}
      
      // II. Find entry_id in case it was not specified in parameters
      if ($entry_id === FALSE)
      {
        $todo2 = "SELECT channel_id FROM exp_channels WHERE channel_name='".$this->EE->db->escape_str($weblog)."' AND site_id = '".$this->EE->db->escape_str($site)."' LIMIT 1";
		  
		  if ($invalid_input === 'alert')
      {
		echo 'pi.single_field: $todo2: '.$todo2.'<br><br>'; 
		  }
		  
        $query2 = $this->EE->db->query($todo2);
        if ($query2->num_rows() == 1)
        {
          $weblog_id = $query2->row('channel_id');
        }
        else
        {
          $weblog_id = FALSE;
        }
		  
		 if ($invalid_input === 'alert')
      {
        echo 'pi.single_field: $weblog_id: '.$weblog_id.'<br><br>';
		 }
		  
        if ($weblog_id !== FALSE)
        {
          $todo3 = "SELECT entry_id FROM exp_channel_titles WHERE channel_id='".$this->EE->db->escape_str($weblog_id)."' AND url_title='".$this->EE->db->escape_str($url_title)."' AND site_id = '".$this->EE->db->escape_str($site)."' LIMIT 1";
		
			if ($invalid_input === 'alert')
      {
		echo 'pi.single_field: $todo3: '.$todo3.'<br><br>';	
			}
			
          $query3 = $this->EE->db->query($todo3);
          if ($query3->num_rows() == 1)
          {
            $entry_id = $query3->row('entry_id');
          }
          else
          {
            $entry_id = FALSE;
          }
        }
		  
		  if ($invalid_input === 'alert')
      {
        echo 'pi.single_field: $entry_id: '.$entry_id.'<br><br>';
		  }
      }
      
      // III. Find values of custom field
      if ($entry_id !== FALSE AND $field_id !== FALSE)
      {
        $todo4 = "SELECT exp_channel_titles.entry_id, exp_channel_titles.title, exp_channel_titles.url_title, exp_channels.channel_name, exp_channels.channel_html_formatting, exp_channels.channel_allow_img_urls, exp_channels.channel_auto_link_urls ".$field_id_clause." 
                  FROM 
                    exp_channel_data
                      INNER JOIN
                    exp_channel_titles
                      ON 
                    exp_channel_data.entry_id = exp_channel_titles.entry_id
                      INNER JOIN
                    exp_channels
                      ON
                    exp_channel_titles.channel_id = exp_channels.channel_id
                  WHERE exp_channel_data.entry_id = '".$this->EE->db->escape_str($entry_id)."' 
                  LIMIT 1";
		  
		  if ($invalid_input === 'alert')
      {
		echo 'pi.single_field: $todo4: '.$todo4.'<br><br>';  
		  }
		  
        $query4 = $this->EE->db->query($todo4);
        //print_r($query4->result_array());
        // the case an entry was found
        if ($query4->num_rows() == 1)
        {
          if ($field_id == 'title')
          {
            $field_value = $query4->row('title');
          }
          elseif ($field_id == 'url_title')
          {
            $field_value = $query4->row('url_title');
          }
          else
          {
            $field_value = $query4->row("field_id_".$field_id);
          }
			
			if ($invalid_input === 'alert')
      {
          echo 'pi.single_field: $field_value: '.$field_value.'<br><br>';
			}
          
          // IV. Format field value
          // If formatting option is not set as parameter of exp:single_field tag, then use relevant value from channel's settings 
          if ($auto_links === FALSE)
          {
            $parse_options['auto_links'] = $query4->row('channel_auto_link_urls');
          }
          if ($html_format === FALSE)
          {
            $parse_options['html_format'] = $query4->row('channel_html_formatting');
          }
          if ($allow_img_url === FALSE)
          {
            $parse_options['allow_img_url'] = $query4->row('channel_allow_img_urls');
          }
          if ($field_is_date == 'yes')
          {
            $field_format = 'none';
          }
          if ($field_format === FALSE)
          {
            $field_format = $query4->row("field_ft_".$field_id);
            //echo '$field_format: '.$field_format.'<br><br>';
            $parse_options['text_format'] = $field_format;
            $field_value = $this->EE->typography->parse_type($field_value, $parse_options);
            $field_value = str_replace( array('&#123;', '&#125;', '{exp'), array(LD, RD, '&#123;exp'), $field_value);
            
			  if ($invalid_input === 'alert')
      {
			echo 'pi.single_field: $field_value: ['.$field_value.']<br><br>';
			  }
			  
          }
          elseif ($field_format === "raw")
          {
            $field_value = trim($field_value);
          }
          else
          {
            $parse_options['text_format'] = $field_format;
            $field_value = $this->EE->typography->parse_type($field_value, $parse_options);
            $field_value = str_replace( array('&#123;', '&#125;', '{exp'), array(LD, RD, '&#123;exp'), $field_value);
            if ($field_is_date == 'yes' AND $date_format !== FALSE)
            {
              $field_value = $this->EE->localize->decode_date($date_format, trim($field_value), $date_localize);
            }
          }
        }
        // the case an entry was not found
        else
        {
          $field_value = '';
			if ($invalid_input === 'alert')
      {
          echo 'pi.single_field: 2 $field_value: '.$field_value.'<br><br>';
			}
        }
        
        // V. Output value of custom field
        $this->return_data = $field_value;
      }
    }
    
    if ($error_encountered === TRUE)
    {
      // V. Output value of custom field
		if ($invalid_input === 'alert')
		  {
			echo 'ERROR! "field" parameter of exp:single_field tag must be defined.<br><br>';
		  }
		else
		{
      		$this->return_data = '';
		}
    }
  }
  // END FUNCTION
  
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------
// This function describes how the plugin is used.
//  Make sure and use output buffering

static function usage()
{
ob_start(); 
?>

PARAMETERS

1) field - Required. Allows you to specify field name.

2) entry_id - Optional. Allows you to specify entry id number.

3) weblog (for EE1.x) - Optional. Allows you to specify weblog short name.

3) channel (for EE2.0 plus) - Optional. Allows you to specify channel short name.

5) url_title - Optional. Allows you to specify url_title.

Either "entry_id" or "weblog/channel" and "url_title" parameters must be defined.

6) field_format - Optional. Allows you to specify how field data should be
formatted. Possible values: "html", "br", "none", "lite", "raw".
In case this parameter is left undefined, format info 
will be retrieved from database. In case parameter's value is "raw",
data will be outputted without any format applied. 

7) site - Optional. Allows you to specify site id number. Default is "1".

8) invalid_input - Optional. Accepts two values: “alert” and “silence”.
Default value is “silence”. If the value is “alert”, then in cases when 
the plugin has some problem with parameters,  PHP alert is being shown;
if the value is “silence”, then in cases when the plugin has 
some problem with parameters, it finishes its work without any alert being shown. 
Set this parameter to “alert” for development, and to “silence” - for deployment.

9) allow_img_url - Optional. Allow inline images? Possible values: "yes" and "no".
If the value is not specified, then the value from channel's preferences will be used.

10) auto_links - Optional. Auto-link URLs and email addresses? Possible values: "yes" and "no".
(Note that auto-linking does not ever occur if  parameter "html_format" is set to "none".)
If the value is not specified, then the value from channel's preferences will be used.

11) encode_email - Optional. Whether or not email addresses are encoded. Possible values: "yes" and "no". Default is "yes".

12) encode_type - Optional. Type of encoding applied to email addresses 
if email address encoding is enabled. Possible values: "javascript " and "noscript".
"noscript" renders in a human readable format (e.g. "name at example dot com)", 
suitable for use where JavaScript is inappropriate, such as in a feed.

13) html_format - Optional. Controls how HTML is handled in text. Possible values: "safe", "all", "none".
If the value is not specified, then the value from channel's preferences will be used.

14) parse_smileys - Optional. Replace text smileys with smiley images? Possible values: "yes" and "no". Default is "yes".

15) field_is_date - Optional. Accepts two values: “yes” and “no”.
Default value is “no”. Set this parameter to "yes" in case custom field contains date.

16) date_format - Optional. Used in case "field_is_date" parameter is set to "yes". Allows to specify ExpressionEngine's
date format.

17) date_localize - Optional. Used in case "field_is_date" parameter is set to "yes". Allows you to specify if the date
should be localized. Accepts two values: “yes” and “no”. Default value is “no”.

EXAMPLE OF USAGE

With "entry_id" parameter defined:

{exp:single_field  field="my_custom_field" entry_id="251" site="1" field_format="xhtml"}

With "channel" and "url_title" parameters defined:

{exp:single_field  field="my_custom_field" channel="my_channel" url_title="my_blog_title" site="1" field_format="raw"}

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END USAGE

}
// END CLASS

?>