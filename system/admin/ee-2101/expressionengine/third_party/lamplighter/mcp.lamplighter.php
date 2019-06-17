<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Lamplighter Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Masuga Design
 * @link
 */
class Lamplighter_mcp
{

	public $return_data;

	private $_base_url;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lamplighter';
		$this->_base_uri = '?D=cp&C=addons_modules&M=show_module_cp&module=lamplighter';
		$this->EE->load->add_package_path( PATH_THIRD.'lamplighter/' );
		$this->EE->load->library('lamplighter_library');

		if ( !empty($this->EE->lamplighter_library->api_key) ) {
			$this->EE->cp->set_right_nav(array(
				'module_home'	=> $this->_base_url,
				'send_data' => $this->_base_url.'&method=refresh&api=addons',
			));
		} else {
			$this->EE->cp->set_right_nav(array(
				'module_home'	=> $this->_base_url,
			));

		}
	}

	// ----------------------------------------------------------------

	/**
	 * The Module's control panel homepage.
	 * @return string
	 */
	public function index()
	{
		$this->EE->view->cp_page_title = lang('lamplighter_module_name');
		$view_data = array(
			'api_key' => $this->EE->lamplighter_library->getSiteToken(),
			'base_url' => $this->_base_url,
			'curl_enabled' => $this->is_curl_enabled(),
			'cp' => $this->EE->cp
		);

		return $this->EE->load->view('mcp_index', $view_data, TRUE);
	}


	/**
	 * This method sends a request to a specified LL endpoint.
	 */
	public function refresh()
	{
		$api_endpoint = $this->EE->input->get('api', true);
		$response = $this->EE->lamplighter_library->api_request($api_endpoint);
		if ( $response['status'] == 'success' ) {
			$this->EE->session->set_flashdata('message_success', $response['message']);
		} else {
			$this->EE->lamplighter_library->purge_token_data();
			$this->EE->session->set_flashdata('message_failure', $response['message']);
		}
		return $this->EE->functions->redirect($this->_base_url);
	}


	/**
	 * This method stores the site token data in the DB and registers the
	 * action ID with the Lamplighter app.
	 */
	public function save_key()
	{
		$site_token = $this->EE->input->post('api_key');
		$response = $this->EE->lamplighter_library->store_token_data($site_token);
		if ( $response['status'] == 'success' ) {
			$this->EE->session->set_flashdata('message_success', $response['message']);
		} else {
			$this->EE->lamplighter_library->purge_token_data();
			$this->EE->session->set_flashdata('message_failure', $response['message']);
		}
		return $this->EE->functions->redirect($this->_base_url);
	}


	/**
	 * This method removes the site token data from the DB and unregisters the
	 * action ID with the Lamplighter app.
	 */
	public function remove_key()
	{
		$response = $this->EE->lamplighter_library->unregister_action_id();
		if ( isset($response->status) && $response->status == 'success' ) {
			$this->EE->session->set_flashdata('message_success', $response->message);
		} else {
			$this->EE->session->set_flashdata('message_failure', $response->message);
		}
		return $this->EE->functions->redirect($this->_base_url);
	}

	/**
	 * This method determines if curl is enabled on the server.
	 * @return boolean
	 */
	public function is_curl_enabled()
	{
    	return function_exists('curl_version');
	}

}
