<?php

/*
 * File: input_voucher_form.php
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
class input_voucher_form extends moodleform
{

    /**
     * form definition
     * @global stdClass $CFG
     * @global moodle_database $DB 
     */
    function definition()
    {
        global $CFG, $DB;

        $mform = & $this->_form;
        
        // All we need is the voucher code
        $mform->addElement('text', 'voucher_code', get_string('label:voucher_code', BLOCK_VOUCHER));
        $mform->addRule('voucher_code', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');

        $this->add_action_buttons(false, get_string('button:submit_voucher_code', BLOCK_VOUCHER));
        
    }
    
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        
        if (!$voucher = $DB->get_record('vouchers', array('submission_code'=>$data['voucher_code']))) {
            $errors['voucher_code'] = get_string('error:invalid_voucher_code', BLOCK_VOUCHER);
        } elseif ($voucher->userid != null) {
            $errors['voucher_code'] = get_string('error:voucher_already_used', BLOCK_VOUCHER);
        }
//        
        return $errors;
    }
    
    
}


?>
