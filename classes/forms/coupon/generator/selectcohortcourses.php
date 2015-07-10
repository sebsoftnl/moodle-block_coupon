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
 * Cohort course selection form for coupon generator
 *
 * File         selectcohortcourses.php
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
use block_coupon\helper;
require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\generator\selectcohortcourses
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selectcohortcourses extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        global $CFG, $DB, $SESSION;

        $mform = & $this->_form;

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_coupon_cohort_courses')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);
        // Collect cohort records.
        $cohorts = $DB->get_records_list('cohort', 'id', $SESSION->generatoroptions->cohorts);

        // Now we'll show the cohorts one by one.
        foreach ($cohorts as $cohort) {

            // Header for the cohort.
            $mform->addElement('header', 'cohortsheader[]', $cohort->name);

            // Collect courses connected to cohort.
            $cohortcourses = helper::get_courses_by_cohort($cohort->id);

            // If we have connected courses we'll display them.
            if ($cohortcourses) {
                $headingstr = array();
                foreach ($cohortcourses as $course) {
                    $headingstr[] = $course->fullname;
                }
                $mform->addElement('static', 'connected_courses',
                        get_string('label:connected_courses', 'block_coupon'), implode('<br/>', $headingstr));
            } else {
                $mform->addElement('static', 'connected_courses[' . $cohort->id . '][]',
                        get_string('label:connected_courses', 'block_coupon'),
                        get_string('label:no_courses_connected', 'block_coupon'));
            }

            // Collect not connected courses.
            $notconnectedcourses = helper::get_unconnected_cohort_courses($cohort->id);

            // If we have not connected courses we'll display them.
            if ($notconnectedcourses) {
                $arrnotconnectedcourses = array();
                foreach ($notconnectedcourses as $notconnectedcourse) {
                    $arrnotconnectedcourses[$notconnectedcourse->id] = $notconnectedcourse->fullname;
                }
                $attributes = array('size' => min(20, count($arrnotconnectedcourses)));
                $selectconnectcourses = &$mform->addElement('select', 'connect_courses[' . $cohort->id . ']',
                        get_string('label:coupon_connect_course', 'block_coupon'), $arrnotconnectedcourses, $attributes);
                $mform->addHelpButton('connect_courses[' . $cohort->id . ']', 'label:coupon_connect_course', 'block_coupon');
                $selectconnectcourses->setMultiple(true);
            }

            // That's the end of the loop.
        }

        // Action buttons.
        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

}