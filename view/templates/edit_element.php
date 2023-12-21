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
 * Edit a element.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');

$tid = required_param('tid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);

$template = $DB->get_record('block_coupon_templates', array('id' => $tid), '*', MUST_EXIST);

// Set the template object.
$template = new \block_coupon\template($template);

require_login();

// Make sure the user has the required capabilities.
$template->require_manage();
$title = $SITE->fullname;

if ($action == 'edit') {
    // The id of the element must be supplied if we are currently editing one.
    $id = required_param('id', PARAM_INT);
    $element = $DB->get_record('block_coupon_elements', array('id' => $id), '*', MUST_EXIST);
    $pageurl = new moodle_url('/blocks/coupon/view/templates/edit_element.php', ['id' => $id, 'tid' => $tid, 'action' => $action]);
} else { // Must be adding an element.
    // We need to supply what element we want added to what page.
    $pageid = required_param('pageid', PARAM_INT);
    $element = new stdClass();
    $element->element = required_param('element', PARAM_ALPHA);
    $pageurl = new moodle_url('/blocks/coupon/view/templates/edit_element.php', ['tid' => $tid, 'element' => $element->element,
        'pageid' => $pageid, 'action' => $action]);
}

$context = $template->get_context();

// Set up the page.
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

// Additional page setup.
if ($template->get_context()->contextlevel == CONTEXT_SYSTEM) {
    $PAGE->navbar->add(get_string('managetemplates', 'block_coupon'),
        new moodle_url('/blocks/coupon/view/templates/manage_templates.php'));
}
$PAGE->navbar->add(get_string('edittemplate', 'block_coupon'), new moodle_url('/blocks/coupon/view/templates/edit.php',
    array('tid' => $tid)));
$PAGE->navbar->add(get_string('editelement', 'block_coupon'));

$mform = new \block_coupon\template\edit_element_form($pageurl, array('element' => $element));

// Check if they cancelled.
if ($mform->is_cancelled()) {
    $url = new moodle_url('/blocks/coupon/view/templates/edit.php', array('tid' => $tid));
    redirect($url);
}

if ($data = $mform->get_data()) {
    // Set the id, or page id depending on if we are editing an element, or adding a new one.
    if ($action == 'edit') {
        $data->id = $id;
        $data->pageid = $element->pageid;
    } else {
        $data->pageid = $pageid;
    }
    // Set the element variable.
    $data->element = $element->element;
    // Get an instance of the element class.
    if ($e = \block_coupon\template\element_factory::get_element_instance($data)) {
        $e->save_form_elements($data);

        // Trigger updated event.
        \block_coupon\event\template_updated::create_from_template($template)->trigger();
    }

    $url = new moodle_url('/blocks/coupon/view/templates/edit.php', array('tid' => $tid));
    redirect($url);
}

$renderer = $PAGE->get_renderer('block_coupon');
echo $OUTPUT->header();
echo html_writer::start_div('block-coupon-container');
echo html_writer::start_div();
echo $renderer->get_tabs($PAGE->context, 'cptpl', array('id' => $id));
echo html_writer::end_div();
$mform->display();
echo html_writer::end_div();
echo $OUTPUT->footer();
