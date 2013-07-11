<?php

/**
 * File: settings.php
 * Encoding: UTF-8
 * @package: voucher
 *
 * @Version: 1.0.0
 * @Since 11-jul-2013
 * @Author: Menno de Ridder :: menno@sebsoft.nl
 * @Copyright menno@sebsoft.nl
 * 
 **/
defined('MOODLE_INTERNAL') || die('moodle_internal not defined');
if ($ADMIN->fulltree)
{
	// Cron disabling
    $settings->add(new admin_setting_configcheckbox(
            'voucher_enablecron', '', 
            get_string("form-desc:voucher_enablecron", "block_voucher"),
            '1', '1', '0'
        ));

	// Debugging settings
    $settings->add(new admin_setting_configcheckbox(
            'voucher_enabledebug', '', 
            get_string("form-desc:voucher_enabledebug", "block_voucher"),
            '0', '1', '0'
        ));

    $supportuser = generate_email_supportuser();
    $settings->add(new admin_setting_configtext(
            'voucher_debugemail', '', 
            get_string("form-desc:voucher_debugemail", "block_voucher"),
            $supportuser->email, PARAM_EMAIL
        ));

	//
}

