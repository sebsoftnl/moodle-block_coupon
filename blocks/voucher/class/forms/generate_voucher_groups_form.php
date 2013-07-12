<?php

/*
 * File: generate_voucher_groups_form.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

require_once $CFG->libdir . '/formslib.php';

/**
 * Description of purchase_form
 *
 * @author Rogier
 */
class generate_voucher_groups_form extends moodleform
{

    /**
     * form definition
     * @global stdClass $CFG
     * @global moodle_database $DB 
     */
    function definition()
    {
        global $CFG, $DB, $USER;

        $mform = & $this->_form;

        // First we'll get some useful info
        $groups = $DB->get_records('groups', array('courseid'=>$SESSION->voucher->courseid));
        
        $arr_groups_select = array();
        foreach($groups as $group) $arr_groups_select[$group->id] = $group->name;
        
        $select_groups = &$mform->addElement('select', 'voucher_groups', get_string('label:voucher_cohorts', BLOCK_VOUCHER), $arr_groups_select);
        $select_groups->setMultiple(true);
        
//        $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
