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
        
        $recipients = voucher_Helper::GetRecipientsFromCsv($data['voucher_recipients']);
        if (!$recipients) $errors['voucher_recipients'] = get_string('error:recipients-invalid', BLOCK_VOUCHER);
        if (!count($recipients) > 0) $errors['voucher_recipients'] = get_string('error:recipients-empty', BLOCK_VOUCHER);
        
        // Set errors below only if none are set yet
        if (!empty($errors)) return $errors;
        
        // Check max of the file
        if (count($recipients) > 10000) {
            $errors['voucher_recipients'] = get_string('error:recipients-max-exceeded', BLOCK_VOUCHER);
            return $errors;
        }

        foreach($recipients as $recipient) {

            $conditions = array();
            // Run search on username, or firstname & lastname & email
            if (!empty($recipient->username)) {

                $conditions['username'] = $recipient->username;

            }

            if (!empty($recipient->firstname) || !empty($recipient->lastname) || !empty($recipient->email)) {

                $conditions['firstname'] = trim($recipient->firstname);
                $conditions['lastname'] = trim($recipient->lastname);
                $conditions['email'] = trim($recipient->email);

            }

            if (!voucher_Db::GetUser($conditions)) {
                $errors['voucher_recipients'] = get_string('error:recipients-unknown-user', BLOCK_VOUCHER);
                break;
            }
        }
        
        return $errors;
    }
    
}

?>