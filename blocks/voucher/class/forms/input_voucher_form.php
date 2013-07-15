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

        $this->add_action_buttons(true, get_string('button:submit_voucher_code', BLOCK_VOUCHER));
        
    }
    
}


?>
