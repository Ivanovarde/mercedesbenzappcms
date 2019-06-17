<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



$plugin_info = array(
	'pi_name'			=> 'IV Nav Menu',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Ivano Vardé',
	'pi_author_url'		=> 'http://neomedia.com.ar',
	'pi_description'	=> 'Arma menu de navegación basado en Structure',
	'pi_usage'			=> iv_navmenu::usage()
);

class Iv_navmenu {

	var $return_data = "";

	public $country_code = '';
	public $separator = '';
	public $query = '';
	public $children = '';
	public $levels = '';
	public $counter = 1;
	public $parent_counter = 1;
	public $remove_ids = '';
	public $not_entry_ids = array();
	public $class = '';
	public $mode = '';
	public $from_id = '';
	public $having = '';
	public $title = '';
	public $section = '';
	public $selection = '';
	public $pages_fields = '';
	public $limit = '';
	public $is_footer = false;
	public $is_tabs_body = false;
	public $refresh = '';
	public $options = array(true, 'true', 'y', 'yes', 1, '1', 'on');
	public $debug = false;

	function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->helper('url');

//		$this->return_data = '';
//		return;

		$this->mode = $this->EE->TMPL->fetch_param('mode', 'main'); //footer
		$this->class = $this->EE->TMPL->fetch_param('class', '');
		$this->levels = $this->EE->TMPL->fetch_param('levels', 1);
		$this->separator = $this->EE->TMPL->fetch_param('separator', $this->EE->config->item('word_separator'));
		$this->country_code = $this->EE->TMPL->fetch_param('lang', 'es');
		$this->from_id = $this->EE->TMPL->fetch_param('from_id', '0');
		$this->remove_ids = $this->EE->TMPL->fetch_param('not', '');
		$this->not_entry_ids = ($this->remove_ids) ? ' AND ct.entry_id != ' . str_replace('|', ' AND ct.entry_id != ', $this->remove_ids) : '';
		$this->title = $this->EE->TMPL->fetch_param('title', '');
		$this->section = $this->EE->TMPL->fetch_param('section', '');
		$this->selection = $this->EE->TMPL->fetch_param('selection', '');
		$this->limit = $this->EE->TMPL->fetch_param('limit', '');
		$this->refresh = $this->EE->TMPL->fetch_param('refresh', '');
		$this->debug = $this->EE->TMPL->fetch_param('debug', $this->debug);
		$this->debug = ($this->debug !== 'y' && $this->debug != true) ? false : $this->debug;

//		echo '<br>----------------------------<br>';
//		$vv = $this->EE->config->config; //['global_vars'];
//		ksort($vv);
//		foreach($vv as $item => $val){
//			echo $item . ': ' . $val . '<br>';
//
//		}
//		echo '<br>----------------------------<br>';

		// Create SQL query string
		$this->query = "SELECT ct.entry_id, field_id_3 AS page_menu_title, url_title, status,
		field_id_1 AS page_menu_main, field_id_2 AS page_menu_footer, field_id_116 AS page_menu_top, field_id_64 AS page_main_title_es,
		{pages_fields}
		(SELECT COUNT(st.entry_id) FROM exp_structure st WHERE st.parent_id = ct.entry_id) AS has_children
		FROM exp_channel_titles ct, exp_channel_data cd, exp_structure st
		WHERE
		ct.entry_id = cd.entry_id
		and ct.entry_id = st.entry_id
		AND ct.channel_id = 1
		AND ct.status = 'open'
		{section}
		{not}
		{parent}
		{from_id}
		{selection}
		{having}
		ORDER BY st.lft;";

		switch($this->mode){
			case 'main':
				$this->having = " HAVING page_menu_main = 'y' ";
			break;
			case 'mobile':
				$this->having = " HAVING page_menu_main = 'y' ";
			break;
			case 'top':
				$this->having = " HAVING page_menu_top = 'y' ";
			break;
			case 'pages':
			case 'tabs_body':
			case 'tabs_body_1':
			case 'tabs_body_2':
				$a_tabs = explode('_', $this->mode);
				$this->is_tabs_body = ($a_tabs[1] == 'body') ? true : false;
				$this->having = "";
				$this->pages_fields = ' field_id_51 AS page_copy_' . $this->country_code . ',  field_id_66 AS page_body_' . $this->country_code . ', ';
			break;
			case 'tabs_header':
				$this->having = "";
			break;
			case 'children':
				$this->having = "";
				$this->wrapper_open = '<div {class}>';
				$this->wrapper_close = '</div>';
			break;
			case 'footer':
			case 'footer-1':
			case 'footer-2':
			case 'footer-3':
			case 'footer-4':
				$this->is_footer = true;
				$this->having = " HAVING page_menu_footer = 'y' ";
			break;
		}

		$query = str_replace('{having}', $this->having, $this->query);
		$query = str_replace('{parent}', (!$this->from_id ? ' AND st.parent_id = 0 ' : ''), $query);
		$query = str_replace('{from_id}', ($this->from_id ? ' AND ct.entry_id = ' . $this->from_id : ''), $query);
		$query = str_replace('{section}', ($this->section ? ' AND ct.url_title = "' . $this->section .  '"' : ''), $query);
		$query = str_replace('{not}', ($this->not_entry_ids ? $this->not_entry_ids : ''), $query);
		$query = str_replace('{selection}', '', $query);
		$query = str_replace('{pages_fields}', $this->pages_fields , $query);

//		$this->refresh = false;
//		if($this->mode == 'pages'){
//			$this->refresh = true;
//		}


		if($this->debug){
			echo 'Mode: ' . $this->mode . '<br>Construct:<br> ' . $query . '<br><br>';
		}

		$return = '';

		//###   Setup the Cache   ###
		if (session_id() == "")
		{
			session_start();
		}

		$section = ($this->EE->uri->segment(1) != '') ? $this->EE->uri->segment(1) : 'index';
		$location = ($section != 'index' && $this->EE->uri->segment(2) != '') ? $section . '_' . $this->EE->uri->segment(2) : $section;

		if(!isset($_SESSION['ivano'])){ $_SESSION['ivano'] = array(); }
		if(!isset($_SESSION['ivano']["iv_navmenu"])){ $_SESSION['ivano']["iv_navmenu"] = array(); }
		if(!isset($_SESSION['ivano']["iv_navmenu"][$location])){ $_SESSION['ivano']["iv_navmenu"][$location] = array(); }
		if(!isset($_SESSION['ivano']["iv_navmenu"][$location][$this->mode])){ $_SESSION['ivano']["iv_navmenu"][$location][$this->mode] = ''; }

		if($this->debug && $this->is_tabs_body){
			echo 'is_tabs_body: ' . $this->is_tabs_body . ' | ' . in_array($this->refresh, $this->options);
		}

		//Seteo los datos en el cache de session;
		if($_SESSION['ivano']["iv_navmenu"][$location][$this->mode] != '' && !in_array($this->refresh, $this->options)){
			//echo '<br>' . $this->mode . ': cache!!<br>';
			$this->return_data = $_SESSION['ivano']["iv_navmenu"][$location][$this->mode];

		}
		else{
			//echo '<br>' . $this->mode . ': NO cache!!<br>';
			// Perform SQL query
			$query = $this->EE->db->query($query);

			//Find number of entries
			if($query->num_rows() > 0){

				$result = $query->result_array();

				$ul_open = '<ul {class} >';
				$ul_close = '</ul>';
				$li_open = '<li {url_title} {class}>';
				$li_close = '</li>';
				$a_children = array();

				// UL OPEN
				if(!$this->is_tabs_body){
					if($this->mode == 'children'){
						$return .= str_replace('{class}', ' class="' . $this->class . '"', $this->wrapper_open);
						if($this->title){
							$return .= '<h4 class="header border-box box-shadow"><span class="fa fa-play"></span>' . $this->title . '</h4>';
						}
						$return .= str_replace('{class}', ' class="border-box box-shadow" ', $ul_open);
					}else{
						$return .= str_replace('{class}', ' class="' . $this->class . '" ', $ul_open);
					}
				}

				if($this->is_footer && $this->title){
					$return .= '<li><h5>' . $this->title . '</h5></li>';
				}

				foreach($result as $entry){

					if(!in_array($entry['entry_id'], $this->not_entry_ids)){

						$this->counter = 1;

						if($this->debug){
							echo 'Parent cunter: ' . $this->parent_counter . '<br>';
						}

						$url_title = $this->EE->config->item('site_url') . '/' . ($entry['url_title'] == 'home' ? '' : $this->make_url($entry['url_title']) . '/');

						// LI OPEN
						if($this->mode != 'pages' && $this->mode != 'children' && $this->mode != 'tabs_header' && !$this->is_tabs_body){
							$uri_segment_1 = $this->EE->uri->segment(1);
							$active_class = ($uri_segment_1 == $entry['url_title'] || ($entry['url_title'] == 'home' && ($uri_segment_1 == '' || $uri_segment_1 == 'index'))) ? ' class="active" ' : '';
							$return .= str_replace(array('{url_title}', '{class}'), array('data-section="' . $entry['url_title'] . '"', $active_class) , $li_open);
						}

						if($this->mode != 'pages' && $this->mode != 'children' && $this->mode != 'tabs_header' && !$this->is_tabs_body){
							if($this->is_footer && !$this->title){
								$return .= '<h5>' . $entry['page_menu_title'] . '</h5>';
							}else{
								$return .= '<a class="menu-title" href="' . $url_title . '">' . $entry['page_menu_title'] . '</a>';
							}
						}

						if($this->debug){
							echo '<br>child: ' . $entry['has_children'] . ' | lvls: ' . $this->levels . ' > count: ' . $this->counter . '<br><br>';
						}

						if($entry['has_children'] > 0 && ($this->levels > $this->counter)){

							$this->children = '';

							self::get_children($entry);

							$return .= $this->children;

						}

						// LI CLOSE
						if($this->mode != 'pages' && $this->mode != 'children' && $this->mode != 'tabs_header' && !$this->is_tabs_body){
							$return .= $li_close;
						}

					}

					$this->parent_counter++;

				}

				if(!$this->is_tabs_body){
					if($this->mode == 'children'){
						$return .= $ul_close;
						$return .= $this->wrapper_close;
					}else{
						$return .= $ul_close;
					}
				}

				$_SESSION['ivano']["iv_navmenu"][$location][$this->mode] = $return;
				$this->return_data = $return;
				unset($return);

			}

		}

//		echo '<br><br>Modo ' . $this->mode . ': <br>';
//		echo $_SESSION['ivano']["iv_navmenu"][$this->mode] . '<br>';

	}
	// END CONSTRUCT

	function get_children($element){
		// Create SQL query string
		$query = str_replace('{having}', /*$this->having*/'', $this->query);
		$query = str_replace('{parent}', ' AND st.parent_id = ' . $element['entry_id'], $query);
		$query = str_replace('{from_id}', '', $query);
		$query = str_replace('{section}', '', $query);
		$query = str_replace('{not}', ($this->not_entry_ids ? $this->not_entry_ids : ''), $query);
		$query = str_replace('{selection}', ($this->selection ? ' AND (ct.url_title = "' . str_replace('|', '" OR ct.url_title = "', trim($this->selection, '\|, ')) .  '")' : ''), $query);
		$query = str_replace('{pages_fields}', $this->pages_fields , $query);

		$this->counter++;

		if($this->debug){
			echo 'get_children: (' . $this->counter . ')<br>' . $query . '<br><br>';
		}

		// Perform SQL query
		$query = $this->EE->db->query($query);

		//Find number of entries
		if($query->num_rows() > 0){

			$result = $query->result_array();

			// UL OPEN
			if($this->mode != 'pages' && $this->mode != 'children' && $this->mode != 'tabs_header' && !$this->is_tabs_body ){
				$this->children .= '<ul ' . ($this->mode == 'mobile' ? 'class="dl-submenu"' : '') . '>';
				$this->children .= ($this->mode == 'mobile' ? '<li class="dl-back"><a href="#">back</a></li>' : '');
			}

			$odd = '';

			foreach($result as $entry){

				if(!in_array($entry['entry_id'], $this->not_entry_ids)){

					//$url_title = $this->EE->config->item('site_url') . '/' . $element['url_title'] . '/' . $this->make_url($entry['url_title']) . '/';

					$url_title = $this->EE->config->item('site_url') . '/' . ($this->is_tabs_body && $this->EE->uri->segment(1) == 'preguntas-frecuentes' ? $this->EE->uri->segment(1) . '/' .  $element['url_title'] : $element['url_title']) . '/' . $this->make_url($entry['url_title']) . '/';

					if($this->is_tabs_body){

						$this->children .= '<div class="panel panel-default">

							<div class="panel-heading ' . $odd . '">
								<h4 class="panel-title">
									<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="' . $url_title . '" data-target="#' . $this->make_url($entry['url_title']) . '"><span class="fa fa-angle-double-right"></span> ' . $entry['page_main_title_' . $this->country_code] . '</a>
								</h4>
							</div>

							<div id="' . $this->make_url($entry['url_title']) . '" class="panel-collapse collapse">
								<div class="panel-body rich">' . $entry['page_copy_' . $this->country_code] . '</div>
							</div>

						</div>';

					}else{

						if($this->mode != 'pages'){

							$arrow = ($this->mode == 'children') ? '<span class="fa fa-angle-double-right"></span>' : '';

							// LI OPEN
							$this->children .= '<li ' . ($this->EE->uri->segment(2) == $entry['url_title'] ? 'class="active"' : '') . '>';

							if($this->mode == 'tabs_header'){
								$this->children .= '<a class="h4" data-toggle="tab" data-target="#' . $entry['url_title'] . '" href="#">' .  $entry['page_main_title_' . $this->country_code] .'</a>';
							}else{
								$this->children .= '<a href="' . $url_title . '">' . $arrow . $entry['page_menu_title'] . '</a>';
							}

						}else{
							// LI OPEN
							$this->children .= '<li>';
							$this->children .= '<h4><span class="fa fa-angle-double-right"></span><a href="' . $url_title . '">' . $entry['page_main_title_es'] . '</a></h4>';
							$this->children .= '<div class="entry-preview">';
							$this->children .= ($entry['page_copy_' . $this->country_code] ? self::limit_words(strip_tags($entry['page_copy_' . $this->country_code]), $this->limit) : self::limit_words(strip_tags($entry['page_body_' . $this->country_code]), $this->limit));
							$this->children .= '</div>';
							$this->children .= '<div class="entry-btn"><a class="btn-sm btn-primary pull-right" href="' . $url_title . '">Ver más</a></div>';
							$this->children .= '</li>';

						}

					}

					$odd = ($odd == '') ? 'odd' : '';

					if($this->levels > $this->counter){

						$this->children .= self::get_children($entry['entry_id']);

					}

					if(!$this->is_tabs_body){
						// LI CLOSE
						$this->children .= '</li>';
					}

				}

			}

			if($this->mode != 'pages' && $this->mode != 'children' && $this->mode != 'tabs_header'  && !$this->is_tabs_body ){
				$this->children .= '</ul>';
			}

		}

		//echo '<br>Children: ' . $this->children . '<br>';

	}
	// END get_children

	public static function limit_words($str, $limit){

		$a_words = explode(' ', $str);
		$total_words = count($a_words);

		$limit = (int)$limit;

		$str = strip_tags(utf8_encode($str));
		$str = preg_replace('/\s+/', ' ', $str);

		if ($total_words > $limit){

		   $words = array_splice($a_words, 0, $limit);
		   $str_output = implode(' ', $words);
		   $str_output .= "&hellip;";

		}else{

		   $str_output = $str;
		}

		return $str_output;
	}

	function make_url($string){
		return url_title($string, $this->separator, true);
	}

	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------
	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	static function usage()
	{
		ob_start();
		?>

		Arma menu de navegación basado en Structure

		Parametros:

		mode: string (opcional def: main). main / footer
		levels: string (opcional def: 1). Niveles de submenues
		separator: string (opcional def: [system]). dash/underscore. Se usa para los valores que necesiten tener formato de url_title
		class: string (opcional def: ''). Propiedad class para el UL
		lang: string (opcional def: es). Codigo de lenguaje es/en/it... etc


		{exp:iv_navmenu levels="2" class="sf-menu nav navbar-nav" lang="en"}


		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	// END USAGE

}
// END CLASS
?>
