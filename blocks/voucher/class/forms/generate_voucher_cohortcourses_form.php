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
class generate_voucher_cohortcourses_form extends moodleform
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

        // Collect cohort records
        $cohorts = voucher_Helper::get_cohorts_by_id($SESSION->voucher->cohorts);

        // Now we'll show the cohorts one by one
        foreach($cohorts as $cohort) {

            // cohort name
            $mform->addElement('static', 'cohort_name', get_string('label:cohort', BLOCK_VOUCHER), $cohort->name);
            
            // Collect courses connected to cohort
            $cohort_courses = voucher_Helper::get_courses_by_cohort($cohort->id);
            
            // if we have connected courses we'll display them
            if ($cohort_courses) {
                
                $mform->addElement('static', 'connected_courses', get_string('label:connected_courses', BLOCK_VOUCHER), '');
                
                foreach($cohort_courses as $course) {
                    $mform->addElement('static', 'connected_courses[' . $cohort->id . '][]', '', $course->fullname);
                }
                
            } else {
                $mform->addElement('static', 'connected_courses[' . $cohort->id . '][]', get_string('label:connected_courses', BLOCK_VOUCHER), get_string('label:no_courses_connected', BLOCK_VOUCHER));
            }
            
            // Collect not connected courses
            $sql_not_connected_courses = "
                SELECT * FROM {$CFG->prefix}course c
                WHERE c.id != 1
                AND c.id NOT IN (
                    SELECT courseid FROM {$CFG->prefix}enrol e
                    WHERE e.customint1 = {$cohort->id}
                    AND e.enrol = 'cohort'
                )";
            $not_connected_courses = $DB->get_records_sql($sql_not_connected_courses);
            
            // If we have not connected courses we'll display them
            if (count($not_connected_courses) > 0) {
                
                $arr_not_connected_courses = array();
                foreach($not_connected_courses as $not_connected_course) $arr_not_connected_courses[$not_connected_course->id] = $not_connected_course->fullname;
                
                
//                $select_cohorts = &$mform->addElement('select', 'voucher_cohorts', get_string('label:voucher_cohorts', BLOCK_VOUCHER), $arr_cohort_select);
                $select_connect_courses = &$mform->addElement('select', 'connect_courses[' . $cohort->id . ']', get_string('label:voucher_connect_course', BLOCK_VOUCHER), $arr_not_connected_courses);
                $select_connect_courses->setMultiple(true);
                    
            }
            
            // That's the end of the loop
        }
        
        // action buttons
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
