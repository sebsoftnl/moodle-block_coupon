<?php

/*
 * File: generate_voucher_groups_form.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 12-jul-2013
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
        foreach($SESSION->voucher->cohorts as $cohortid) {
            
            $cohort = $DB->get_record('cohort', array('id'=>$cohortid));
        
            $mform->addElement('static', 'cohort', '', $cohort->name);
            
            $sql_cohort_courses = "
                SELECT * FROM {$CFG->prefix}course c
                LEFT JOIN {$CFG->prefix}enrol e
                    ON e.courseid = c.id
                WHERE e.enrol = 'cohort'
                AND e.customint1 = $cohortid";
            $cohort_courses = $DB->get_records_sql($sql_cohort_courses);
            
            $arr_cohort_courses = array();
            foreach($cohort_courses as $cohort_course) $arr_cohort_courses[$cohort_course->id] = $cohort_course->fullname;
            
            
//            $type_options[] =& $mform->createElement('static', 'type', '', '');
//            $type_options[] =& $mform->createElement('radio', 'type', '', get_string('label:type_cohorts', BLOCK_VOUCHER), 1);
//            $mform->addGroup($type_options, 'voucher_type', get_string('label:voucher_type', BLOCK_VOUCHER), array(' '));

        }
        
        $arr_groups_select = array();
        foreach($groups as $group) $arr_groups_select[$group->id] = $group->name;
        
        $select_groups = &$mform->addElement('select', 'voucher_groups', get_string('label:voucher_cohorts', BLOCK_VOUCHER), $arr_groups_select);
        $select_groups->setMultiple(true);
        
//        $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
