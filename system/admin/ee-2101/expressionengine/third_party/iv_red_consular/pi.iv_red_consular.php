<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



$plugin_info = array(
	'pi_name'			=> 'IV Red Consular',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Ivano Vardé',
	'pi_author_url'		=> 'http://neomedia.com.ar',
	'pi_description'	=> 'Arma dropdowns, ul y ol de consulados y provincias en donde hay consulados',
	'pi_usage'			=> iv_red_consular::usage()
);

class Iv_red_consular {

	var $return_data = "";

	public $country_code = '';
	public $separator = '';
	public $placeholder = '';
	public $selected = '';
	public $extra = '';

	function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->helper('url');

		$mode = $this->EE->TMPL->fetch_param('mode', '');
		$type = $this->EE->TMPL->fetch_param('type', 'dropdown');
		$this->placeholder = $this->EE->TMPL->fetch_param('placeholder', '...');
		$this->selected = $this->EE->TMPL->fetch_param('selected', '');
		$this->extra = $this->EE->TMPL->fetch_param('extra', '');
		$this->separator = $this->EE->TMPL->fetch_param('separator', $this->EE->config->item('word_separator'));
		$this->country_code = $this->EE->TMPL->fetch_param('lang', 'es');

//		if($separator != $this->EE->config->item('word_separator')){
//
//			$temp_separator = $this->EE->config->item('word_separator');
//
//			$this->EE->config->set_item('word_separator', $separator);
//
//		}

		// Create SQL query string
		$query = "SELECT t.entry_id, title, url_title, status, field_id_122 as page_provincia, field_id_66 as page_body_es, field_id_64 as page_main_title_es, field_id_123 as page_jurisdiction_es
FROM exp_channel_titles t, exp_channel_data d
WHERE
t.channel_id = 18
AND t.entry_id = d.entry_id
AND t.status = 'open'
ORDER BY page_provincia ASC, title ASC";

		//echo $query;

		$return = '';

		// Perform SQL query
		$query = $this->EE->db->query($query);

		//Find number of entries
		if($query->num_rows() > 0){

			$result = $query->result_array();
			//var_dump($result);

			$consulados = array();
			$provincias = array();
			$entries = array();

			foreach($result as $entry){

				if(!empty($entry['page_provincia']) && !in_array($entry['page_provincia'], $provincias)){
					array_push($provincias, $entry['page_provincia']);
				}

				if(!empty($entry['page_main_title_' . $this->country_code]) && !in_array($entry['page_main_title_' . $this->country_code], $consulados)){
					array_push($consulados, $entry['page_main_title_' . $this->country_code]);
				}

				array_push($entries, $entry);

			}

		}

		switch($mode){
			case 'provincias':
				if($type == 'dropdown'){
					$return = self::dropdown($provincias, $this->extra);
				}elseif($type == 'list'){
					$return = self::item_list($provincias, $this->extra);
				}
			break;

			case 'consulados':
				if($type == 'dropdown'){
					$return = self::dropdown($consulados, $this->extra);
				}elseif($type == 'list'){
					$return = self::item_list($consulados, $this->extra);
				}
			break;

			case 'accordion':
				$return = self::accordion($entries);
			break;

		}

		$this->return_data = $return;

	}
	// END CONSTRUCT

	function accordion($data){
		$items = '';
		$search = array('{count}', '{class}', '{provincia}', '{consulado}', '{url_consulado}', '{consulado_datos}', '{url}', '{consulado_jurisdiccion}');
		$replace = array();
		$counter = 1;
		$div_open = '<div id="{provincia}-container" class="prov-panel">';
		$div_close = '</div>';
		$class = '';

		$template = '<div id="accordion-{count}" class="panel-group">

				 <div class="panel panel-default">
					<div class="panel-heading {class}">
						<h4 class="panel-title">
							<a class="collapsed" data-toggle="collapse" data-parent="#accordion-{count}" href="{url}" data-target="#{url_consulado}"><span class="fa fa-angle-double-right"></span> {consulado}</a>
						</h4>
					</div>
					<div id="{url_consulado}" class="panel-collapse collapse">
						<div class="panel-body rich">
							{consulado_datos}
						</div>

							{consulado_jurisdiccion}

					</div>
				</div>

			</div>';

		foreach($data as $entry){
			$cons = $entry['page_main_title_' . $this->country_code];
			$cons_data = $entry['page_body_' . $this->country_code];
			$cons_jur = ($entry['page_jurisdiction_' . $this->country_code] != '') ?
				'<div class="panel-body rich">' . $entry['page_jurisdiction_' . $this->country_code] . '</div>' : '';
			$cons_provincia = $entry['page_provincia'];
			//$url = $this->EE->config->item('site_url') . '/' . $this->country_code . '/red-consular/' . $this->make_url($cons) . '/';
			$url = $this->EE->config->item('site_url_front') . '/red-consular/' . $this->make_url($cons) . '/';

			if($last_provincia != $entry['page_provincia']){
				$items .= ($counter == 1 ? $div_open : $div_close . $div_open);
				$class = '';
				$items .= '<h3>' . $entry['page_provincia'] . '</h3>';
			}

			$replace = array($counter, $class, $this->make_url($cons_provincia), $cons, $this->make_url($cons), $cons_data, $url, $cons_jur);
			//var_dump($cons);
			$items .= $template;
			$items = str_replace($search, $replace, $items);

			$last_provincia = $entry['page_provincia'];

			$class = ($class == '') ? 'odd' : '';

			$items .= ($counter == count($data) ? $div_close : '');

			$counter++;
		}

		return $items;
	}

	function dropdown($options, $extra=''){
		$s = '<select ' . $extra . '>{options}</select>';
		$ops = '<option value="">' . $this->placeholder . '</option>';

		foreach($options as $o){
			$value = $this->make_url($o);
			$ops .= '<option value="' . $value . '" ' . ($this->selected == $value ? 'selected="selected"' : '') . ' >' . $o . '</option>';
		}

		return str_replace('{options}', $ops, $s);
	}

	function item_list($options, $extra='', $style='ul'){
		$l = '<' . $style . ' ' . $extra . '>{items}</' . $style . '>';
		$items = '';

		foreach($options as $o){
			$items .= '<li>' . $o . '</li>';
		}

		return str_replace('{items}', $items, $l);
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

		Arma dropdowns, ul y ol de consulados y provincias en donde hay consulados

		Parametros:

		mode: string. accordion, consulados, provincias
		type: string. list, dropdown
		placeholder: string. En los casos de dropdown, es el texto que se muestra en el select
		selected: string. En los casos de dropdown, es la opcion que deberia quedar como seleccionada por default
		separator: string. dash/underscore. Se usa para los valores que necesiten tener formato de url_title
		extra: string. Para los casos de dropdown, ul y ol, aca se pueden agregar los atributos html (usar comilla simple)
		lang: string. Codigo de lenguaje es/en/it... etc


		{exp:iv_red_consular mode="accordion"}
		{exp:iv_red_consular mode="accordion" separator="dash"}
		{exp:iv_red_consular mode="accordion" lang="es"}
		{exp:iv_red_consular mode="accordion" lang="{country_code}"}

		{exp:iv_red_consular mode="consulados" type="dropdown"}
		{exp:iv_red_consular mode="consulados" type="list" lang="en"}

		{exp:iv_red_consular mode="provincias" type="dropdown" separator="underscore"}
		{exp:iv_red_consular mode="provincias" type="list"}
		{exp:iv_red_consular mode="provincias" type="list" placeholder="Seleccionar provincia"}

		{exp:iv_red_consular mode="provincias" type="dropdown" selected="santa-fe"}
		{exp:iv_red_consular mode="provincias" type="list" extra="id='mi-id' class='mi-clase' "}


		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	// END USAGE

}
// END CLASS
?>
