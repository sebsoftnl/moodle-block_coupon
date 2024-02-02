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
 * Edit the template settings.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');

require_once($CFG->dirroot . '/blocks/coupon/lib.php');

$tid = optional_param('tid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
if ($action) {
    $actionid = required_param('aid', PARAM_INT);
}
$confirm = optional_param('confirm', 0, PARAM_INT);

// Edit an existing template.
if ($tid) {
    // Create the template object.
    $template = $DB->get_record('block_coupon_templates', array('id' => $tid), '*', MUST_EXIST);
    $template = new block_coupon\template($template);
    // Set the context.
    $contextid = $template->get_contextid();
    // Set the page url.
    $pageurl = new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $tid]);
} else { // Adding a new template.
    // Need to supply the contextid.
    $contextid = required_param('contextid', PARAM_INT);
    // Set the page url.
    $pageurl = new moodle_url('/blocks/coupon/view/templates/edit.php', array('contextid' => $contextid));
}

$context = context::instance_by_id($contextid);
require_login();
$title = $SITE->fullname;

require_capability('block/coupon:administration', $context);

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

if ($context->contextlevel == CONTEXT_SYSTEM) {
    // We are managing a template - add some navigation.
    $PAGE->navbar->add(get_string('managetemplates', 'block_coupon'),
        new moodle_url('/blocks/coupon/view/templates/manage_templates.php'));
    if (!$tid) {
        $PAGE->navbar->add(get_string('edittemplate', 'block_coupon'));
    } else {
        $PAGE->navbar->add(get_string('edittemplate', 'block_coupon'),
            new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $tid]));
    }
}

// Flag to determine if we are deleting anything.
$deleting = false;

if ($tid) {
    if ($action && confirm_sesskey()) {
        switch ($action) {
            case 'pmoveup' :
                $template->move_item('page', $actionid, 'up');
                break;
            case 'pmovedown' :
                $template->move_item('page', $actionid, 'down');
                break;
            case 'emoveup' :
                $template->move_item('element', $actionid, 'up');
                break;
            case 'emovedown' :
                $template->move_item('element', $actionid, 'down');
                break;
            case 'addpage' :
                $template->add_page();
                $url = new \moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $tid]);
                redirect($url);
                break;
            case 'deletepage' :
                if (!empty($confirm)) { // Check they have confirmed the deletion.
                    $template->delete_page($actionid);
                    $url = new \moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $tid]);
                    redirect($url);
                } else {
                    // Set deletion flag to true.
                    $deleting = true;
                    // Create the message.
                    $message = get_string('deletepageconfirm', 'block_coupon');
                    // Create the link options.
                    $nourl = new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $tid]);
                    $yesurl = new moodle_url('/blocks/coupon/view/templates/edit.php',
                        array(
                            'tid' => $tid,
                            'action' => 'deletepage',
                            'aid' => $actionid,
                            'confirm' => 1,
                            'sesskey' => sesskey()
                        )
                    );
                }
                break;
            case 'deleteelement' :
                if (!empty($confirm)) { // Check they have confirmed the deletion.
                    $template->delete_element($actionid);
                } else {
                    // Set deletion flag to true.
                    $deleting = true;
                    // Create the message.
                    $message = get_string('deleteelementconfirm', 'block_coupon');
                    // Create the link options.
                    $nourl = new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $tid]);
                    $yesurl = new moodle_url('/blocks/coupon/view/templates/edit.php',
                        array(
                            'tid' => $tid,
                            'action' => 'deleteelement',
                            'aid' => $actionid,
                            'confirm' => 1,
                            'sesskey' => sesskey()
                        )
                    );
                }
                break;
        }
    }
}

// Check if we are deleting either a page or an element.
if ($deleting) {
    // Show a confirmation page.
    $PAGE->navbar->add(get_string('deleteconfirm', 'block_coupon'));
    echo $OUTPUT->header();
    echo $OUTPUT->confirm($message, $yesurl, $nourl);
    echo $OUTPUT->footer();
    exit();
}

if ($tid) {
    $mform = new \block_coupon\template\edit_form($pageurl, ['tid' => $tid]);
    // Set the name for the form.
    $mform->set_data(array('name' => $template->get_name()));
} else {
    $mform = new \block_coupon\template\edit_form($pageurl);
}

if ($data = $mform->get_data()) {
    // If there is no id, then we are creating a template.
    if (!$tid) {
        $template = \block_coupon\template::create($data->name, $contextid);

        // Create a page for this template.
        $pageid = $template->add_page();

        // Associate all the data from the form to the newly created page.
        $width = 'pagewidth_' . $pageid;
        $height = 'pageheight_' . $pageid;
        $leftmargin = 'pageleftmargin_' . $pageid;
        $rightmargin = 'pagerightmargin_' . $pageid;
        $rightmargin = 'pagerightmargin_' . $pageid;

        $data->$width = $data->pagewidth_0;
        $data->$height = $data->pageheight_0;
        $data->$leftmargin = $data->pageleftmargin_0;
        $data->$rightmargin = $data->pagerightmargin_0;

        // We may also have clicked to add an element, so these need changing as well.
        if (isset($data->element_0) && isset($data->addelement_0)) {
            $element = 'element_' . $pageid;
            $addelement = 'addelement_' . $pageid;
            $data->$element = $data->element_0;
            $data->$addelement = $data->addelement_0;

            // Need to remove the temporary element and add element placeholders so we
            // don't try add an element to the wrong page.
            unset($data->element_0);
            unset($data->addelement_0);
        }
    }

    if ($tid) {
        // Save any data for the template.
        $template->save($data);
    }

    // Save any page data.
    $template->save_page($data);

    // Loop through the data.
    foreach ($data as $key => $value) {
        // Check if they chose to add an element to a page.
        if (strpos($key, 'addelement_') !== false) {
            // Get the page id.
            $pageid = str_replace('addelement_', '', $key);
            // Get the element.
            $element = "element_" . $pageid;
            $element = $data->$element;
            // Create the URL to redirect to to add this element.
            $params = array();
            $params['tid'] = $template->get_id();
            $params['action'] = 'add';
            $params['element'] = $element;
            $params['pageid'] = $pageid;
            $url = new moodle_url('/blocks/coupon/view/templates/edit_element.php', $params);
            redirect($url);
        }
    }

    // Check if we want to preview this custom certificate.
    if (!empty($data->previewbtn)) {
        // Create a fake coupon.
        $coupons = block_coupon\helper::create_fake_coupons(1);
        $template->generate_pdf($coupons, true);
        exit();
    }

    // Redirect to the editing page to show form with recent updates.
    $url = new moodle_url('/blocks/coupon/view/templates/edit.php', ['tid' => $template->get_id()]);
    redirect($url);
}

$renderer = $PAGE->get_renderer('block_coupon');
echo $OUTPUT->header();
echo html_writer::start_div('block-coupon-container');
echo html_writer::start_div();
echo $renderer->get_tabs($PAGE->context, 'cptpl');
echo html_writer::end_div();
$mform->display();
echo html_writer::end_div();
echo $OUTPUT->footer();
