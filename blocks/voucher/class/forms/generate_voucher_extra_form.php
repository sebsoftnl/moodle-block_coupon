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
class generate_voucher_extra_form extends moodleform
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
        $mform->addElement('textarea', 'voucher_recipients', get_string("label:voucher_recipients", BLOCK_VOUCHER), 'rows="20" cols="50"');
        $mform->addRule('voucher_recipients', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('voucher_recipients', 'label:voucher_recipients_txt', BLOCK_VOUCHER);
        $mform->setDefault('voucher_recipients', $SESSION->voucher->csv_content);
        
        $this->add_action_buttons(true, get_string('button:save', BLOCK_VOUCHER));
    }
    
    public function validation($data, $files) {
        
        $errors = parent::validation($data, $files);
        
        $recipientsError = voucher_Helper::ValidateVoucherRecipients($data['voucher_recipients']);
        
        if ($recipientsError !== true) {
            $errors['voucher_recipients'] = $recipientsError;
        }
        
        return $errors;
    }
    
}

?>