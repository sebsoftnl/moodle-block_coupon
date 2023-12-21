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
 * Course coupon generator form (step 1)
 *
 * File         page1.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\course;

use block_coupon\helper;
use block_coupon\forms\baseform;

/**
 * block_coupon\forms\coupon\course\page1
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursevars extends baseform {

    /**
     * form definition
     */
    public function definition() {
        global $CFG;
        $mform = & $this->_form;

        // Register element.
        $path = $CFG->dirroot . '/blocks/coupon/classes/forms/element/findcourses.php';
        \MoodleQuickForm::registerElementType('findcourses', $path, '\block_coupon\forms\element\findcourses');

        list($this->generatoroptions) = $this->_customdata;

        $mform->addElement('header', 'header1', get_string('heading:courseandvars', 'block_coupon'));

        // Select course(s).
        $multiselect = true;
        if (!empty($this->_customdata['coursemultiselect'])) {
            $multiselect = (bool)$this->_customdata['coursemultiselect'];
        }
        $options = ['multiple' => $multiselect, 'onlyvisible' => true];
        $mform->addElement('findcourses', 'coupon_courses',
                get_string('label:coupon_courses', 'block_coupon'), $options);
        $mform->addRule('coupon_courses', get_string('error:required', 'block_coupon'), 'required', null, 'client');
        $mform->addHelpButton('coupon_courses', 'label:coupon_courses', 'block_coupon');

        // Select role(s).
        $roles = helper::get_role_menu(null, true);
        $attributes = [];
        // Role id.
        $selectrole = &$mform->addElement('select', 'coupon_role',
                get_string('label:coupon_role', 'block_coupon'), $roles, $attributes);
        $selectrole->setMultiple(false);
        $mform->setDefault('coupon_role', helper::get_default_coupon_role()->id);
        $mform->addHelpButton('coupon_role', 'label:coupon_role', 'block_coupon');

        // Configurable enrolment time.
        $mform->addElement('duration', 'enrolment_period',
                get_string('label:enrolment_period', 'block_coupon'), array('size' => 40, 'optional' => true));
        $mform->setDefault('enrolment_period', '0');
        $mform->addHelpButton('enrolment_period', 'label:enrolment_period', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), false);

        // Set data.
        $data = [];
        if (!empty($this->generatoroptions->courses)) {
            $data['coupon_courses'] = $multiselect ? $this->generatoroptions->courses : reset($this->generatoroptions->courses);
        }
        $data['roleid'] = $this->generatoroptions->roleid ?? helper::get_default_coupon_role()->id;
        $data['enrolperiod'] = $this->generatoroptions->enrolperiod ?? null;
        $this->set_data($data);
    }

    /**
     * Validate input
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $err = parent::validation($data, $files);
        return $err;
    }

}
