<?php

/*
 * File: generate_voucher_form.php
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
class generate_voucher_course_form extends moodleform
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
        
        $mform->addElement('header', 'header', get_string('heading:info', BLOCK_VOUCHER));
        $mform->addElement('static', 'info', '', get_string('info:voucher_course', BLOCK_VOUCHER));

        $mform->addElement('header', 'header', get_string('heading:input_course', BLOCK_VOUCHER));
        
        // First we'll get some useful info
        $courses = voucher_Db::GetVisibleCourses();
        
        // And create data for multiselect
        $arr_courses_select = array();
        foreach($courses as $course) $arr_courses_select[$course->id] = $course->fullname;

        // Course id
        $select_course = &$mform->addElement('select', 'voucher_course', get_string('label:voucher_course', BLOCK_VOUCHER), $arr_courses_select);
        $select_course->setMultiple(false);
        $mform->addRule('voucher_course', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_course', 'label:voucher_course', BLOCK_VOUCHER);

        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
