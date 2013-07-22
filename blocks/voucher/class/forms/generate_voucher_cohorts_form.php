<?php

/*
 * File: generate_voucher_cohorts_form.php
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
class generate_voucher_cohorts_form extends moodleform
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
        
        $mform->addElement('header', 'header', get_string('heading:input_cohorts', BLOCK_VOUCHER));
        
        // First we'll get some useful info
        $cohorts = $DB->get_records('cohort');
        
        // And create data for multiselect
        $arr_cohort_select = array();
        foreach($cohorts as $cohort) $arr_cohort_select[$cohort->id] = $cohort->name;

        // Course id
        $select_cohorts = &$mform->addElement('select', 'voucher_cohorts', get_string('label:voucher_cohorts', BLOCK_VOUCHER), $arr_cohort_select);
        $select_cohorts->setMultiple(true);
        $mform->addRule('voucher_cohorts', get_string('error:required', BLOCK_VOUCHER), 'required', null, 'client');
        $mform->addHelpButton('voucher_cohorts', 'label:voucher_cohorts', BLOCK_VOUCHER);

        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
        
    }
    
}


?>
