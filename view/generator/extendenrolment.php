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
 * display unused coupons
 *
 * File         extendenrolment.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Login_check is done in couponpage class.
// @codingStandardsIgnoreLine
require_once(dirname(__FILE__) . '/../../../../config.php');

use block_coupon\couponpage;

$cid = optional_param('cid', null, PARAM_INT);

if (empty($cid)) {
    $course = get_site();
    $context = \context_system::instance();
} else {
    $course = $DB->get_record('course', array('id' => $cid));
    $context = \context_course::instance($cid);
}

$title = get_string('view:extendenrolment:title', 'block_coupon');
$heading = get_string('view:extendenrolment:heading', 'block_coupon');

$url = couponpage::get_view_url('generator/extendenrolment.php');
$page = couponpage::setup(
    'block_coupon_view_generator_extendenrolment',
    $title,
    $url,
    'block/coupon:extendenrolments',
    $context,
    [
        'pagelayout' => 'report',
        'title' => $title,
        'heading' => $heading
    ]
);

// Using a manager.
$renderer = $PAGE->get_renderer('block_coupon');
$requestcontroller = new \block_coupon\controller\generator\extendenrolmentcoupon($PAGE, $OUTPUT, $renderer);
$requestcontroller->execute_request();
