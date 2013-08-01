<?php

/*
 * File: settings.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */
require_once(BLOCK_VOUCHER_CLASSROOT . 'admin_setting_customConfigTextInt.php');

defined('MOODLE_INTERNAL') || die('moodle_internal not defined');
if ($ADMIN->fulltree)
{

//    // Use alternative email
//    $usealtemail_help = voucher_Helper::generateHelpButton(
//        'label:use_alternative_email',
//        BLOCK_VOUCHER
//    );
    $settings->add(new admin_setting_configcheckbox(
            'voucher/use_alternative_email',
            get_string('label:use_alternative_email', BLOCK_VOUCHER),
            get_string('label:use_alternative_email_desc', BLOCK_VOUCHER),
            0
        ));
//    $altemail_help = voucher_Helper::generateHelpButton(
//        'label:alternative_email',
//        BLOCK_VOUCHER
//    );
    $settings->add(new admin_setting_configtext(
            'voucher/alternative_email',
            get_string('label:alternative_email', BLOCK_VOUCHER),
            get_string('label:alternative_email_desc', BLOCK_VOUCHER),
            ''
        ));
    
    $max_code_length_choices = array(6=>6, 8=>8, 16=>16, 32=>32);
    $settings->add(new admin_setting_configselect(
            'voucher/voucher_code_length',
            get_string('label:voucher_code_length', BLOCK_VOUCHER),
            get_string('label:voucher_code_length_desc', BLOCK_VOUCHER),
            16,
            $max_code_length_choices
        ));
    
    $max_voucher_choices = array(5=>5, 10=>10, 25=>25, 50=>50, 100=>100);
    $settings->add(new admin_setting_configselect(
            'voucher/max_vouchers',
            get_string('label:max_vouchers', BLOCK_VOUCHER),
            get_string('label:max_vouchers_desc', BLOCK_VOUCHER),
            50,
            $max_voucher_choices
        ));

    $settings->add(new admin_setting_configcheckbox(
            'voucher/api_enabled',
            get_string('label:api_enabled', BLOCK_VOUCHER),
            get_string('label:api_enabled_desc', BLOCK_VOUCHER),
            0
        ));

    $settings->add(new admin_setting_configtext(
            'voucher/api_user',
            get_string('label:api_user', BLOCK_VOUCHER),
            get_string('label:api_user_desc', BLOCK_VOUCHER),
            ''
        ));
    
    $settings->add(new admin_setting_configtext(
            'voucher/api_password',
            get_string('label:api_password', BLOCK_VOUCHER),
            get_string('label:api_password_desc', BLOCK_VOUCHER),
            ''
        ));

}
