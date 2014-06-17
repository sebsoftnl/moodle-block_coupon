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
        if (!$str_info = get_config('voucher', 'info_voucher_course')) $str_info = get_string('missing_config_info', BLOCK_VOUCHER);
        $mform->addElement('static', 'info', '', $str_info);

        $mform->addElement('header', 'header', get_string('heading:input_course', BLOCK_VOUCHER));
        
        // First we'll get some useful info
        $courses = voucher_Db::GetVisibleCourses();
        
        // And create data for multiselect
        $arr_courses_select = array();
        foreach($courses as $course) $arr_courses_select[$course->id] = $course->fullname;

        // Course id
        $select_course = &$mform->addElement('select', 'voucher_courses', get_string('label:voucher_courses', BLOCK_VOUCHER), $arr_courses_select);
        $select_course->setMultiple(true);
        $mform->addRule('voucher_courses', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_courses', 'label:voucher_courses', BLOCK_VOUCHER);

        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
