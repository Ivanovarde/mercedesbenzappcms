<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Logiq pagination
 *
 * @package Logiq_pagination
 * @version 2.0
 * @author	Alexander Jones alex@logiqdesign.com
 * @copyright Logiq Design.
 * @link http://www.logiqdesign.com
 */

class Logiq_pagination_ext
{
   var $settings = array();
    var $name = 'Logiq Pagination';
    var $version = '2.0';
    var $description = 'Logiq Pagination allows you to modify pagination and style tag elements';
    var $settings_exist = 'y';
    var $docs_url = 'http://www.logiqdesign.com/blog/index.php/site/comments/pagination_2';
    
    function Logiq_pagination_ext($settings = '')
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }
	

	
	
	/**
 * Settings Form
 *
 * @param	Array	Settings
 * @return 	void
 */
function settings_form()
{
	$this->EE->load->helper('form');
	$this->EE->load->library('table');
	
	$vars = array();
	
		  $site_id = $this->EE->config->item('site_id');
			

			$results = $this->EE->db->query("SELECT * FROM exp_logiq_pagination WHERE site_id = '".$site_id."'");	
		
$the_site_id = $this->EE->config->item('site_id');
$first_marker = $results->row('first_marker');
	$last_marker = $results->row('last_marker');
	$next_link = $results->row('next_link');
	$prev_link = $results->row('prev_link');
	$text_first = $results->row('text_first');
	$text_last = $results->row('text_last');
	$maxlinks = $results->row('maxlinks');
	$first_p_open = $results->row('first_p_open');
	$first_p_close = $results->row('first_p_close');
	$next_p_open = $results->row('next_p_open');
	$next_p_close = $results->row('next_p_close');
	$prev_p_open = $results->row('prev_p_open');
	$prev_p_close = $results->row('prev_p_close');
	$num_p_open = $results->row('num_p_open');
	$num_p_close = $results->row('num_p_close');
	$cur_p_open = $results->row('cur_p_open');
	$cur_p_close = $results->row('cur_p_close');
	$last_p_open = $results->row('last_p_open');
	$last_p_close = $results->row('last_p_close');
	$get_last_p_index = $results->row('get_last_p_index');
	$set_last_p_index =$results->row('set_last_p_index');
	$use_num_for_last = $results->row('use_num_for_last');
	$p_links_open =  $results->row('p_links_open');
	$p_links_close = $results->row('p_links_close');
	$pre_start_tag =  $results->row('pre_start_tag');
	$pre_end_tag =  $results->row('pre_end_tag');

	
	
	$yes_no_options = array(
		'yes' 	=> lang('yes'), 
		'no'	=> lang('no')
	);
	
	$vars['settings'] = array(
		
		'site_id'	=> $this->EE->config->item('site_id'),
		'first_marker'	=> form_input('first_marker', $first_marker),
		'last_marker'	=> form_input('last_marker', $last_marker),
		'next_link'	=> form_input('next_link', $next_link),
		'prev_link'	=> form_input('prev_link', $prev_link),
		'text_first'	=> form_input('text_first', $text_first),
		'text_last'	=> form_input('text_last', $text_last),
		'maxlinks'	=> form_input('maxlinks', $maxlinks),
		'first_p_open'	=> form_input('first_p_open', $first_p_open),
		'first_p_close'	=> form_input('first_p_close', $first_p_close),
		'next_p_open'	=> form_input('next_p_open', $next_p_open),
		'next_p_close'	=> form_input('next_p_close', $next_p_close),
		'prev_p_open'	=> form_input('prev_p_open', $prev_p_open),
		'prev_p_close'	=> form_input('prev_p_close', $prev_p_close),
		'num_p_open'	=> form_input('num_p_open', $num_p_open),
		'num_p_close'	=> form_input('num_p_close', $num_p_close),
		'cur_p_open'	=> form_input('cur_p_open', $cur_p_open),
		'cur_p_close'	=> form_input('cur_p_close', $cur_p_close),
		'last_p_open'	=> form_input('last_p_open', $last_p_open),
		'last_p_close'	=> form_input('last_p_close', $last_p_close),
		'get_last_p_index'	=> form_input('get_last_p_index', $get_last_p_index),
		'set_last_p_index'	=> form_input('set_last_p_index', $set_last_p_index),
		'p_links_open'	=> form_input('p_links_open', $p_links_open),
		'p_links_close'	=> form_input('p_links_close', $p_links_close),
		'pre_start_tag'	=> form_input('pre_start_tag', $pre_start_tag),
		'pre_end_tag'	=> form_input('pre_end_tag', $pre_end_tag),
		'use_num_for_last' => form_dropdown(
					'use_num_for_last',
					array(
		'yes' 	=> lang('yes'), 
		'no'	=> lang('no')
	), 
					$use_num_for_last)
		);

	
	return $this->EE->load->view('index', $vars, TRUE);			
}

/**
 * Save Settings
 *
 * This function provides a little extra processing and validation 
 * than the generic settings form.
 *
 * @return void
 */
function save_settings()
{
	if (empty($_POST))
	{
		show_error($this->EE->lang->line('unauthorized_access'));
	}
	
	unset($_POST['submit']);

	$this->EE->lang->loadfile('logiq_pagination');


	$site_id = $this->EE->config->item('site_id');
	$first_marker = $this->EE->input->post('first_marker');
	$last_marker = $this->EE->input->post('last_marker');
	$next_link = $this->EE->input->post('next_link');
	$prev_link = $this->EE->input->post('prev_link');
	$text_first = $this->EE->input->post('text_first');
	$text_last = $this->EE->input->post('text_last');
	$first_p_open = $this->EE->input->post('first_p_open');
	$first_p_close = $this->EE->input->post('first_p_close');
	$next_p_open = $this->EE->input->post('next_p_open');
	$next_p_close = $this->EE->input->post('next_p_close');
	$prev_p_open = $this->EE->input->post('prev_p_open');
	$prev_p_close = $this->EE->input->post('prev_p_close');
	$num_p_open = $this->EE->input->post('num_p_open');
	$num_p_close = $this->EE->input->post('num_p_close');
	$cur_p_open = $this->EE->input->post('cur_p_open');
	$cur_p_close = $this->EE->input->post('cur_p_close');
	$last_p_open = $this->EE->input->post('last_p_open');
	$last_p_close = $this->EE->input->post('last_p_close');
	$get_last_p_index = $this->EE->input->post('get_last_p_index');
	$set_last_p_index = $this->EE->input->post('set_last_p_index');
	$p_links_open = $this->EE->input->post('p_links_open');
	$p_links_close = $this->EE->input->post('p_links_close');
	$pre_start_tag = $this->EE->input->post('pre_start_tag');
	$pre_end_tag = $this->EE->input->post('pre_end_tag');
	$use_num_for_last = $this->EE->input->post('use_num_for_last');
	$maxlinks = $this->EE->input->post('maxlinks');
	
	
	$data = array(
	'site_id' => $site_id,
	'first_marker' => $first_marker,
	'last_marker' => $last_marker,
	'next_link' => $next_link,
	'prev_link' => $prev_link,
	'text_first' => $text_first,
	'text_last' => $text_last,
	'first_p_open' => $first_p_open,
	'first_p_close' => $first_p_close,
	'next_p_open' => $next_p_open,
	'next_p_close' => $next_p_close,
	'prev_p_open' => $prev_p_open,
	'prev_p_close' => $prev_p_close,
	'num_p_open' => $num_p_open,
	'num_p_close' => $num_p_close,
	'cur_p_open' => $cur_p_open,
	'cur_p_close' => $cur_p_close,
	'last_p_open' => $last_p_open,
	'last_p_close' => $last_p_close,
	'get_last_p_index' => $get_last_p_index,
	'set_last_p_index' => $set_last_p_index,
	'p_links_open' => $p_links_open,
	'p_links_close' => $p_links_close,
	'pre_start_tag' => $pre_start_tag,
	'pre_end_tag' => $pre_end_tag,
	'first_p_open' => $first_p_open,
	'use_num_for_last' => $use_num_for_last,
	'maxlinks' => $maxlinks

	
	);
	
	global $existingsite;
		$results = $this->EE->db->query("SELECT * FROM exp_logiq_pagination");

if ($results->num_rows() > 0)
{
    foreach($results->result_array() as $row)
    {
        
		if( $row['site_id'] == $site_id ){
			$existingsite = TRUE;
		}
	
	}
}

if ($existingsite == TRUE){
	$sql = $this->EE->db->update_string('exp_logiq_pagination', $data, "site_id = '".$site_id."'");

// UPDATE exp_channel SET name = 'Joe', email = 'joe@joe.com', url = 'www.joe.com' WHERE author_id = '1'
}
else{
$sql = $this->EE->db->insert_string('exp_logiq_pagination', $data);

}
$this->EE->db->query($sql);


	
	
	
	$this->EE->session->set_flashdata(
		'message_success',
	 	$this->EE->lang->line('preferences_updated')
	);
}



    
    function create_new_pagination(&$data)
    {
        $data->EE->extensions->end_script = TRUE;
        $query                            = $data->EE->db->query($data->pager_sql);
        
        $count  = (isset($query->num_rows)) ? $query->num_rows : false;
        $offset = (!$data->EE->TMPL->fetch_param('offset') OR !is_numeric($data->EE->TMPL->fetch_param('offset'))) ? '0' : $data->EE->TMPL->fetch_param('offset');
        $count  = $count - $offset;
        
        if ($data->paginate == TRUE) {
            if (($data->EE->uri->uri_string == '' OR $data->EE->uri->uri_string == '/') && $data->EE->config->item('template_group') != '' && $data->EE->config->item('template') != '') {
                $data->basepath = $data->EE->functions->create_url($data->EE->config->slash_item('template_group') . '/' . $data->EE->config->item('template'));
            }
            
            if ($data->basepath == '') {
                $data->basepath = $data->EE->functions->create_url($data->EE->uri->uri_string);
                
                if (preg_match("#^P(\d+)|/P(\d+)#", $data->query_string, $match)) {
                    $data->p_page   = (isset($match[2])) ? $match[2] : $match[1];
                    $data->basepath = $data->EE->functions->remove_double_slashes(str_replace($match[0], '', $data->basepath));
                }
            }
            
            //  Standard pagination - base values
            
            if ($data->field_pagination == FALSE) {
                if ($data->display_by == '') {
                    if ($count == 0) {
                        $data->sql = '';
                        return;
                    }
                    
                    $data->total_rows = $count;
                }
                
                if ($data->dynamic_sql == FALSE) {
                    $cat_limit = FALSE;
                    if ((in_array($data->reserved_cat_segment, explode("/", $data->EE->uri->uri_string)) AND $data->EE->TMPL->fetch_param('dynamic') != 'no' AND $data->EE->TMPL->fetch_param('channel')) OR (preg_match("#(^|\/)C(\d+)#", $data->EE->uri->uri_string, $match) AND $data->EE->TMPL->fetch_param('dynamic') != 'no')) {
                        $cat_limit = TRUE;
                    }
                    
                    if ($cat_limit AND is_numeric($data->EE->TMPL->fetch_param('cat_limit'))) {
                        $data->p_limit = $data->EE->TMPL->fetch_param('cat_limit');
                    } else {
                        $data->p_limit = (!is_numeric($data->EE->TMPL->fetch_param('limit'))) ? $data->limit : $data->EE->TMPL->fetch_param('limit');
                    }
                }
                
                $data->p_page = ($data->p_page == '' OR ($data->p_limit > 1 AND $data->p_page == 1)) ? 0 : $data->p_page;
                
                if ($data->p_page > $data->total_rows) {
                    $data->p_page = 0;
                }
                
                $data->current_page = floor(($data->p_page / $data->p_limit) + 1);
                
                $data->total_pages = intval(floor($data->total_rows / $data->p_limit));
            } else {
                //  Field pagination - base values
                
                if ($count == 0) {
                    $data->sql = '';
                    return;
                }
                
                $m_fields = array();
                
                foreach ($data->multi_fields as $val) {
                    foreach ($data->cfields as $site_two_id => $cfields) {
                        if (isset($cfields[$val])) {
                            if (isset($row['field_id_' . $cfields[$val]]) AND $row['field_id_' . $cfields[$val]] != '') {
                                $m_fields[] = $val;
                            }
                        }
                    }
                }
                
                $data->p_limit = 1;
                
                $data->total_rows = count($m_fields);
                
                $data->total_pages = $data->total_rows;
                
                if ($data->total_pages == 0)
                    $data->total_pages = 1;
                
                $data->p_page = ($data->p_page == '') ? 0 : $data->p_page;
                
                if ($data->p_page > $data->total_rows) {
                    $data->p_page = 0;
                }
                
                $data->current_page = floor(($data->p_page / $data->p_limit) + 1);
                
                if (isset($m_fields[$data->p_page])) {
                    $data->EE->TMPL->tagdata                              = preg_replace("/" . LD . "multi_field\=[\"'].+?[\"']" . RD . "/s", LD . $m_fields[$data->p_page] . RD, $data->EE->TMPL->tagdata);
                    $data->EE->TMPL->var_single[$m_fields[$data->p_page]] = $m_fields[$data->p_page];
                }
            }
            
            //  Load CodeIgniter Pagination Library
            $this->EE->load->library('pagination');
            if ($data->total_rows % $data->p_limit) {
                $data->total_pages++;
            }
            
            if ($data->total_rows > $data->p_limit) {
                if (strpos($data->basepath, SELF) === FALSE && $data->EE->config->item('site_index') != '') {
                    $data->basepath .= SELF;
                }
                
                if ($data->EE->TMPL->fetch_param('paginate_base')) {
                    $data->EE->load->helper('string');
                    
                    $data->basepath = $data->EE->functions->create_url(trim_slashes($data->EE->TMPL->fetch_param('paginate_base')));
                }
                
	
				  $site_id = $this->EE->config->item('site_id');
			//	  $results = $this->EE->db->query("SELECT * FROM exp_logiq_pagination WHERE site_id = '1'");

			$results = $this->EE->db->query("SELECT * FROM exp_logiq_pagination WHERE site_id = '".$site_id."'");	
				
				$ld_first_marker = $results->row('first_marker');
				$ld_last_marker = $results->row('last_marker');
				$ld_max_links = $results->row('maxlinks');
				$ld_first_link = $results->row('text_first');
				$ld_last_link = $results->row('text_last');
				$ld_first_tag_open = $results->row('first_p_open');
				$ld_first_tag_close = $results->row('first_p_close');
				$ld_last_tag_open = $results->row('last_p_open');
				$ld_last_tag_close = $results->row('last_p_close');
				$ld_next_tag_open = $results->row('next_p_open');
				$ld_next_tag_close = $results->row('next_p_close');
				$ld_prev_tag_open = $results->row('prev_p_open');
				$ld_prev_tag_close = $results->row('prev_p_close');
				$ld_cur_tag_open = $results->row('cur_p_open');
				$ld_cur_tag_close = $results->row('cur_p_close');
				$ld_num_tag_open = $results->row('num_p_open');
				$ld_num_tag_close = $results->row('num_p_close');
				$ld_use_num_for_last = $results->row('use_num_for_last');
				$ld_next_link = $results->row('next_link');
				$ld_prev_link = $results->row('prev_link');
				$ld_get_last_p_index = $results->row('get_last_p_index');
				$ld_set_last_p_index = $results->row('set_last_p_index');
				$ld_pagination_links_open = $results->row('p_links_open');
				$ld_pagination_links_close = $results->row('p_links_close');
				$ld_pre_start_tag = $results->row('pre_start_tag');
				$ld_pre_end_tag = $results->row('pre_end_tag');
	
		
                $p_config['base_url']        = $data->basepath;
                $p_config['prefix']          = 'P';
                $p_config['total_rows']      = $data->total_rows;
                $p_config['cur_page']        = $data->p_page;
                $p_config['per_page']        = $data->p_limit;
                $p_config['first_marker']    = $ld_first_marker;
                $p_config['last_marker']     = $ld_last_marker;
                $p_config['num_links']       = $ld_max_links;
                $p_config['first_link']      = $ld_first_marker . ' ' .$ld_first_link;
                $p_config['first_tag_open']  = $ld_first_tag_open;
                $p_config['first_tag_close'] = $ld_first_tag_close;
                $p_config['last_link']       = $ld_last_marker . ' ' .$ld_last_link;
                $p_config['last_tag_open']   = $ld_last_tag_open;
                $p_config['last_tag_close']  = $ld_last_tag_close;
                $p_config['next_link']       = $ld_next_link;
                $p_config['next_tag_open']   = $ld_next_tag_open;
                $p_config['next_tag_close']  = $ld_next_tag_close;
                $p_config['prev_link']       = $ld_prev_link;
                $p_config['prev_tag_open']   = $ld_prev_tag_open;
                $p_config['prev_tag_close']  = $ld_prev_tag_close;
                $p_config['cur_tag_open']    = $ld_cur_tag_open;
                $p_config['cur_tag_close']   = $ld_cur_tag_close;
                $p_config['num_tag_open']    = $ld_num_tag_open;
                $p_config['num_tag_close']   = $ld_num_tag_close;
				
               
                if ($ld_use_num_for_last == 'yes') {
                    $p_config['last_link'] = $data->total_pages;
                }
               
                
                
                $this->EE->pagination->initialize($p_config);
                
                $data->pagination_links = $ld_pagination_links_open;
                $data->pagination_links .= $ld_pre_start_tag;
                $data->pagination_links .= $this->EE->pagination->create_links();
                
                $tmp = strlen($ld_get_last_p_index);
                
                if ($tmp > 0) {
                    $start                  = $this->lastIndexOf($data->pagination_links, $ld_get_last_p_index);
                    $data->pagination_links = substr_replace($data->pagination_links, $ld_set_last_p_index, $start, $tmp);
                    
                }
                
                $data->pagination_links .= $ld_pre_end_tag;
                $data->pagination_links .= $ld_pagination_links_close;
                
              
                
                if ((($data->total_pages * $data->p_limit) - $data->p_limit) > $data->p_page) {
                    $data->page_next = $data->basepath . 'P' . ($data->p_page + $data->p_limit) . '/';
                }
                
                if (($data->p_page - $data->p_limit) >= 0) {
                    $data->page_previous = $data->basepath . 'P' . ($data->p_page - $data->p_limit) . '/';
                }
                
                
                
                
                
            } else {
                $data->p_page = '';
            }
            
            
            
            
        }
        
        
    }
    
    
    
    
    
    
    
    function lastIndexOf($string, $item)
    {
        $index = strpos(strrev($string), strrev($item));
        if ($index) {
            $index = strlen($string) - strlen($item) - $index;
            return $index;
        } else
            return -1;
    }
    
    
    function activate_extension()
    {
	
	
/*
        $default_settings = serialize(array(
			'site_id' => $site_id,
            'first_marker' => '&laquo;',
            'last_marker' => '&raquo;',
            'next_link' => '&gt;',
            'prev_link' => '&lt;',
            'text_first' => 'First',
            'text_last' => 'Last',
            'maxlinks' => '4',
            'first_p_open' => '<li class="first">',
            'first_p_close' => '</li>',
            'next_p_open' => '<li class="next">',
            'next_p_close' => '</li>',
            'prev_p_open' => '<li class="prev">',
            'prev_p_close' => '</li>',
            'num_p_open' => '<li>',
            'num_p_close' => '</li>',
            'cur_p_open' => '<li class="active">',
            'cur_p_close' => '</li>',
            'last_p_open' => '<li class="last">',
            'last_p_close' => '</li>',
            'get_last_p_index' => '<li>',
            'set_last_p_index' => '<li id="end-pagination">',
            'p_links_open' => '<div id="pagination">',
            'p_links_close' => '</div>',
            'pre_start_tag' => '<ul>',
            'pre_end_tag' => '</ul>',
            'use_num_for_last' => 'no'
        ));
        
*/



	$this->EE->load->dbforge();
		
	$fields = array(
		
						'logiq_pagination_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
						'site_id'			=> array('type' 		 => 'int',
													'constraint'	 => '4',
													'unsigned'		 => TRUE,
													'auto_increment' => FALSE,
													'default' 		 => '1'),
						'first_marker'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '&laquo;'),
						'last_marker'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '&raquo;'),
						'next_link'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '&gt;'),
						'prev_link'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '&lt;'),
						'text_first'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => 'First'),
						'text_last'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => 'Last'),
						'maxlinks'			=> array('type' => 'int', 'unsigned' => TRUE, 'constraint' => '4', 'default' => '4'),
						'first_p_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="first">'),
						'first_p_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</li>'),
						'next_p_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="next">'),
						'next_p_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</li>'),
						'prev_p_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="prev">'),
						'prev_p_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</li>'),
						'num_p_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="digitlink">'),
						'num_p_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</li>'),
						'cur_p_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="current">'),
						'cur_p_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</li>'),
						'last_p_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="last">'),
						'last_p_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</li>'),
						'get_last_p_index'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li>'),
						'set_last_p_index'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<li class="end-pagination">'),
						'p_links_open'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<div id="pagination">'),
						'p_links_close'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</div>'),
						'pre_start_tag'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '<ul>'),
						'pre_end_tag'			=> array('type' => 'varchar', 'constraint' => '250', 'default' => '</ul>'),
						'use_num_for_last'			=> array('type' => 'varchar', 'constraint' => '6', 'default' => 'no')
						);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('logiq_pagination_id', TRUE);

		$this->EE->dbforge->create_table('logiq_pagination');
		
		
		
		unset($fields);
		
		
		$results = $this->EE->db->query("SELECT * FROM exp_sites");

if ($results->num_rows() > 0)
{
    foreach($results->result_array() as $row)
    {
        
        $default_settings = array(
			'site_id' => $row['site_id'],
            'first_marker' => '&laquo;',
            'last_marker' => '&raquo;',
            'next_link' => '&gt;',
            'prev_link' => '&lt;',
            'text_first' => 'First',
            'text_last' => 'Last',
            'maxlinks' => '4',
            'first_p_open' => '<li class="first">',
            'first_p_close' => '</li>',
            'next_p_open' => '<li class="next">',
            'next_p_close' => '</li>',
            'prev_p_open' => '<li class="prev">',
            'prev_p_close' => '</li>',
            'num_p_open' => '<li>',
            'num_p_close' => '</li>',
            'cur_p_open' => '<li class="active">',
            'cur_p_close' => '</li>',
            'last_p_open' => '<li class="last">',
            'last_p_close' => '</li>',
            'get_last_p_index' => '<li>',
            'set_last_p_index' => '<li id="end-pagination">',
            'p_links_open' => '<div id="pagination">',
            'p_links_close' => '</div>',
            'pre_start_tag' => '<ul>',
            'pre_end_tag' => '</ul>',
            'use_num_for_last' => 'no'
        );
		
			


$sql = $this->EE->db->insert_string('exp_logiq_pagination', $default_settings);

// INSERT INTO exp_channel (name, email, url) VALUES ('Joe', 'joe@joe.com', 'www.joe.com')

$this->EE->db->query($sql);
		
		
    }
}

	


        $this->EE->db->insert('exp_extensions', array(
            'extension_id' => '',
            'class' => __CLASS__,
            'method' => "create_new_pagination",
            'hook' => "channel_module_create_pagination",
           // 'settings' => $default_settings,
			'settings' => '',
            'priority' => 10,
            'version' => $this->version,
            'enabled' => "y"
        ));

        
    }
    function disable_extension()
    {
	$this->EE->load->dbforge();

        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('exp_extensions');
		$this->EE->dbforge->drop_table('logiq_pagination');

    }
    
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version) {
            return FALSE;
        }
        
        $data = array();
        
        $data['version'] = $this->version;
        
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->update('exp_extensions', $data);
    }
}
/* End of file ext.logiq_pagination.php */
/* Location: ./system/expressionengine/third_party/logiq_pagination/ext.logiq_pagination.php */