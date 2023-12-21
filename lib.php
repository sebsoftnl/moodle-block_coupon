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
 * library for coupon block.
 *
 * File         lib.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Add items to course navigation
 * @param navigation_node $parentnode
 * @param stdClass $course
 * @param context_course $context
 */
function block_coupon_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    global $CFG;
    if (!has_capability('block/coupon:extendenrolments', $context)) {
        return false;
    }
    $biid = \block_coupon\helper::find_block_instance_id();
    if (empty($biid)) {
        return;
    }
    $icon = new \pix_icon('coupons', get_string('coupon:extendenrol', 'block_coupon'), 'block_coupon');
    $conditions = array('cid' => $course->id, 'id' => $biid);
    $action = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/extendenrolment.php', $conditions);
    $parentnode->add(get_string('coupon:extendenrol', 'block_coupon'), $action, navigation_node::TYPE_CUSTOM,
            get_string('coupon:extendenrol', 'block_coupon'), 'cpextendenrol', $icon);
}

/**
 * Send a file
 *
 * @param \stdClass $course
 * @param \stdClass $birecordorcm
 * @param \context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 */
function block_coupon_pluginfile($course, $birecordorcm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    $allowed = array('content', 'logos', 'image');
    if (!in_array($filearea, $allowed)) {
        send_file_not_found();
    }
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }
    require_login();

    // Get file.
    $fs = get_file_storage();
    $filename = array_pop($args);
    $itemid = array_shift($args);
    if (!is_numeric($itemid)) {
        $filepath = $itemid . ($args ? '/' . implode('/', $args) . '/' : '/');
        $itemid = 0;
    } else {
        $filepath = ($args ? '/' . implode('/', $args) . '/' : '/');
    }
    $file = $fs->get_file($context->id, 'block_coupon', $filearea, $itemid, $filepath, $filename);
    if ($file === false || $file->is_directory()) {
        send_file_not_found();
    }

    \core\session\manager::write_close();
    send_stored_file($file, 60 * 60, 0, $forcedownload, $options);
}

/**
 * Handles editing the 'name' of the element in a list.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param string $newvalue
 * @return \core\output\inplace_editable
 */
function block_coupon_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $PAGE;

    if ($itemtype === 'elementname') {
        $element = $DB->get_record('block_coupon_elements', array('id' => $itemid), '*', MUST_EXIST);
        $page = $DB->get_record('block_coupon_pages', array('id' => $element->pageid), '*', MUST_EXIST);
        $template = $DB->get_record('block_coupon_templates', array('id' => $page->templateid), '*', MUST_EXIST);

        // Set the template object.
        $template = new \block_coupon\template($template);
        // Perform checks.
        $PAGE->set_context(context_system::instance());
        require_login();
        // Make sure the user has the required capabilities.
        $template->require_manage();

        // Clean input and update the record.
        $updateelement = new stdClass();
        $updateelement->id = $element->id;
        $updateelement->name = clean_param($newvalue, PARAM_TEXT);
        $DB->update_record('block_coupon_elements', $updateelement);

        return new \core\output\inplace_editable('block_coupon', 'elementname', $element->id, true,
            $updateelement->name, $updateelement->name);
    }
}

/**
 * Force custom language for current session.
 *
 * @param string $language
 * @return bool
 */
function block_coupon_force_current_language($language): bool {
    global $USER;

    $forced = false;
    if (empty($language)) {
        return $forced;
    }

    $activelangs = get_string_manager()->get_list_of_translations();
    $userlang = $USER->lang ?? current_language();

    if (array_key_exists($language, $activelangs) && $language != $userlang) {
        force_current_language($language);
        $forced = true;
    }

    return $forced;
}
