<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * fieldtype
 *
 * @package     auto_field
 * @category    Modules
 * @author      Rein de Vries <info@reinos.nl>
 * @link        http://reinos.nl
 * @copyright   Copyright (c) 2014 Reinos.nl Internet Media
 */

include(PATH_THIRD.'auto_field/config.php');


class Auto_field_ft extends EE_Fieldtype
{

    public $info = array(
        'name'      => AUTO_FIELD_NAME,
        'version'   => AUTO_FIELD_VERSION,
        'has_global_settings' => 'n'
    );

    public $settings = array();

    public $default_settings = array();

    public $has_array_data = TRUE;

    private $prefix;

    // ----------------------------------------------------------------------

    /**
     * Constructor
     *
     * @access public
     *
     * Calls the parent constructor
     */
    public function __construct()
    {
        if (version_compare(APP_VER, '2.1.4', '>')) { parent::__construct(); } else { parent::EE_Fieldtype(); }

        $this->prefix = AUTO_FIELD_MAP.'_';

        //load lang file
        ee()->lang->loadfile(AUTO_FIELD_MAP);

        //require settings
        require 'settings.php';
    }

    // ---------------------------------------------------------------------- 
    // After saving the content to the database
    // ---------------------------------------------------------------------- 
    /**
     * save (Native EE)
     *
     * @access public
    */
    public function post_save($data)
    {
        return $this->_post_save($data);
    }

     /**
     * Save after the saving of the entry
     *
     * @access public
    */
    public function _post_save($data)
    {
        //load the channel data
        ee()->load->driver('channel_data');

        if(isset($this->settings['entry_id']) && $this->settings['entry_id'] != '')
        {
            $entry = ee()->channel_data->get_entry($this->settings['entry_id']);

            //init TMPL
            if (!class_exists('EE_Template')) 
            {
                require_once APPPATH.'libraries/Template.php';
            }
            $OLD_TMPL = isset(ee()->TMPL) ? ee()->TMPL : NULL;
            ee()->TMPL = new EE_Template();

            //set the entry data
            $entry_data = $entry->row_array();

            //get channel
            $channel = ee()->channel_data->get_channel($entry_data['channel_id'], array('channel_name, channel_title'));
            $channel_data = $channel->row_array();

            //merge with channel data
            $variables = array(array_merge($entry_data, $channel_data));
            $new_data = ee()->TMPL->parse_variables($this->settings['auto_field_pattern'], $variables);

            //reset TMPL
            ee()->TMPL = $OLD_TMPL;

            //update new data
            ee()->db->where('entry_id', $this->settings['entry_id']);
            ee()->db->update('channel_data', array(
                'field_id_'.$this->settings['field_id'] => $new_data
            ));
        }
   }

    // ---------------------------------------------------------------------- 
    // Before saving the content to the database
    // ---------------------------------------------------------------------- 
    
    /**
     * save (Native EE)
     *
     * @access public
    */
    public function save($data)
    {
        return $this->_save($data);
    }

    // ---------------------------------------------------------------------- 

    /**
     * save (Low Variables)
     *
     * @access public
    */
    // public function save_var_field($data)
    // {
    //     return $this->_save($data);
    // }

    // ---------------------------------------------------------------------- 

    /**
     * save (Matrix)
     *
     * @access public
    */
    // public function save_cell($data)
    // {
    //     return $this->_save($data);
    // }

    // ---------------------------------------------------------------------- 

    /**
     * save (Content Elements)
     *
     * @access public
    */
    // public function save_element($data)
    // {
    //     return $this->_save($data);
    // }

    // ---------------------------------------------------------------------- 

    /**
     * save
     *
     * @access public
    */
    private function _save($data = '')
    {        
        if(is_array($data))
        {
            $data = implode('|', $data);
        }
        
        return $data;
    }

    // ----------------------------------------------------------------------
    // Display the field for all types
    // ----------------------------------------------------------------------

    /**
     * display_field
     *
     * @access public
    */
    function display_field($data)
    {
        //generate the data
        return $this->_display_field($data);
    }

    // ----------------------------------------------------------------------

    /**
     * display_var_field (Low variables)
     *
     * @access public
    */
    // function display_var_field ($data)
    // {
    //     //generate the data
    //     return $this->_display_field($data, 'low_variables');
    // }

    // ----------------------------------------------------------------------
    
    /**
     * display_cell (MATRIX)
     *
     * @access public
    */
    // function display_cell( $data )
    // {
    //    return $this->_display_field($data, 'matrix');
    // }

    // ----------------------------------------------------------------------
    
    /**
     * display_element (Content Elements)
     *
     * http://www.krea.com/docs/content-elements/element-development/ee2-functions-reference
     *
     * @access public
    */
    // function display_element($data)
    // {       
    //     return $this->_display_field($data, 'content_elements');
    // }
    
    // ----------------------------------------------------------------------

    /**
     * display_field
     *
     * @access public
    */
    private function _display_field($data, $type = 'default')
    {
        //save data
        $data = trim($data);

        //fieldname
        switch($type)
        {
            case 'matrix': $field_name = $this->cell_name ;
                break;
            case 'default':
            default : $field_name = $this->field_name;
                break;
        }
        
        //return data to display
        return form_input($this->field_name, $data, 'id="'.$this->field_name.'"').'
            <style>#'.str_replace('field_id', 'hold_field', $this->field_name).' {display:none;}</style>
            <script>$(function(){
                $("#publishForm").submit(function(){
                    $("input[name=\''.$this->field_name.'\']").val($(this).find("input[name=\'title\']").val());
                });
            });</script>
        ';
    }

    // ----------------------------------------------------------------------

    /**
     * Validates the field input (EE)
     *
     * @access public
    */
    /*public function validate($data)
    {
        $data = str_replace($this->settings['currency_decimal_value'], '.', $data);

        return $this->_validate($data);
    }*/


    // ----------------------------------------------------------------------

    /**
     * Validates the field input
     *
     * @access public
    */
    /*public function _validate($data)
    {
        if(is_null($data) || empty($data) || is_int($data) || is_numeric($data) || (is_numeric( $data ) && floor( $data ) != $data))
        {
            return true;
        }

        return 'Not a currency';
    }*/
	
    // ----------------------------------------------------------------------
    // Replace the tags for all types
    // ----------------------------------------------------------------------

    /**
     * display_var_tag (Low variables)
     *
     * @access public
    */
    // public function display_var_tag($var_data, $tagparams, $tagdata)
    // {
    //     return $this->replace_tag($var_data, $tagparams, $tagdata);
    // }

    // ----------------------------------------------------------------------

    /**
     * replace_element_tag (Content Elements)
     *
     * @access public
    */
    // public function replace_element_tag($data, $params = array(), $tagdata)
    // {
    //     return $this->replace_tag($data, $params, $tagdata);
    // }

    // ----------------------------------------------------------------------

    /**
     * replace_tag
     *
     * @access public
    */
    public function replace_tag($data, $params = array(), $tagdata = FALSE)
    {
        $data = trim($data);
        if ($data == FALSE) return;

        //set the decimal value
        //$data = str_replace('.', $this->settings['currency_decimal_value'], $data);

        return $data;
    }

    // ----------------------------------------------------------------------
    
    /**
     * replace_tag_catchall
     *
     * @access public
    */
    /*function replace_tag_catchall($file_info, $params = array(), $tagdata = FALSE, $modifier)
    {
    
    }*/

    // ----------------------------------------------------------------------
    // Display the settings for all types
    // ----------------------------------------------------------------------
    
    /**
     * Display settings screen (Default EE)
     *
     * @access  public
     */
    function display_settings($data)
    {
        foreach($this->_display_settings($data) as $val)
        {
             ee()->table->add_row($val);
        }
    }

    // --------------------------------------------------------------------
    
    /**
     * Display settings screen (Matrix)
     *
     * @access  public
     */
    function display_cell_settings($data)
    {
        return $this->_display_settings($data, array('matrix_input' => 'class="matrix-textarea"'));
    }

    // --------------------------------------------------------------------
    
    /**
     * Display settings screen (Low variables)
     *
     * @access  public
     */
    // function display_var_settings($data)
    // {
    //     return $this->_display_settings($data);
    // }

    // --------------------------------------------------------------------
    
    /**
     * Display settings screen (EE 2.7 GRID)
     *
     * @access  public
     */
    // function grid_display_settings($data)
    // {
    //     return $this->_display_settings($data, array(), 'grid');
    // }

    // --------------------------------------------------------------------
    
    /**
     * Display settings screen (Content Elements)
     *
     * @access  public
     */
    // function display_element_settings($data)
    // {   
    //     return $this->_display_settings($data);
    // }

    // --------------------------------------------------------------------

     /**
     * Display settings screen
     *
     * @access  public
     */
    private function _display_settings($data, $options = array(), $type = '')
    {
        $matrix_input = isset($options['matrix_input']) ? $options['matrix_input'] : '';

        $return = array();

        if(!empty($this->fieldtype_settings))
        {
            foreach($this->fieldtype_settings as $field=>$options)
            {
                //global?
                if($options['global'])
                    continue;

                switch($options['type'])
                {
                    //multiselect
                    case 'm' : 
                        //grid
                        if($type == 'grid')
                        {
                            $return[] = $this->grid_dropdown_row(
                                $options['label'],
                                $this->prefix.$options['name'].'[]',
                                $options['options'],
                                isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'],
                                true
                            );
                        }

                        //normal
                        else
                        {
                            $return[] = array(
                                $options['label'],
                                form_multiselect($this->prefix.$options['name'], $options['options'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ))
                            );
                        }
                    break;

                    //select field
                    case 's' : 
                        //grid
                        if($type == 'grid')
                        {
                            $return[] = $this->grid_dropdown_row(
                                $options['label'],
                                $this->prefix.$options['name'],
                                $options['options'],
                                isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value']
                            );
                        }

                        //normal
                        else
                        {
                            $return[] = array(
                                $options['label'],
                                form_dropdown($this->prefix.$options['name'], $options['options'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ))
                            );
                        }
                        
                    break;

                    //text field
                    default:
                    case 't' : 
                        //grid
                        if($type == 'grid')
                        {
                           $return[] =  form_label($options['label']).NBS.NBS.NBS.
                            form_input(array(
                                'name'  => $this->prefix.$options['name'],
                                'value' => (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ),
                                'class' => ''
                            )).NBS.NBS.NBS;  
                        }

                        //normal
                        else
                        {
                            $return[] = array(
                                $options['label'],
                                form_input($this->prefix.$options['name'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ), $matrix_input)
                            );
                        }
                    break;
                }
            }
        }

        return $return;
    }

    // ----------------------------------------------------------------------
    // Save the settings for all types
    // ----------------------------------------------------------------------
    
    /**
     * save_settings (Default EE)
     *
     * @access public
    */
    function save_settings($data)
    {  
        return $this->_save_settings($data);
    } 

    // --------------------------------------------------------------------
    
    /**
     * save_settings (matrix)
     *
     * @access public
    */
    // function save_cell_settings($data)
    // {  
    //     return $this->_save_settings($data, true);
    // }  

    // --------------------------------------------------------------------
    
    /**
     * save_settings ((Low variables))
     *
     * @access public
    */
    // function save_var_settings($data)
    // {  
    //     return $this->_save_settings($data);
    // }  

    // --------------------------------------------------------------------
    
    /**
     * save_settings (EE 2.7 GRID)
     *
     * @access public
    */
    // function grid_save_settings($data)
    // { 
    //     return $this->_save_settings($data, true);
    // }  

    // --------------------------------------------------------------------

    /**
     * save_settings
     *
     * @access public
    */
    private function _save_settings($data, $use_data = false)
    { 
        $return = array();

        if(!empty($this->fieldtype_settings))
        {
            foreach($this->fieldtype_settings as $field=>$options)
            {
                //global?
                if($options['global'])
                    continue;

                $return[$this->prefix.$options['name']] =  $use_data ? $data[$this->prefix.$options['name']] : ee()->input->post($this->prefix.$options['name']);
            }
        }

        return $return;
    }

    // --------------------------------------------------------------------

    /**
     * Do additional processing after the field is created/modified. (EE)
     *
     * @access public
    */
    public function post_save_settings($data)
    {
        $this->_post_save_settings($data);
    }   

    // --------------------------------------------------------------------

    /**
     * Do additional processing after the field is created/modified.
     *
     * @access public
    */
    public function _post_save_settings($data)
    {
        //check if the field is converted to an int field        
        /*$fields = ee()->db->field_data('channel_data');

        foreach ($fields as $field)
        {
    
            if($field->name == 'field_id_'.$data['field_id'] && $field->type != 'decimal')
            {
                //change the field tupe
                ee()->db->query('ALTER TABLE  `exp_channel_data` CHANGE  `field_id_'.$data['field_id'].'`  `field_id_'.$data['field_id'].'` decimal(65,'.$this->settings['currency_decimal'].') NULL DEFAULT NULL');
                
                //update existing items to be null where they are zero
                ee()->db->where('field_id_'.$data['field_id'], '0,'.$this->settings['currency_decimal'].'');
                ee()->db->update('channel_data', array(
                  'field_id_'.$data['field_id'] => null  
                ));
            } 
        }*/

    } 

    // ----------------------------------------------------------------------
    
    /**
     * install
     *
     * @access public
    */
    function install()
    {
        $return = array();

        if(!empty($this->fieldtype_settings))
        {
            foreach($this->fieldtype_settings as $field=>$options)
            {
                $return[$this->prefix.$options['name']] = $options['def_value'];
            }
        }

        return $return;
    }

    // ----------------------------------------------------------------------

    /**
     * Update
     */
    function update()
    {
        return TRUE;
    }

    // ----------------------------------------------------------------------

    /**
     * display_global_settings
     *
     * @access public
    */
    function display_global_settings()
    {
        $data = array_merge($this->settings, $_POST);

        $form = '';
        foreach($this->fieldtype_settings as $field=>$options)
        {
            //global?
            if(!$options['global'])
                continue;

            switch($options['type'])
            {
                //multiselect
                case 'm' : 
                    $form .= form_label($options['label'], $options['label']).NBS.form_multiselect($this->prefix.$options['name'], $options['options'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] )).NBS.NBS.NBS.' ';
                break;

                //select field
                case 's' : 
                    $form .= form_label($options['label'], $options['label']).NBS.form_dropdown($this->prefix.$options['name'], $options['options'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] )).NBS.NBS.NBS.' ';
                break;

                //text field
                default:
                case 't' : 
                    $form .= form_label($options['label'], $options['label']).NBS.form_input($this->prefix.$options['name'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] )).NBS.NBS.NBS.' ';
                break;
            }
        }

        return $form;
    }

    // ----------------------------------------------------------------------

    /**
     * save_global_settings
     *
     * @access public
    */
    function save_global_settings()
    {
        return array_merge($this->settings, $_POST);
    }

    // ---------------------------------------------------------------------- 

    /**
     * accepts_content_type (GRID EE 2.7)
     *
     * @access public
    */
    public function accepts_content_type($name)
    {
        return ($name == 'channel' || $name == 'grid');
        //return true;
    }

    // --------------------------------------------------------------------

   

    // ---------------------------------------------------------------------- 
    // Custom 
    // ---------------------------------------------------------------------- 
       
    /**
     * _set_map_data
     *
     * @access private
    */
    /*private function _set_map_data($data)
    {
        $data = json_decode(base64_decode($data));

        $new_data = array(
            'map' => array(),
            'markers' => array(
                'latlng' => '',
                'icon' => '',
                'title' => ''
            )
        );

        foreach($data as $key=>$val)
        {
            //map data
            if(isset($val->map))
            {
                foreach($val->map as $k=>$v)
                {
                    if(is_array($v))
                    {
                        $new_data['map'][$k] = implode('|', $v);
                    }
                    else
                    {
                        $new_data['map'][$k] = $v;
                    }    
                }
            } 

            if(isset($val->markers))
            {
                foreach($val->markers as $k=>$v)
                {
                    $new_data['markers']['latlng'][] = $v->lat.','.$v->lng;
                    $new_data['markers']['title'][] = $v->title;
                    $new_data['markers']['icon'][] = isset($v->icon) ? $v->icon : null;
                }  

                $new_data['markers']['latlng'] = is_array($new_data['markers']['latlng']) ? implode('|', $new_data['markers']['latlng']) : '';
                $new_data['markers']['title'] = is_array($new_data['markers']['title']) ? implode('|', $new_data['markers']['title']) : '';
                $new_data['markers']['icon'] = is_array($new_data['markers']['icon']) ? implode('|', $new_data['markers']['icon']) : '';
            }
        }

        return $new_data;
    }*/

    // ----------------------------------------------------------------------
    
    /**
     * _set_params 
     *
     * @access private
    */
    /*private function _set_params($params)
    {
        $new_params = '';
        $method = 'geocoding'; //default
        $allowed_methods = array('geocoding', 'polygon', 'polyline'); //allowed

        if(!empty($params))
        {
            foreach($params as $key=>$val)
            {
                if($key == 'method')
                {
                    if(in_array($val, $allowed_methods))
                    {
                        $method = $val;
                    }
                }
                else
                {
                    $new_params .= $key.'="'.$val.'"';
                }
            }
        }

        return array('method' => $method, 'params' => $new_params);
    }*/

    // ----------------------------------------------------------------------

}

/* End of file ft.auto_field.php */
/* Location: ./system/expressionengine/third_party/auto_field/ft.auto_field.php */
