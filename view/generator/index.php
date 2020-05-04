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
 * Generator index (chooser)
 *
 * File         unused_coupons.php
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
use block_coupon\coupon\generatoroptions;
use block_coupon\forms\coupon\generator\chooser;

$id = required_param('id', PARAM_INT);

$instance = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$context = \context_block::instance($instance->id);
$coursecontext = $context->get_course_context(false);
$course = false;
if ($coursecontext !== false) {
    $course = $DB->get_record("course", array("id" => $coursecontext->instanceid));
}
if ($course === false) {
    $course = get_site();
}

require_login($course, true);

$title = 'view:generate_coupon:title';
$heading = 'view:generate_coupon:heading';

$PAGE->navbar->add(get_string($title, 'block_coupon'));

$url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/index.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string($title, 'block_coupon'));
$PAGE->set_heading(get_string($heading, 'block_coupon'));
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');

// Make sure the moodle editmode is off.
helper::force_no_editing_mode();
require_capability('block/coupon:generatecoupons', $context);
$renderer = $PAGE->get_renderer('block_coupon');

$mform = new chooser($url);
if ($mform->is_cancelled()) {
    generatoroptions::clean_session();
    redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $PAGE->course->id)));
} else if ($data = $mform->get_data()) {
    // We will always force cleaning of generator options here.
    generatoroptions::clean_session();
    // Load generator options.
    $generatoroptions = generatoroptions::from_session();
    $generatoroptions->ownerid = $USER->id;
    switch ($data->coupon_type['type']) {
        case 0:
            $generatoroptions->type = generatoroptions::COURSE;
            break;
        case 1:
            $generatoroptions->type = generatoroptions::COHORT;
            break;
    }
    // Serialize generatoroptions to session.
    $generatoroptions->to_session();
    // And redirect user to next page.
    switch ($generatoroptions->type) {
        case generatoroptions::COURSE:
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/course.php',
                    ['id' => $id]);
            break;
        case generatoroptions::COHORT:
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/cohort.php',
                    ['id' => $id]);
            break;
        default:
            // Try autodetect.
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/' .
                    $generatoroptions->type . '.php', ['id' => $id]);
            break;
    }
    redirect($redirect);
}

echo $OUTPUT->header();
echo html_writer::start_div('block-coupon-container');
echo html_writer::start_div();
echo $renderer->get_tabs($PAGE->context, 'wzcoupons', $PAGE->url->params());
echo $mform->render();
echo html_writer::end_div();
echo $OUTPUT->footer();
