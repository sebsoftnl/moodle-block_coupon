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
        global $CFG, $DB, $SESSION;

        $mform = & $this->_form;
        
        $mform->addElement('static', 'header', get_string('header:label_instructions', BLOCK_VOUCHER), get_string('header:instructions_txt', BLOCK_VOUCHER));
        
        // Display which course we selected
        $course = $DB->get_record('course', array('id'=>$SESSION->voucher->course));
        $mform->addElement('static', 'selected_course', get_string('label:selected_course', BLOCK_VOUCHER), $course->fullname);
        
        // Collect connected groups
        $groups = $DB->get_records('groups', array('courseid'=>$SESSION->voucher->course));
        
        if (count($groups) > 0) {
            $arr_groups_select = array();
            foreach($groups as $group) $arr_groups_select[$group->id] = $group->name;

            $select_groups = &$mform->addElement('select', 'voucher_groups', get_string('label:voucher_groups', BLOCK_VOUCHER), $arr_groups_select);
            $mform->addHelpButton('voucher_groups', 'label:voucher_groups', BLOCK_VOUCHER);
            $select_groups->setMultiple(true);
        
        // Shouldn't happen cause it'll just skip this step if no groups are connected
        } else {
            $select_groups = &$mform->addElement('static', 'voucher_groups', '', get_string('label:no_groups_selected', BLOCK_VOUCHER));
        }
        
//        $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
