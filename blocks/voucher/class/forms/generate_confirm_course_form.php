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
        if (!$str_info = get_config('voucher', 'info_voucher_confirm')) $str_info = get_string('missing_config_info', BLOCK_VOUCHER);
        $mform->addElement('static', 'info', '', $str_info);

        // determine which type of settings we'll use
        $radioarray=array();
        $radioarray[] =& $mform->createElement('radio', 'showform', '', get_string('showform-csv', BLOCK_VOUCHER), 'csv', array('onchange'=>'showHide(this.value)'));
        $radioarray[] =& $mform->createElement('radio', 'showform', '', get_string('showform-amount', BLOCK_VOUCHER), 'amount', array('onchange'=>'showHide(this.value)'));
        $mform->addGroup($radioarray, 'radioar', get_string('label:showform', BLOCK_VOUCHER), array(' '), false);
        $mform->setDefault('showform', 'csv');

        
        // Send vouchers based on CSV upload
        $mform->addElement('header', 'csvForm', get_string('heading:csvForm', BLOCK_VOUCHER));

        // Filepicker
        $urlDownloadCsv = '<a href="' . $CFG->wwwroot . '/blocks/voucher/sample.csv" target="_blank">' . get_string('download-sample-csv', BLOCK_VOUCHER) . '</a>';
        $mform->addElement('filepicker', 'voucher_recipients', get_string('label:voucher_recipients', BLOCK_VOUCHER), null, array('accepted_types' => 'csv'));
        $mform->addRule('voucher_recipients', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('voucher_recipients', 'label:voucher_recipients', BLOCK_VOUCHER);
        $mform->addElement('static', 'sample_csv', '', $urlDownloadCsv);

        // Editable email message
        $mform->addElement('editor', 'email_body', get_string('label:email_body', BLOCK_VOUCHER));
        $mform->setType('email_body', PARAM_RAW);
        $mform->setDefault('email_body', array('text'=>get_string('voucher_mail_content', BLOCK_VOUCHER)));
        $mform->addRule('email_body', get_string('required'), 'required');
        $mform->addHelpButton('email_body', 'label:email_body', BLOCK_VOUCHER);

        
        // Send vouchers based on Amount field
        $mform->addElement('header', 'amountForm', get_string('heading:amountForm', BLOCK_VOUCHER));

        // Set email_to variable
        $use_alternative_email = get_config('voucher', 'use_alternative_email');
        $alternative_email = get_config('voucher', 'alternative_email');

        // Amount of vouchers
        $mform->addElement('text', 'voucher_amount', get_string('label:voucher_amount', BLOCK_VOUCHER));
        $mform->setType('voucher_amount', PARAM_INT);
        $mform->addRule('voucher_amount', get_string('error:numeric_only', BLOCK_VOUCHER), 'numeric');
        $mform->addRule('voucher_amount', get_string('required'), 'required');
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


        // Settings that apply for both csv and amount
        $mform->addElement('header', 'lastSettings', get_string('heading:general_settings', BLOCK_VOUCHER));
        
        // Configurable redirect url
        $mform->addElement('text', 'redirect_url', get_string('label:redirect_url', BLOCK_VOUCHER));
        $mform->setType('redirect_url', PARAM_RAW);
        $mform->setDefault('redirect_url', $CFG->wwwroot . '/my');
        $mform->addRule('redirect_url', get_string('required'), 'required');
        $mform->addHelpButton('redirect_url', 'label:redirect_url', BLOCK_VOUCHER);
        
        // Configurable enrolment time
        $mform->addElement('text', 'enrolment_period', get_string('label:enrolment_period', BLOCK_VOUCHER));
        $mform->addRule('enrolment_period', get_string('required'), 'required');
        $mform->setType('enrolment_period', PARAM_INT);
        $mform->setDefault('enrolment_period', '0');
        $mform->addHelpButton('enrolment_period', 'label:enrolment_period', BLOCK_VOUCHER);
        
        // Configurable enrolment time
        $mform->addElement('date_selector', 'date_send_vouchers', get_string('label:date_send_vouchers', BLOCK_VOUCHER));
        $mform->addRule('date_send_vouchers', get_string('required'), 'required');
        $mform->addHelpButton('date_send_vouchers', 'label:date_send_vouchers', BLOCK_VOUCHER);

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

        
        // All elements added, add the custom js function and submit buttons
        $mform->addElement('html', "
            <script type='text/javascript'>
            window.onload=showHide('csv');
            
            function showHide(fieldValue) {
                if (fieldValue == 'csv') {
                    document.getElementById('id_amountForm').style.display='none';
                } else {
                    document.getElementById('id_csvForm').style.display='none';
                }
                document.getElementById('id_' + fieldValue + 'Form').style.display='block';
            }
            </script>
        ");
        $this->add_action_buttons(true, get_string('button:save', BLOCK_VOUCHER));

    }
    
    public function validation($data, $files) {
        
        // Set which fields to validate, depending on form used
        if ($data['showform'] == 'csv') {
            $data2validate = array(
                'email_body' => $data['email_body']
            );
        } else {
            $data2validate = array(
                'voucher_amount' => $data['voucher_amount'],
                'alternative_email' => $data['alternative_email']
            );
        }
        $data2validate['redirect_url'] = $data['redirect_url'];
        $data2validate['enrolment_period'] = $data['enrolment_period'];
        $data2validate['date_send_vouchers'] = $data['date_send_vouchers'];
        
        // Validate
        $errors = parent::validation($data2validate, $files);
        
        // Custom validate
        if ($data['showform'] == 'csv') {
            
            // Unfortunately we've gotte validate csv after saving it, so we'll do it in the view
            
        } else {

            // Max amount of vouchers
            $max_vouchers_amount = get_config('voucher', 'max_vouchers');
            if ($data['voucher_amount'] > $max_vouchers_amount) {
                $errors['voucher_amount'] = get_string('error:voucher_amount_too_high', BLOCK_VOUCHER, array('max_vouchers'=>$max_vouchers_amount));
            }
            // Alternative email required if use_alternative_email is checked
            if ($data['use_alternative_email'] && empty($data['alternative_email'])) {

                $errors['alternative_email'] = get_string('error:alternative_email_required', BLOCK_VOUCHER);

            }
            
        }
        
        return $errors;
    }
    
}


?>
