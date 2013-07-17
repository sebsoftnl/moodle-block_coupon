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

defined('MOODLE_INTERNAL') || die('moodle_internal not defined');
if ($ADMIN->fulltree)
{
//	// Cron disabling
//    $settings->add(new admin_setting_configcheckbox(
//            'voucher_enablecron', '', 
//            get_string("form-desc:voucher_enablecron", "block_voucher"),
//            '1', '1', '0'
//        ));
//
//	// Debugging settings
//    $settings->add(new admin_setting_configcheckbox(
//            'voucher_enabledebug', '', 
//            get_string("form-desc:voucher_enabledebug", "block_voucher"),
//            '0', '1', '0'
//        ));
//
    
//$settings->add(new admin_setting_configcheckbox('forum_replytouser', get_string('replytouser', 'forum'),
//                    get_string('replytouser_desc', 'forum'), 1));

//    $supportuser = generate_email_supportuser();
    $settings->add(new admin_setting_configcheckbox(
            'use_supportuser',
            get_string('use_supportuser', BLOCK_VOUCHER),
            get_string('use_supportuser_desc', BLOCK_VOUCHER),
            1
        ));
    
}

