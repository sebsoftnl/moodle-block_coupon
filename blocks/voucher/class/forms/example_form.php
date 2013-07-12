<?php

/*
 * File: example_form.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

require_once $CFG->libdir.'/formslib.php';

/**
 * Description of purchase_form
 *
 * @author Rogier
 */
class example_form extends moodleform
{
 
    /**
     * form definition
     * @global stdClass $CFG
     * @global moodle_database $DB 
     */
    function definition()
    {
        global $CFG, $DB;
        
        $mform =& $this->_form;
        
        // add elements
        //$attribs = array();
        //$textbox = $mform->addElement('text', 'argname', get_string('label:argname', BLOCK_VOUCHER));
        //$mform->setType('argname', PARAM_TEXT);
        
        // buttons
        $this->add_action_buttons();
        
    }
    
}

?>