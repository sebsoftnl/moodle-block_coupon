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
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$services = array(
    'couponservice' => array(
        'functions' => array(
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
        ),
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);

$functions = array(
    'block_coupon_get_courses' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'get_courses',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get courses.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_get_cohorts' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'get_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get cohorts.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_get_course_groups' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'get_course_groups',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get course groups.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_request_coupon_codes_for_course' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'request_coupon_codes_for_course',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupon codes for a course.',
        'type' => 'write',
        'ajax' => true
    ),
    'block_coupon_generate_coupons_for_course' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'generate_coupons_for_course',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupons for a course.',
        'type' => 'write',
        'ajax' => true
    ),
    'block_coupon_request_coupon_codes_for_cohorts' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'request_coupon_codes_for_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupon codes for cohort(s).',
        'type' => 'write',
        'ajax' => true
    ),
    'block_coupon_generate_coupons_for_cohorts' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'generate_coupons_for_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Generate coupons for cohort(s).',
        'type' => 'write',
        'ajax' => true
    ),
    'block_coupon_get_coupon_reports' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'get_coupon_reports',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Get coupon reports.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_find_users' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'find_users',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find users.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_find_courses' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'find_courses',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find courses.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_find_potential_cohort_courses' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'find_potential_cohort_courses',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find potential courses to connect to a cohort.',
        'type' => 'read',
        'ajax' => true
    ),
    'block_coupon_find_cohorts' => array(
        'classname' => 'block_coupon_external',
        'methodname' => 'find_cohorts',
        'classpath' => 'blocks/coupon/externallib.php',
        'description' => 'Find cohorts. Only use for AMD please',
        'type' => 'read',
        'ajax' => true
    ),
);
