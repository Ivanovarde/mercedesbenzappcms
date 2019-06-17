<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'PG',
	'pi_version' => '1.2.0',
	'pi_author' => 'Caddis (TJ Draper)',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Fetch a single request value or loop through all GET/POST parameters.',
	'pi_usage' => Pg::usage()
);

class Pg {

	public $return_data = '';

	public function __construct()
	{
		// Fetch parameters
		$this->method = ee()->TMPL->fetch_param('method', 'get');
		$this->key = ee()->TMPL->fetch_param('key');
		$this->getArrayKey = ee()->TMPL->fetch_param('array_key');

		/**
		 * If the key param is false, then this is either the deprecated
		 * {exp:pg} tag or the {exp:pg:pair} tag because the key param is
		 * required for the {exp:pg:param} tag. The entire if statement
		 * should be removed in 2.0.0
		 */
		if ($this->key === false) {
			$this->return_data = $this->pair();
		} else {
			$this->return_data = '';
		}
	}

	public function pair()
	{
		/**
		 * Only run this function if it is called from the constructor. This
		 * is to allow for the deprecated use of the tag {exp:pg}. In version
		 * 2.0.0 this if statement should be removed and the {exp:pg} tag will
		 * no longer funtion. {exp:pg:pair} should be used.
		 */
		if ($this->return_data === '') {
			$data = ($this->method === 'get') ? $_GET : $_POST;

			$variables = array();

			// Loop through all parameters
			foreach ($data as $key => $value) {
				if (gettype($value) !== 'array') {
					$variables[] = array(
						'key' => ee()->security->xss_clean($key),
						'value' => ee()->security->xss_clean($value)
					);
				}
			}

			// Return parameters
			return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);
		}

		return $this->return_data;
	}

	public function param()
	{
		if ($this->key !== false) {
			$data = ee()->input->{$this->method}($this->key, true);

			/**
			 * Check if array param is set and get a value from array if it is
			 * Otherwise, return the data.
			 */
			if (gettype($data) === 'array' and ! empty($data[$this->getArrayKey])) {
				return $data[$this->getArrayKey];
			} else if (gettype($data) !== 'array') {
				return $data;
			}
		}

		return '';
	}

	public static function usage()
	{
		ob_start();
?>
See docs and examples on GitHub:
https://github.com/caddis/pg
<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}