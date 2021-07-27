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
 * File         unused_coupons.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../../config.php');

use block_coupon\helper;

$id = required_param('id', PARAM_INT);

$instance = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$context       = \context_block::instance($instance->id);
$coursecontext = $context->get_course_context(false);
$course = false;
if ($coursecontext !== false) {
    $course = $DB->get_record("course", array("id" => $coursecontext->instanceid));
}
if ($course === false) {
    $course = get_site();
}

require_login($course, true);

$title = 'view:userrequest:title';
$heading = 'view:userrequest:heading';

$PAGE->navbar->add(get_string($title, 'block_coupon'));

$url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string($title, 'block_coupon'));
$PAGE->set_heading(get_string($heading, 'block_coupon'));
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');

// If you're no request user, deny access.
if (!$DB->record_exists('block_coupon_rusers', ['userid' => $USER->id])) {
    throw new \block_coupon\exception('err:not-a-requestuser');
}

// Make sure the moodle editmode is off.
helper::force_no_editing_mode();
$renderer = $PAGE->get_renderer('block_coupon');

$controller = new \block_coupon\controller\myrequests($PAGE, $OUTPUT, $renderer);
$controller->execute_request();
