<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * URL Encode
 */

$plugin_info = array(
	'pi_name'     => 'URL Encode',
	'pi_version'    => '1.0.0',
	'pi_author'     => 'Michael Watts',
	'pi_author_url'   => 'http://michaelwatts.me/',
	'pi_description'  => 'Returns an encoded url',
	'pi_usage'      => Url_encode::usage()
);

/**
 * Returns an encoded url
 * within the tag pair.
 *
 * @package Url_encode
 */

class Url_encode {
  public $return_data = "";

  public function Url_encode()
  {
    ee()->load->helper('url');
    $this->EE =& get_instance();
    $tagdata = $this->EE->TMPL->tagdata;

    // check for standard global variables in curly braces
    if (strpos($tagdata,'{site_url}') !== false) {
      $tagdata = str_replace('{site_url}', '', $tagdata);
      $this->return_data = (string)urlencode(base_url().$tagdata);
    } elseif (strpos($tagdata,'{homepage}') !== false) {
      $tagdata = str_replace('{homepage}', '', $tagdata);
      $this->return_data = (string)urlencode(site_url().$tagdata);
    }
    else {
      $this->return_data = (string)urlencode($tagdata);
    }
    return;
  }

  /**
   * ExpressionEngine plugins require this for displaying
   * usage in the control panel
   * @access public
   * @return string
   */
    public function usage()
  {
    ob_start();
?>

    {exp:url_encode} http://www.michaelwatts.me/ {/exp:url_encode}

      Outputs: http%3A%2F%2Fwww.michaelwatts.me%2F


    You can also use EE standard global variables {site_url} and {homepage} inside the tag:


    {exp:url_encode} {site_url}/your-page {/exp:url_encode}

    Outputs: http%3A%2F%2Fwww.michaelwatts.me%2Fyour-page


    {exp:url_encode} {homepage}/your-page {/exp:url_encode}

    Outputs: http%3A%2F%2Fwww.michaelwatts.me%2Findex.php%2Fyour-page


    Note: {site_url} and {homepage} return whatever is in your config

<?php
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
  }

}

/* End of file pi.url_encode.php */
/* Location: ./system/expressionengine/third_party/url_encode/pi.url_encode.php */
