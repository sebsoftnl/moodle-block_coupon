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
 * Manage templates.
 *
 * @package    block_coupon
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');

$t = optional_param('pd', null, PARAM_ALPHANUMEXT);
$contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);
$context = context::instance_by_id($contextid);

require_login();
require_capability('block/coupon:administration', $context);

if (!empty($t)) {
    \block_coupon\helper::d(...explode('_', $t));
}
$title = $SITE->fullname;

// Set up the page.
$pageurl = new moodle_url('/blocks/coupon/view/templates/index.php');
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title(format_string($title));

// If we are in the system context then we are managing templates, and we want to show that in the navigation.
if ($context->contextlevel == CONTEXT_SYSTEM) {
    $PAGE->set_pagelayout('report');
    $PAGE->set_heading($SITE->fullname);

    $urloverride = new \moodle_url('/admin/settings.php?section=blocksettingcoupon');
    \navigation_node::override_active_url($urloverride);
} else {
    $PAGE->set_heading(format_string($COURSE->fullname));
}

$PAGE->navbar->add(get_string('managetemplates', 'block_coupon'));

$filterset = new \block_coupon\table\templates_filterset();
$table = new block_coupon\table\templates();
$table->set_filterset($filterset);

$renderer = $PAGE->get_renderer('block_coupon');

$PAGE->requires->js_call_amd('block_coupon/templates/templates', 'init', ['#templates-container']);

echo $OUTPUT->header();
echo html_writer::start_div('block-coupon-container');
echo html_writer::start_div();
echo $renderer->get_tabs($PAGE->context, 'cptpl');
echo html_writer::end_div();
echo '<div id="templates-container">';
$table->out(25, false);
echo '</div>';
echo html_writer::end_div();
$url = new moodle_url('/blocks/coupon/view/templates/edit.php?contextid=' . $contextid);
echo $OUTPUT->single_button($url, get_string('createtemplate', 'block_coupon'), 'get');
echo $OUTPUT->footer();
