<?php

/*
 * File: generate_confirm_course_form.php
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
class generate_confirm_course_form extends moodleform
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
        
        $mform->addElement('header', 'confirmheader', get_string('heading:general_settings', BLOCK_VOUCHER));

        // Set email_to variable
        if (get_config('voucher', 'use_supportuser')) {
            $supportuser = generate_email_supportuser();
            $email_to = $supportuser->email;
        } else {
            $email_to = '';
        }

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

        // Generate_pdf checkbox
        $mform->addElement('checkbox', 'generate_pdf', get_string('label:generate_pdfs', BLOCK_VOUCHER));
        $mform->addHelpButton('generate_pdf', 'label:generate_pdfs', BLOCK_VOUCHER);
        
        // Course fullname
        $course = $DB->get_record('course', array('id'=>$SESSION->voucher->course));
        $mform->addElement('static', 'voucher_course', get_string('label:selected_course', BLOCK_VOUCHER), $course->fullname);

        // Selected groups
        if (isset($SESSION->voucher->groups)) {
            $mform->addElement('static', 'voucher_groups', get_string('label:selected_groups', BLOCK_VOUCHER), '');

            $groups = voucher_Helper::get_groups_by_ids($SESSION->voucher->groups);
            
            foreach($groups as $group) {
                $mform->addElement('static', 'voucher_groups', '', $group->name);
            }
        }
        
        // Submit button
        $this->add_action_buttons(true, get_string('button:save', BLOCK_VOUCHER));
        
    }
    
}


?>
