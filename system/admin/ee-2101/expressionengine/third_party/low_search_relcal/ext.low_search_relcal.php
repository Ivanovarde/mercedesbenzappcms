<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Search Relationships and Calendars (201500901)
 *
 * Customized Low Search Relationships to include Calendar for Event
 * Will not return either value unless a Calendar has been set for Event
 *
 * @package   Low Search Relationships
 * @author    Scott-David Jones <sdjones1985@gmail.com>
 * @copyright Copyright (c) 2014 AutumnDev 
 */
class Low_search_relcal_ext {

    var $name           = "Low Search Relationships & Calendars";
    var $version        = 1.0;
    var $description    = "Added functionality to Low Search in order to search relationship titles and calendar titles by keyword";
    var $docs_url       = '';
    var $settings_exist = 'n';
    var $globalVars;
    // --------------------------------------------------------------------

    /** Activate Extension
     */
    function activate_extension()
    {
        // -------------------------------------------
        //  Add the row to exp_extensions
        // -------------------------------------------

        ee()->db->insert('extensions', array(
            'class'    => __CLASS__,
            'method'   => 'search_relationships',
            'hook'     => 'low_search_update_index',
            'settings' => '',
            'priority' => 10,
            'version'  => $this->version,
            'enabled'  => 'y'
        ));
    }

    /**
     * Update Extension
     */
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return false;
        }
       
        ee()->db->where('class', __CLASS__);
        ee()->db->update(
            'extensions',
            array('version' => $this->version)
        );
    }

    /**
     * Disable Extension
     */
    function disable_extension()
    {
        // -------------------------------------------
        //  Remove the row from exp_extensions
        // -------------------------------------------

        ee()->db->where('class', __CLASS__)
            ->delete('extensions');
    }

    /**
     * seach parent child relationships for keywords
     *
     * searches entry id for relationships and adds the 
     * relationship title to the index_text ready for low search
     * to search
     *  
     * @param  array $data  low search index data
     * @param  array $entry entry data
     * 
     * @return array modified data
     */
    function search_relationships($data, $entry){

        $site_id = ee()->config->item('site_id');
        
        //get partner based on collection channel
        ee()->db->select('c4.title AS partner_title')
            ->from('channel_titles c3')
            ->join('calendar_events ce', 'ce.entry_id = c3.entry_id')
            ->join('channel_titles c4', 'c4.entry_id = ce.calendar_id')
            ->where('c3.site_id', $site_id)
            ->where('c3.entry_id', $data['entry_id']);
          
        //get results
        $results1 = ee()->db->get();
        
        //return if no results
        if ($results1->num_rows == 0)
        {
            return $data;
        }
        
        //for each result add tot he index_text param
        foreach ($results1->result_array() as $key => $value1) 
        {
            $data['index_text'] .= ' '.low_clean_string($value1['partner_title']).' |';
            
        }

        //get channel based on collection channel
        ee()->db->select('c2.title AS venue_title')
            ->from('channel_titles c1')
            ->join('relationships r', 'r.parent_id = c1.entry_id')
            ->join('channel_titles c2', 'c2.entry_id = r.child_id')
            ->where('c1.site_id', $site_id)
            ->where('c1.entry_id', $data['entry_id']);

        //get results
        $results2 = ee()->db->get();
        
        //return if no results
        if ($results2->num_rows == 0)
        {
            return $data;
        }
        
        //for each result add tot he index_text param
        foreach ($results2->result_array() as $key => $value2) 
        {
            $data['index_text'] .= ' '.low_clean_string($value2['venue_title']).' |';
            
        }
        
        return $data;
    }

}