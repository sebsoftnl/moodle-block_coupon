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
 * Handles position elements on the PDF via drag and drop.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');

// The page of the template we are editing.
$pid = required_param('pid', PARAM_INT);

$page = $DB->get_record('block_coupon_pages', array('id' => $pid), '*', MUST_EXIST);
$templaterec = $DB->get_record('block_coupon_templates', array('id' => $page->templateid), '*', MUST_EXIST);
$elements = $DB->get_records('block_coupon_elements', array('pageid' => $pid), 'sequence');

// Set the template.
$template = new \block_coupon\template($templaterec);
// Perform checks.
require_login();
// Make sure the user has the required capabilities.
$template->require_manage();

$title = $SITE->fullname;
$heading = $title;

// Set the $PAGE settings.
$pageurl = new moodle_url('/blocks/coupon/view/templates/rearrange.php', array('pid' => $pid));
$context = $template->get_context();

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

// Add more links to the navigation.
$str = get_string('managetemplates', 'block_coupon');
$link = new moodle_url('/blocks/coupon/view/templates/manage_templates.php');
$PAGE->navbar->add($str, new \action_link($link, $str));

$str = get_string('edittemplate', 'block_coupon');
$link = new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $template->get_id()]);
$PAGE->navbar->add($str, new \action_link($link, $str));

$PAGE->navbar->add(get_string('rearrangeelements', 'block_coupon'));

$wctx = (object)[
    'buttons' => [],
    'template' => $templaterec,
    'page' => $page,
    'elements' => []
];

// Create the buttons to save the position of the elements.
$wctx->buttons[] = $OUTPUT->single_button(new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $template->get_id()]),
        get_string('saveandclose', 'block_coupon'), 'get', array('class' => 'savepositionsbtn'));
$wctx->buttons[] = $OUTPUT->single_button(new moodle_url('/blocks/coupon/view/templates/rearrange.php', array('pid' => $pid)),
        get_string('saveandcontinue', 'block_coupon'), 'get', array('class' => 'applypositionsbtn'));
$wctx->buttons[] = $OUTPUT->single_button(new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $template->get_id()]),
        get_string('close', 'block_coupon'), 'get', array('class' => 'cancelbtn'));

if ($elements) {
    foreach ($elements as $element) {
        // Get an instance of the element class.
        if ($e = \block_coupon\template\element_factory::get_element_instance($element)) {
            switch ($e->get_refpoint()) {
                case \block_coupon\template\element_helper::COUPON_REF_POINT_TOPRIGHT:
                    $class = 'element refpoint-right';
                    break;
                case \block_coupon\template\element_helper::COUPON_REF_POINT_TOPCENTER:
                    $class = 'element refpoint-center';
                    break;
                case \block_coupon\template\element_helper::COUPON_REF_POINT_TOPLEFT:
                default:
                    $class = 'element refpoint-left';
            }
            switch ($e->get_alignment()) {
                case \block_coupon\template\element::ALIGN_CENTER:
                    $class .= ' align-center';
                    break;
                case \block_coupon\template\element::ALIGN_RIGHT:
                    $class .= ' align-right';
                    break;
                case \block_coupon\template\element::ALIGN_LEFT:
                default:
                    $class .= ' align-left';
                    break;
            }

            if (!$e->is_draggable_in_html_view()) {
                $class .= ' nodrag';
            }
            if (!$e->is_visible_in_html_view()) {
                $class .= ' invisible';
            }

            $el = clone $element;
            $el->draggable = $e->is_draggable_in_html_view();
            $el->visible = $e->is_visible_in_html_view();
            $el->class = $class;
            $el->rendered = $e->render_html();
            $wctx->elements[] = $el;
        }
    }
}

/** @var moodle_page $PAGE */
$PAGE->requires->css('/blocks/coupon/tplstyles.css');
$PAGE->requires->js_call_amd('block_coupon/templates/elements', 'init', ['#right-container']);
$PAGE->requires->js_call_amd('block_coupon/templates/rearrange-area', 'init', ['#pdf', $template->get_id(), $page, $elements]);

$renderer = $PAGE->get_renderer('block_coupon');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('rearrangeelementsheading', 'block_coupon'), 3);
echo html_writer::start_div('block-coupon-container');
echo html_writer::start_div();
echo $renderer->get_tabs($PAGE->context, 'cptpl');
echo html_writer::end_div();
echo $OUTPUT->notification(get_string('exampledatawarning', 'block_coupon'), \core\output\notification::NOTIFY_WARNING);
echo $OUTPUT->render_from_template('block_coupon/templates/rearrange', $wctx);
echo html_writer::end_div();
echo $OUTPUT->footer();
