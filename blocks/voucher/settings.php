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
require_once BLOCK_VOUCHER_CLASSROOT . 'customIntConfigText.php';

defined('MOODLE_INTERNAL') || die('moodle_internal not defined');
if ($ADMIN->fulltree)
{

    $settings->add(new admin_setting_configcheckbox(
            'voucher/use_supportuser',
            get_string('use_supportuser', BLOCK_VOUCHER),
            get_string('use_supportuser_desc', BLOCK_VOUCHER),
            1
        ));

    $settings->add(new admin_setting_customIntConfigText(
            'voucher/voucher_code_length',
            get_string('voucher_code_length', BLOCK_VOUCHER),
            get_string('voucher_code_length_desc', BLOCK_VOUCHER),
            '16'
        ));
}

