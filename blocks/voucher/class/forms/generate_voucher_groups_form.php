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
        
        $mform->addElement('header', 'header', get_string('heading:info', BLOCK_VOUCHER));
        if (!$str_info = get_config('voucher', 'info_voucher_course_groups')) $str_info = get_string('missing_config_info', BLOCK_VOUCHER);
        $mform->addElement('static', 'info', '', $str_info);

        $mform->addElement('header', 'groupsheader', get_string('heading:input_groups', BLOCK_VOUCHER));
        
        // Display which course we selected
        $groupOptions = array();
        foreach($SESSION->voucher->courses as $courseid) {
            
            // collect data
            if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
                print_error('error:course-not-found', BLOCK_VOUCHER);
            }
            $groups = $DB->get_records("groups", array('courseid'=>$courseid));
            if (empty($groups)) {
                continue;
            }
            
            // build up groups
            if (!isset($groupOptions[$course->fullname])) {
                $groupOptions[$course->fullname] = array();
            }
            foreach($groups as $group) {
                $groupOptions[$course->fullname][$group->id] = $group->name;
            }
            
        }
        
        if (!empty($groupOptions)) {
            
            $groupsElement = &$mform->addElement('selectgroups', 'voucher_groups', get_string('label:voucher_groups', BLOCK_VOUCHER), $groupOptions);
            $mform->addHelpButton('voucher_groups', 'label:voucher_groups', BLOCK_VOUCHER);
            $groupsElement->setMultiple(true);
        
        // Shouldn't happen cause it'll just skip this step if no groups are connected
        } else {
            $groupsElement = &$mform->addElement('static', 'voucher_groups', '', get_string('label:no_groups_selected', BLOCK_VOUCHER));
        }
        
//        $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>