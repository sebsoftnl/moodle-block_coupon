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
 * Enrolment extension coupon generator form (step 1)
 *
 * File         extendenrolment.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\extendenrolment;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_coupon\helper;

/**
 * block_coupon\forms\coupon\extendenrolment\page1
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page1 extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;

        $multiselect = true;
        if (isset($this->_customdata['coursemultiselect'])) {
            $multiselect = (bool)$this->_customdata['coursemultiselect'];
        }

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_coupon_course')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);
        $mform->addElement('header', 'header', get_string('heading:input_course', 'block_coupon'));

        // First we'll get some useful info.
        $courses = helper::get_visible_courses();

        // And create data for multiselect.
        $arrcoursesselect = array();
        foreach ($courses as $course) {
            $arrcoursesselect[$course->id] = $course->fullname;
        }

        $attributes = array('size' => min(20, count($arrcoursesselect)));
        // Course id.
        $selectcourse = &$mform->addElement('select', 'coupon_courses',
                get_string('label:coupon_courses', 'block_coupon'), $arrcoursesselect, $attributes);
        $selectcourse->setMultiple($multiselect);
        $mform->addRule('coupon_courses', get_string('error:required', 'block_coupon'), 'required', null, 'client');
        $mform->addHelpButton('coupon_courses', 'label:coupon_courses', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

}
