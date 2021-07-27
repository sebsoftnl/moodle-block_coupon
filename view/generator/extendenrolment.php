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
require_once(dirname(__FILE__) . '/../../../../config.php');
use block_coupon\helper;

$cid = optional_param('cid', null, PARAM_INT);
$id = required_param('id', PARAM_INT);

if (empty($cid)) {
    $course = get_site();
    $context = \context_system::instance();
} else {
    $course = $DB->get_record('course', array('id' => $cid));
    $context = \context_course::instance($cid);
}

require_login($course, true);

$title = 'view:extendenrolment:title';
$heading = 'view:extendenrolment:heading';

$PAGE->navbar->add(get_string($title, 'block_coupon'));

$url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/extendenrolment.php', array('id' => $id, 'cid' => $cid));
$PAGE->set_url($url);

$PAGE->set_title(get_string($title, 'block_coupon'));
$PAGE->set_heading(get_string($heading, 'block_coupon'));
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');

// Make sure the moodle editmode is off.
helper::force_no_editing_mode();
require_capability('block/coupon:extendenrolments', $context);
$renderer = $PAGE->get_renderer('block_coupon');

// Using a manager.
$requestcontroller = new \block_coupon\controller\generator\extendenrolmentcoupon($PAGE, $OUTPUT, $renderer);
$requestcontroller->execute_request();
