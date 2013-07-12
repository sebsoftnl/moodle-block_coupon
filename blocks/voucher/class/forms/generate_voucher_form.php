<?php

/*
 * File: generate_voucher_form.php
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
class generate_voucher_form extends moodleform
{

    /**
     * form definition
     * @global stdClass $CFG
     * @global moodle_database $DB 
     */
    function definition()
    {
        global $CFG, $DB, $USER;

        $default_email = get_config('block/' . BLOCK_VOUCHER, 'default_email');
        $mform = & $this->_form;

        // Type of voucher
        $type_options = array();
        $type_options[] =& $mform->createElement('radio', 'type', '', get_string('label:type_course', BLOCK_VOUCHER), 0);
        $type_options[] =& $mform->createElement('radio', 'type', '', get_string('label:type_cohorts', BLOCK_VOUCHER), 1);
        $mform->addGroup($type_options, 'voucher_type', get_string('label:voucher_type', BLOCK_VOUCHER), array(' '));
        $mform->setDefault('yesno', 0);
        $mform->addRule('voucher_type', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_type', 'label:voucher_type', BLOCK_VOUCHER);
        
        // Amount of vouchers
        $mform->addElement('text', 'voucher_amount', get_string('label:voucher_amount', BLOCK_VOUCHER));
        $mform->setType('voucher_amount', PARAM_INT);
        $mform->addRule('voucher_amount', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addRule('voucher_amount', get_string('error:numeric_only', BLOCK_VOUCHER), 'numeric', null, 'client');
        $mform->addHelpButton('voucher_amount', 'label:voucher_amount', BLOCK_VOUCHER);

        // Email address to mail to
        $mform->addElement('text', 'voucher_email', get_string('label:voucher_email', BLOCK_VOUCHER));
        $mform->setType('voucher_email', PARAM_EMAIL);
        $mform->setDefault('voucher_email', $default_email);
        $mform->addRule('voucher_email', get_string('error:invalid_email', BLOCK_VOUCHER), 'email', null, 'client');
        $mform->addRule('voucher_email', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_email', 'label:voucher_email', BLOCK_VOUCHER);

        
//        // First we'll get some useful info
//        $courses = $DB->get_records('course');
//        $cohorts = $DB->get_records('cohort');
//        
//        // And create data for multiselect
//        $arr_course_select = array();
//        foreach($courses as $course) $arr_course_select[$course->id] = $course->fullname;
//        $arr_cohort_select = array();
//        foreach($cohorts as $cohort) $arr_cohort_select[$cohort->id] = $cohort->name;
//
//        // Course id
//        $select_course = &$mform->addElement('select', 'course_id', get_string('label:select-course', BLOCK_VOUCHER), $arr_course_select);
//        $select_course->setMultiple(false);
//
//        // If course id isn't set we'll check the cohort
//        $select_cohorts = &$mform->addElement('select', 'cohorts', get_string('label:select-cohorts', BLOCK_VOUCHER), $arr_cohort_select);
//        $select_cohorts->setMultiple(true);
        
//        $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
