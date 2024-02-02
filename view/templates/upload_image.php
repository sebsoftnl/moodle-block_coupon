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
 * Handles uploading files
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$struploadimage = get_string('uploadimage', 'block_coupon');

// Set the page variables.
$pageurl = new moodle_url('/blocks/coupon/view/templates/upload_image.php');
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title($SITE->fullname);

// If we are in the system context then we are managing templates, and we want to show that in the navigation.
if ($context->contextlevel == CONTEXT_SYSTEM) {
    $PAGE->set_pagelayout('report');
    $PAGE->set_heading($SITE->fullname);

    $urloverride = new \moodle_url('/admin/settings.php?section=blocksettingcoupon');
    \navigation_node::override_active_url($urloverride);
} else {
    $PAGE->set_heading(format_string($COURSE->fullname));
}

// Additional page setup.
$PAGE->navbar->add($struploadimage);

$uploadform = new \block_coupon\template\upload_image_form();

if ($uploadform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php?section=blocksettingcoupon'));
} else if ($data = $uploadform->get_data()) {
    // Handle file uploads.
    \block_coupon\template::upload_files($data->templateimage, $context->id);

    redirect(new moodle_url('/blocks/coupon/view/templates/upload_image.php'), get_string('changessaved'));
}

$renderer = $PAGE->get_renderer('block_coupon');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('rearrangeelementsheading', 'block_coupon'), 3);
echo html_writer::start_div('block-coupon-container');
echo html_writer::start_div();
echo $renderer->get_tabs($PAGE->context, 'cptpl');
echo html_writer::end_div();
$uploadform->display();
echo html_writer::end_div();
echo $OUTPUT->footer();
