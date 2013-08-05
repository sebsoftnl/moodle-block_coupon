<?php

/*
 * File: imageupload_form.php
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
class imageupload_form extends moodleform
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

        $mform->addElement('header', 'header', get_string('heading:info', BLOCK_VOUCHER));
        if (!$str_info = get_config('voucher', 'info_imageupload')) $str_info = get_string('missing_config_info', BLOCK_VOUCHER);
        $mform->addElement('static', 'info', '', $str_info);
        
        $mform->addElement('header', 'header', get_string('heading:imageupload', BLOCK_VOUCHER));

        //$mform->addElement('html', '<img src="' . BLOCK_VOUCHER_LOGOFILE . '" title="'.get_string('label:current_image', BLOCK_VOUCHER) . '" />');
        $html_image = '
        <div class="fitem">
            <div class="fitemtitle">
                <div class="fstaticlabel">
                    <label>'. get_string('label:current_image', BLOCK_VOUCHER) . '</label>
                </div>
            </div>
            <div class="felement fstatic">
                <img src="' . BLOCK_VOUCHER_WWWROOT.'view/logodisplay.php' . '" width="210" height="297" title="'.get_string('label:current_image', BLOCK_VOUCHER) . '" />
            </div>
        </div>';
        
        $mform->addElement('html', $html_image);
        
        // add elements
        $attributes = array(
            'accepted_types' => array('.png')
        );

        // Add IMAGE uploader
        $mform->addElement('filepicker', 'userfile', get_string('file'), null, $attributes);
        $mform->addRule('userfile', 'required', 'required', null, 'client');

        // buttons
        $this->add_action_buttons(true, get_string('button:next', BLOCK_VOUCHER));
    }

}

?>