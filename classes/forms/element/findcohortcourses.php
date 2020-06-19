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
 * Courses selector field.
 *
 * File         findcohortcourses.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_coupon\forms\element;

defined('MOODLE_INTERNAL') || die();

use MoodleQuickForm_autocomplete;
use block_coupon\helper;
global $CFG;

require_once($CFG->libdir . '/form/autocomplete.php');

/**
 * Form field type for choosing a course.
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class findcohortcourses extends findcourses {

    /**
     * Cohort ID we're looking up potential courses for.
     * @var int
     */
    private $cohortid = null;

    /**
     * Constructor.
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param int $cohortid cohort we're searching non connected courses for
     * @param array $options Options to control the element's display
     *                       Valid options are:
     *                       - multiple bool Whether or not the field accepts more than one values.
     */
    public function __construct($elementname = null, $elementlabel = null, $cohortid = null, $options = array()) {
        $this->cohortid = $cohortid;
        $validattributes = array(
            'ajax' => 'block_coupon/findcohortcourses',
            'multiple' => true
        );
        if (!empty($options['multiple'])) {
            $validattributes['multiple'] = 'multiple';
        }
        if (isset($options['onlyvisible'])) {
            $this->onlyvisible = (bool)$options['onlyvisible'];
        }
        $validattributes['tags'] = false;
        $validattributes['casesensitive'] = false;
        $validattributes['placeholder'] = get_string('findcourses:placeholder', 'block_coupon');
        $validattributes['noselectionstring'] = get_string('findcohortcourses:noselectionstring', 'block_coupon');
        $validattributes['showsuggestions'] = true;
        $validattributes['data-cohortid'] = $this->cohortid;
        MoodleQuickForm_autocomplete::__construct($elementname, $elementlabel, array(), $validattributes);
    }

    /**
     * Set the value of this element.
     *
     * @param  string|array $value The value to set.
     * @return boolean
     */
    public function setValue($value) { // @codingStandardsIgnoreLine Can't change parent behaviour.
        // The following lines SEEM to fix the issues around the autocomplete...
        // When e.g. postback of form introduces a server side validation error.
        // The result is that when this method has been called before, selection is reset to NOTHING.
        // See https://tracker.moodle.org/browse/MDL-53889 among others.
        // The autocomplete, is must say, is VERY poorly developed and not properly tested.
        if ($this->selectedset) {
            return;
        }
        $this->selectedset = true;

        $values = (array) $value;
        $ids = array();
        foreach ($values as $onevalue) {
            if (!empty($onevalue) && (!$this->optionExists($onevalue)) &&
                    ($onevalue !== '_qf__force_multiselect_submission')) {
                array_push($ids, $onevalue);
            }
        }
        if (empty($ids)) {
            return;
        }
        // Logic here is simulating API.
        $toselect = array();
        $courses = $this->load_courses();
        foreach ($courses as $id => $coursefullname) {
            $optionname = $coursefullname;
            $this->addOption($optionname, $id, ['selected' => 'selected']);
            array_push($toselect, $id);
        }
        $rs = $this->setSelected($toselect);
        return $rs;
    }

    /**
     * Load courses based on cohorot setting.
     *
     * @return array
     */
    private function load_courses() {
        // Collect not connected courses.
        $unconnectedcourses = helper::get_unconnected_cohort_courses($this->cohortid);
        // If we have not connected courses we'll display them.
        $courses = [];
        if ($unconnectedcourses) {
            foreach ($unconnectedcourses as $course) {
                $courses[$course->id] = $course->fullname;
            }
        }
        return $courses;
    }

}
