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
        
        $mform->addElement('header', 'header', get_string('heading:info', BLOCK_VOUCHER));
        $mform->addElement('static', 'info', '', get_string('info:voucher_confirm', BLOCK_VOUCHER));

        $mform->addElement('header', 'confirmheader', get_string('heading:general_settings', BLOCK_VOUCHER));

        // Set email_to variable
        $use_alternative_email = get_config('voucher', 'use_alternative_email');
        $alternative_email = get_config('voucher', 'alternative_email');
//        $max_vouchers_amount = get_config('voucher', 'max_vouchers');
        
        // Amount of vouchers
        $mform->addElement('text', 'voucher_amount', get_string('label:voucher_amount', BLOCK_VOUCHER));
        $mform->setType('voucher_amount', PARAM_INT);
        $mform->addRule('voucher_amount', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addRule('voucher_amount', get_string('error:numeric_only', BLOCK_VOUCHER), 'numeric', null, 'client');
        $mform->addHelpButton('voucher_amount', 'label:voucher_amount', BLOCK_VOUCHER);
        
        // Use alternative email address
        $mform->addElement('checkbox', 'use_alternative_email', get_string('label:use_alternative_email', BLOCK_VOUCHER));
        $mform->setType('use_alternative_email', PARAM_BOOL);
        $mform->setDefault('use_alternative_email', $use_alternative_email);
        
        // Email address to mail to
        $mform->addElement('text', 'alternative_email', get_string('label:alternative_email', BLOCK_VOUCHER));
        $mform->setType('alternative_email', PARAM_EMAIL);
        $mform->setDefault('alternative_email', $alternative_email);
        $mform->addRule('alternative_email', get_string('error:invalid_email', BLOCK_VOUCHER), 'email', null, 'client');
        $mform->addHelpButton('alternative_email', 'label:alternative_email', BLOCK_VOUCHER);
        $mform->disabledIf('alternative_email', 'use_alternative_email', 'notchecked');

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
    
    public function validation($data, $files) {
        
        $errors = parent::validation($data, $files);
        
        $max_vouchers_amount = get_config('voucher', 'max_vouchers');
        if ($data['voucher_amount'] > $max_vouchers_amount) {
            $errors['voucher_amount'] = get_string('error:voucher_amount_too_high', BLOCK_VOUCHER, array('max_vouchers'=>$max_vouchers_amount));
        }
        
        // If we want to use alternative email we'd best be sure its a proper email address
        if (isset($data['use_alternative_email']) && $data['use_alternative_email']) {
            
            if (empty($data['alternative_email'])) {
                
                $error['alternative_email'] = get_string('error:alternative_email_required', BLOCK_VOUCHER);
                
            } elseif (!filter_var($data['alternative_email'], FILTER_VALIDATE_EMAIL)) {
            
                $error['alternative_email'] = get_string('error:alternative_email_invalid', BLOCK_VOUCHER);
                
            }
        }
        
        return $errors;
    }
    
}


?>
