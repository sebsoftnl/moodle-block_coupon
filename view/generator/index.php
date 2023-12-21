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
// Login_check is done in couponpage class.
// @codingStandardsIgnoreLine
require_once(dirname(__FILE__) . '/../../../../config.php');

use block_coupon\couponpage;
use block_coupon\coupon\generatoroptions;
use block_coupon\forms\coupon\generator\chooser;

$title = get_string('view:generate_coupon:title', 'block_coupon');
$heading = get_string('view:generate_coupon:heading', 'block_coupon');

$url = couponpage::get_view_url('generator/index.php');
$page = couponpage::setup(
    'block_coupon_view_generator_index',
    $title,
    $url,
    'block/coupon:generatecoupons',
    \context_system::instance(),
    [
        'pagelayout' => 'report',
        'title' => $title,
        'heading' => $heading
    ]
);

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
        case 2:
            $generatoroptions->type = generatoroptions::COURSEGROUPING;
            break;
    }
    // Serialize generatoroptions to session.
    $generatoroptions->to_session();
    // And redirect user to next page.
    switch ($generatoroptions->type) {
        case generatoroptions::COURSE:
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/course.php');
            break;
        case generatoroptions::COHORT:
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/cohort.php');
            break;
        case generatoroptions::COURSEGROUPING:
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/coursegrouping.php');
            break;
        default:
            // Try autodetect.
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/' .
                    $generatoroptions->type . '.php');
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
