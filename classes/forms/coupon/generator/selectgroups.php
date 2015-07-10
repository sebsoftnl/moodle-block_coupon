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
 * Groups selector for coupon generator
 *
 * File         selectgroups.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\generator;
require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\generator\selectgroups
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selectgroups extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        global $CFG, $DB, $SESSION;

        $mform = & $this->_form;

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_coupon_course_groups')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);

        $mform->addElement('header', 'groupsheader', get_string('heading:input_groups', 'block_coupon'));

        // Display which course we selected.
        $groupoptions = array();
        foreach ($SESSION->generatoroptions->courses as $courseid) {

            // Collect data.
            if (!$course = $DB->get_record('course', array('id' => $courseid))) {
                print_error('error:course-not-found', 'block_coupon');
            }
            $groups = $DB->get_records("groups", array('courseid' => $courseid));
            if (empty($groups)) {
                continue;
            }

            // Build up groups.
            if (!isset($groupoptions[$course->fullname])) {
                $groupoptions[$course->fullname] = array();
            }
            foreach ($groups as $group) {
                $groupoptions[$course->fullname][$group->id] = $group->name;
            }
        }

        if (!empty($groupoptions)) {
            $groupselement = &$mform->addElement('selectgroups', 'coupon_groups',
                    get_string('label:coupon_groups', 'block_coupon'), $groupoptions);
            $mform->addHelpButton('coupon_groups', 'label:coupon_groups', 'block_coupon');
            $groupselement->setMultiple(true);
            // Shouldn't happen cause it'll just skip this step if no groups are connected.
        } else {
            $groupselement = &$mform->addElement('static', 'coupon_groups', '',
                    get_string('label:no_groups_selected', 'block_coupon'));
        }

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

}