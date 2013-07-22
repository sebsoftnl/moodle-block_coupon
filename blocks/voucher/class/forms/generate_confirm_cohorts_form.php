<?php

/*
 * File: generate_confirm_cohorts_form.php
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
class generate_confirm_cohorts_form extends moodleform
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

        // Set email_to variable
        if (get_config('voucher', 'use_supportuser')) {
            $supportuser = generate_email_supportuser();
            $email_to = $supportuser->email;
        } else {
            $email_to = '';
        }
        
        // Header
        $mform->addElement('header', 'confirmheader', get_string('heading:general_settings', BLOCK_VOUCHER));
        
        // Amount of vouchers
        $mform->addElement('text', 'voucher_amount', get_string('label:voucher_amount', BLOCK_VOUCHER));
        $mform->setType('voucher_amount', PARAM_INT);
        $mform->addRule('voucher_amount', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addRule('voucher_amount', get_string('error:numeric_only', BLOCK_VOUCHER), 'numeric', null, 'client');
        $mform->addHelpButton('voucher_amount', 'label:voucher_amount', BLOCK_VOUCHER);
        
        // Email address to mail to
        $mform->addElement('text', 'voucher_email', get_string('label:voucher_email', BLOCK_VOUCHER));
        $mform->setType('voucher_email', PARAM_EMAIL);
        $mform->setDefault('voucher_email', $email_to);
        $mform->addRule('voucher_email', get_string('error:invalid_email', BLOCK_VOUCHER), 'email', null, 'client');
        $mform->addRule('voucher_email', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_email', 'label:voucher_email', BLOCK_VOUCHER);

        // Generate PDF
        $mform->addElement('checkbox', 'generate_pdf', get_string('label:generate_pdfs', BLOCK_VOUCHER));
        $mform->addHelpButton('generate_pdf', 'label:generate_pdfs', BLOCK_VOUCHER);

        // Collect cohort records
        $cohorts = voucher_Helper::get_cohorts_by_ids($SESSION->voucher->cohorts);

        // Cohorts to add
        foreach($cohorts as $cohort) {

            $mform->addElement('header', 'cohortsheader[]', $cohort->name);
//            $mform->addElement('static', 'cohort_name', get_string('label:selected_cohort', BLOCK_VOUCHER), $cohort->name);

            // Fetch the courses that are connected to this cohort
            if ($cohort_courses = voucher_Helper::get_courses_by_cohort($cohort->id)) {
                
                $mform->addElement('static', 'connected_courses', get_string('label:connected_courses', BLOCK_VOUCHER), '');
                
                // And display which courses
                foreach($cohort_courses as $course) {
                    $mform->addElement('static', 'connected_courses[' . $cohort->id . '][]', '', $course->fullname);
                }
                
            } else {
                $mform->addElement('static', 'connected_courses[' . $cohort->id . ']', get_string('label:connected_courses', BLOCK_VOUCHER), get_string('label:no_courses_connected', BLOCK_VOUCHER));
            }
            
        }
        
        // Submit button
        $this->add_action_buttons(true, get_string('button:save', BLOCK_VOUCHER));
        
    }
    
}


?>
