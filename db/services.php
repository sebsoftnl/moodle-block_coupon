<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Service definitions for block_coupon
 *
 * File         webservices.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$services = [
    'couponservice' => [
        'functions' => [
            'block_coupon_get_courses',
            'block_coupon_get_cohorts',
            'block_coupon_get_course_groups',
            'block_coupon_request_coupon_codes_for_course',
            'block_coupon_generate_coupons_for_course',
            'block_coupon_request_coupon_codes_for_cohorts',
            'block_coupon_generate_coupons_for_cohorts',
            'block_coupon_get_coupon_reports',
            'block_coupon_find_users',
            'block_coupon_find_courses',
            'block_coupon_find_potential_cohort_courses',
            'block_coupon_find_cohorts',
            'block_coupon_claim_coupon',
        ],
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];

$functions = [
    'block_coupon_get_courses' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'get_courses',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get courses.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_get_cohorts' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'get_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get cohorts.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_get_course_groups' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'get_course_groups',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get course groups.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_request_coupon_codes_for_course' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'request_coupon_codes_for_course',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupon codes for a course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_generate_coupons_for_course' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'generate_coupons_for_course',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupons for a course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_request_coupon_codes_for_cohorts' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'request_coupon_codes_for_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupon codes for cohort(s].',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_generate_coupons_for_cohorts' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'generate_coupons_for_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupons for cohort(s].',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_get_coupon_reports' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'get_coupon_reports',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get coupon reports.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_find_users' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'find_users',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find users.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_find_courses' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'find_courses',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find courses.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_find_potential_cohort_courses' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'find_potential_cohort_courses',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find potential courses to connect to a cohort.',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_find_cohorts' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'find_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find cohorts. Only use for AMD please',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_coupon_claim_coupon' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'claim_coupon',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Claim coupon code',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_delete_coupons' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'delete_coupons',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Delete coupons by id',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_find_batches' => [
        'classname' => 'block_coupon_external',
        'methodname' => 'find_batches',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find batches. Only use for AMD please',
        'type' => 'read',
        'ajax' => true,
    ],

    'block_coupon_delete_mailtemplate' => [
        'classname' => '\\block_coupon\\external\\mailtemplates',
        'methodname' => 'delete_template',
        'description' => 'delete_template',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_duplicate_mailtemplate' => [
        'classname' => '\\block_coupon\\external\\mailtemplates',
        'methodname' => 'duplicate_template',
        'description' => 'duplicate_template',
        'type' => 'write',
        'ajax' => true,
    ],

    'block_coupon_delete_template' => [
        'classname' => '\\block_coupon\\external\\templates',
        'methodname' => 'delete_template',
        'description' => 'delete_template',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_duplicate_template' => [
        'classname' => '\\block_coupon\\external\\templates',
        'methodname' => 'duplicate_template',
        'description' => 'duplicate_template',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_update_element_positions' => [
        'classname' => '\\block_coupon\\external\\templates',
        'methodname' => 'update_element_positions',
        'description' => 'update_element_positions',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_save_element' => [
        'classname' => '\\block_coupon\\external\\templates',
        'methodname' => 'save_element',
        'description' => 'save_element',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_coupon_get_element_html' => [
        'classname' => '\\block_coupon\\external\\templates',
        'methodname' => 'get_element_html',
        'description' => 'get_element_html',
        'type' => 'write',
        'ajax' => true,
    ],
];
