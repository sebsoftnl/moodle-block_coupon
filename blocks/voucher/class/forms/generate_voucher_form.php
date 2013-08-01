<?php

/*
 * File: generate_voucher_form.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

require_once $CFG->libdir . '/formslib.php';

/**
 * Description of purchase_form
 *
 * @author Rogier
 */
class generate_voucher_form extends moodleform
{

    /**
     * form definition
     * @global stdClass $CFG
     * @global moodle_database $DB 
     */
    function definition()
    {
        global $CFG, $DB, $USER;

        $mform = & $this->_form;
        
        $mform->addElement('header', 'header', get_string('heading:info', BLOCK_VOUCHER));
        $mform->addElement('static', 'info', '', get_string('info:voucher_type', BLOCK_VOUCHER));
        
        $mform->addElement('header', 'header', get_string('heading:voucher_type', BLOCK_VOUCHER));
        
        // Type of voucher
        $type_options = array();
        $type_options[] =& $mform->createElement('radio', 'type', '', get_string('label:type_course', BLOCK_VOUCHER), 0);
        $type_options[] =& $mform->createElement('radio', 'type', '', get_string('label:type_cohorts', BLOCK_VOUCHER), 1);
        $mform->addGroup($type_options, 'voucher_type', get_string('label:voucher_type', BLOCK_VOUCHER), array(' '));
        $mform->setDefault('yesno', 0);
        $mform->addRule('voucher_type', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_type', 'label:voucher_type', BLOCK_VOUCHER);

        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}
