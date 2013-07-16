<?php

/*
 * File: access.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

defined('MOODLE_INTERNAL') || die;

$capabilities = array(
    'block/voucher:administration' => array(
        'captype' => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    ),
    'block/voucher:generatevouchers' => array(
        'captype' => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        )
    ),
    'block/voucher:inputvouchers' => array(
        'captype' => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW,
            'guest' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
        )
    ),
    'block/voucher:addinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    )

);
