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
 * validate coupon input
 *
 * File         input_coupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/coupon/classes/settings.php');

use block_coupon\helper;
use block_coupon\forms\coupon\validator;

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

$PAGE->navbar->add(get_string('view:input_coupon:title', 'block_coupon'));

$url = new moodle_url('/blocks/coupon/view/input_coupon.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:input_coupon:title', 'block_coupon'));
$PAGE->set_heading(get_string('view:input_coupon:heading', 'block_coupon'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Make sure the moodle editmode is off.
helper::force_no_editing_mode();

require_capability('block/coupon:inputcoupons', $context);
// Include the form.
$mform = new validator($url);
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
} else if ($data = $mform->get_data()) {
    $redirecturl = helper::claim_coupon($data->coupon_code);
    // Redirect to my directly.
    redirect($redirecturl, get_string('success:coupon_used', 'block_coupon'));
} else {
    echo $OUTPUT->header();
    echo '<div class="block-coupon-container">';
    $mform->display();
    echo '</div>';
    echo $OUTPUT->footer();
}