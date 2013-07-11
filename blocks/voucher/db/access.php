<?php
/**
 * File: access.php
 * Encoding: UTF-8
 * @package: voucher
 *
 * @Version: 1.0.0
 * @Since 11-jul-2013
 * @Author: Menno de Ridder :: menno@sebsoft.nl
 * @Copyright menno@sebsoft.nl
 *
 **/
defined('MOODLE_INTERNAL') || die;

$capabilities = array(
    'blocks/voucher:administration' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    ),
    'blocks/voucher:use_voucher' => array(
        'captype' => 'view',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_ALLOW
        )
    )
);
