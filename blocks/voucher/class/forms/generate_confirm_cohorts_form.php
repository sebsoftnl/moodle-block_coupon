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

        $mform->addElement('header', 'header', get_string('heading:info', BLOCK_VOUCHER));
        if (!$str_info = get_config('voucher', 'info_voucher_confirm')) $str_info = get_string('missing_config_info', BLOCK_VOUCHER);
        $mform->addElement('static', 'info', '', $str_info);

        // Set email_to variable
        $use_alternative_email = get_config('voucher', 'use_alternative_email');
        $alternative_email = get_config('voucher', 'alternative_email');
//        $max_vouchers_amount = get_config('voucher', 'max_vouchers');
        
        // Header
        $mform->addElement('header', 'confirmheader', get_string('heading:general_settings', BLOCK_VOUCHER));
        
        // Amount of vouchers
        $mform->addElement('text', 'voucher_amount', get_string('label:voucher_amount', BLOCK_VOUCHER));
        $mform->setType('voucher_amount', PARAM_INT);
        $mform->addRule('voucher_amount', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addRule('voucher_amount', get_string('error:numeric_only', BLOCK_VOUCHER), 'numeric', null, 'client');
        $mform->addHelpButton('voucher_amount', 'label:voucher_amount', BLOCK_VOUCHER);

        // Upload users csv
        $mform->addElement('filepicker', 'voucher_recipients', get_string('label:voucher_recipients', BLOCK_VOUCHER), null, array('accepted_types' => 'csv'));
        $mform->addHelpButton('voucher_recipients', 'label:voucher_recipients', BLOCK_VOUCHER);
        // Download sample of csv
        $url = '<a href="' . $CFG->wwwroot . '/blocks/voucher/sample.csv" target="_blank">' . get_string('download-sample-csv', BLOCK_VOUCHER) . '</a>';
        $mform->addElement('static', 'sample_csv', '', $url);
        
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

        // Generate PDF
        $mform->addElement('checkbox', 'generate_pdf', get_string('label:generate_pdfs', BLOCK_VOUCHER));
        $mform->addHelpButton('generate_pdf', 'label:generate_pdfs', BLOCK_VOUCHER);

        // Editable email message
        $mform->addElement('editor', 'email_body', get_string('label:email_body', BLOCK_VOUCHER));
        $mform->setType('email_body', PARAM_RAW);
        $mform->setDefault('email_body', array('text'=>get_string('voucher_mail_content', BLOCK_VOUCHER)));
        $mform->addRule('email_body', get_string('required'), 'required');
        $mform->addHelpButton('email_body', 'label:email_body', BLOCK_VOUCHER);
        
        // Configurable enrolment time
        $mform->addElement('text', 'enrolment_period', get_string('label:enrolment_period', BLOCK_VOUCHER));
        $mform->addRule('enrolment_period', get_string('required'), 'required');
        $mform->setType('enrolment_period', PARAM_INT);
        $mform->setDefault('enrolment_period', '0');
        $mform->addHelpButton('enrolment_period', 'label:enrolment_period', BLOCK_VOUCHER);

        // Configurable enrolment time
        $mform->addElement('date_selector', 'date_send_vouchers', get_string('label:date_send_vouchers', BLOCK_VOUCHER));
        $mform->addRule('date_send_vouchers', get_string('required'), 'required');
//        $mform->setType('date_send_vouchers', PARAM_RAW);
//        $mform->setDefault('date_send_vouchers', date('d-m-Y'));
        $mform->addHelpButton('date_send_vouchers', 'label:date_send_vouchers', BLOCK_VOUCHER);
        
        // Collect cohort records
        $cohorts = voucher_Helper::get_cohorts_by_ids($SESSION->voucher->cohorts);

        // Cohorts to add
        foreach($cohorts as $cohort) {

            $mform->addElement('header', 'cohortsheader[]', $cohort->name);
//            $mform->addElement('static', 'cohort_name', get_string('label:selected_cohort', BLOCK_VOUCHER), $cohort->name);

            // Fetch the courses that are connected to this cohort
            if ($cohort_courses = voucher_Db::GetCoursesByCohort($cohort->id)) {
                
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
        
    public function validation($data, $files) {
        
        $errors = parent::validation($data, $files);
        
        $max_vouchers_amount = get_config('voucher', 'max_vouchers');
        if ($data['voucher_amount'] > $max_vouchers_amount) {
            $errors['voucher_amount'] = get_string('error:voucher_amount_too_high', BLOCK_VOUCHER, array('max_vouchers'=>$max_vouchers_amount));
        }
        
        // When we want to use alternative email
        if (isset($data['use_alternative_email']) && $data['use_alternative_email']) {
            
            if (empty($data['alternative_email'])) {
                
                $error['alternative_email'] = get_string('error:alternative_email_required', BLOCK_VOUCHER);
                
            } elseif (!filter_var($data['alternative_email'], FILTER_VALIDATE_EMAIL)) {
            
                $error['alternative_email'] = get_string('error:alternative_email_invalid', BLOCK_VOUCHER);
                
            }
        }
        
        // If both elements or neither elements are set
        if ((!empty($files) && isset($files['recipients'])) && isset($data['voucher_amount']) && !empty($data['voucher_amount'])) {
            
            $errors['voucher_amount'] = get_string('error:voucher_amount-recipients-both-set', BLOCK_VOUCHER);
            
        }elseif((empty($files) || !isset($files['recipients'])) && !isset($data['voucher_amount']) || empty($data['voucher_amount'])) {
            
            $errors['voucher_amount'] = get_string('error:voucher_amount-recipients-both-unset', BLOCK_VOUCHER);
            
        }
        
        return $errors;
    }

}


?>
