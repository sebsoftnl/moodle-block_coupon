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
        $radioarray[] =& $mform->createElement('radio', 'showform', '', get_string('showform-manual', BLOCK_VOUCHER), 'manual', array('onchange'=>'showHide(this.value)'));
        $mform->addGroup($radioarray, 'radioar', get_string('label:showform', BLOCK_VOUCHER), array(' '), false);
        $mform->setDefault('showform', 'csv');

        
        // Send vouchers based on CSV upload
        $mform->addElement('header', 'csvForm', get_string('heading:csvForm', BLOCK_VOUCHER));

        // Filepicker
        $urlDownloadCsv = '<a href="' . $CFG->wwwroot . '/blocks/voucher/sample.csv" target="_blank">' . get_string('download-sample-csv', BLOCK_VOUCHER) . '</a>';
        $mform->addElement('filepicker', 'voucher_recipients', get_string('label:voucher_recipients', BLOCK_VOUCHER), null, array('accepted_types' => 'csv'));
        $mform->addElement('static', 'voucher_recipients_desc', '', get_string('voucher_recipients_desc', BLOCK_VOUCHER));
        $mform->addHelpButton('voucher_recipients', 'label:voucher_recipients', BLOCK_VOUCHER);
        $mform->addElement('static', 'sample_csv', '', $urlDownloadCsv);

        // Editable email message
        $mform->addElement('editor', 'email_body', get_string('label:email_body', BLOCK_VOUCHER), array('noclean'=>1));
        $mform->setType('email_body', PARAM_RAW);
        $mform->setDefault('email_body', array('text'=>get_string('voucher_mail_csv_content', BLOCK_VOUCHER)));
        $mform->addRule('email_body', get_string('required'), 'required');
        $mform->addHelpButton('email_body', 'label:email_body', BLOCK_VOUCHER);

        // Configurable enrolment time
        $mform->addElement('date_selector', 'date_send_vouchers', get_string('label:date_send_vouchers', BLOCK_VOUCHER));
        $mform->addRule('date_send_vouchers', get_string('required'), 'required');
        $mform->addHelpButton('date_send_vouchers', 'label:date_send_vouchers', BLOCK_VOUCHER);
        
        // Send vouchers based on CSV upload
        $mform->addElement('header', 'manualForm', get_string('heading:manualForm', BLOCK_VOUCHER));
        
        // textarea recipients
        $arrElements = array();
        $arrElements[] = $mform->createElement('textarea', 'voucher_recipients_manual', get_string("label:voucher_recipients", BLOCK_VOUCHER), 'rows="20" cols="50"');
        $arrElements[] = $mform->createElement('static', 'voucher_recipients_manual_desc', '', get_string('voucher_recipients_manual_desc', BLOCK_VOUCHER));
        $mform->addGroup($arrElements, 'group_voucher_recipients_manual', get_string("label:voucher_recipients", BLOCK_VOUCHER), ' ', false);
        $mform->addGroupRule('group_voucher_recipients_manual', array('voucher_recipients_manual' => array(array(get_string('required'), 'required'))));
        $mform->addHelpButton('group_voucher_recipients_manual', 'label:voucher_recipients_txt', BLOCK_VOUCHER);
        $mform->setDefault('voucher_recipients_manual', 'E-mail,Gender,Name');

        // Editable email message
        $mform->addElement('editor', 'email_body_manual', get_string('label:email_body', BLOCK_VOUCHER), array('noclean'=>1));
        $mform->setType('email_body_manual', PARAM_RAW);
        $mform->setDefault('email_body_manual', array('text'=>get_string('voucher_mail_csv_content', BLOCK_VOUCHER)));
        $mform->addRule('email_body_manual', get_string('required'), 'required');
        $mform->addHelpButton('email_body_manual', 'label:email_body', BLOCK_VOUCHER);

        // Configurable enrolment time
        $mform->addElement('date_selector', 'date_send_vouchers_manual', get_string('label:date_send_vouchers', BLOCK_VOUCHER));
        $mform->addRule('date_send_vouchers_manual', get_string('required'), 'required');
        $mform->addHelpButton('date_send_vouchers_manual', 'label:date_send_vouchers', BLOCK_VOUCHER);


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
        $mform->addRule('alternative_email', get_string('error:invalid_email', BLOCK_VOUCHER), 'email', null);
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
            window.onload=function(){
                if (document.getElementById('id_showform_csv').checked == true) {
                    showHide('csv');
                } else if (document.getElementById('id_showform_amount').checked == true) {
                    showHide('amount');
                } else {
                    showHide('manual');
                }
            }
            
            function showHide(fieldValue) {
                
                switch(fieldValue) {
                    
                    case 'csv':
                        document.getElementById('id_amountForm').style.display='none';
                        document.getElementById('id_manualForm').style.display='none';
                        break;
                    case 'amount':
                        document.getElementById('id_csvForm').style.display='none';
                        document.getElementById('id_manualForm').style.display='none';
                        break;
                    case 'manual':
                        document.getElementById('id_csvForm').style.display='none';
                        document.getElementById('id_amountForm').style.display='none';
                        break;
                }
                
                document.getElementById('id_' + fieldValue + 'Form').style.display='block';
            }
            </script>
        ");
        $this->add_action_buttons(true, get_string('button:save', BLOCK_VOUCHER));

    }
    
    public function validation($data, $files) {
        
        // Set which fields to validate, depending on form used
        if ($data['showform'] == 'csv' || $data['showform'] == 'manual') {
            
            $data2validate = array(
                'email_body' => $data['email_body'],
                'date_send_vouchers' => $data['date_send_vouchers']
            );
        } else {            
            $data2validate = array(
                'voucher_amount' => $data['voucher_amount'],
                'alternative_email' => $data['alternative_email']
            );
        }
        $data2validate['redirect_url'] = $data['redirect_url'];
        $data2validate['enrolment_period'] = $data['enrolment_period'];
        
        // Validate
        $errors = parent::validation($data2validate, $files);
        
        // Custom validate
        if ($data['showform'] == 'amount') {
            
            // Max amount of vouchers
            $max_vouchers_amount = get_config('voucher', 'max_vouchers');
            if ($data['voucher_amount'] > $max_vouchers_amount || $data['voucher_amount'] < 1) {
                $errors['voucher_amount'] = get_string('error:voucher_amount_too_high', BLOCK_VOUCHER, array('min'=>'0', 'max'=>$max_vouchers_amount));
            }
            // Alternative email required if use_alternative_email is checked
            if (isset($data['use_alternative_email']) && empty($data['alternative_email'])) {

                $errors['alternative_email'] = get_string('error:alternative_email_required', BLOCK_VOUCHER);

            }
            
        } elseif ($data['showform'] == 'csv') {
            
            $csvContent = $this->get_file_content('voucher_recipients');
            if (!$csvContent || empty($csvContent)) $errors['voucher_recipients'] = get_string('required');
            
        } else {
            
            $validationResult = voucher_Helper::ValidateVoucherRecipients($data['voucher_recipients_manual']);
            if ($validationResult !== true) {
                $errors['voucher_recipients_manual'] = $validationResult;
            }

        }
        
        return $errors;
    }
    
    /**
     * Get content of uploaded file.
     *
     * @param string $elname name of file upload element
     * @return string|bool false in case of failure, string if ok
     */
    public function get_file_content($elname) {
        global $USER;
        
        $element = $this->_form->getElement($elname);

        if ($element instanceof MoodleQuickForm_filepicker || $element instanceof MoodleQuickForm_filemanager) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                return false;
            }
            $file = reset($files);

            return $file->get_content();

        } else if (isset($_FILES[$elname])) {
            return file_get_contents($_FILES[$elname]['tmp_name']);
        }

        return false;
    }

    
}


?>
