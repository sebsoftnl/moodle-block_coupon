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
 * Coupon user form
 *
 * File         user.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\request;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\user
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        global $CFG;
        $mform = & $this->_form;

        list($instance, $user) = $this->_customdata;

        // Register element.
        $path = $CFG->dirroot . '/blocks/coupon/classes/forms/element/findcourses.php';
        \MoodleQuickForm::registerElementType('findcourses', $path, '\block_coupon\forms\element\findcourses');

        $mform->addElement('header', 'header', get_string('coupon:user:heading', 'block_coupon', $user));
        $mform->addElement('static', 'info', '', get_string('coupon:user:info', 'block_coupon', $user));

        // Select courses that can be accessed.
        $mform->addElement('findcourses', 'course', get_string('course'));
        $mform->addHelpButton('course', 'findcourses', 'block_coupon');
        $mform->addRule('course', null, 'required', null, 'client');

        // Now for some other options and settings...
        $mform->addElement('static', '_xother', '', get_string('othersettings', 'block_coupon') . '<hr/>');

        // Logo selection.
        $mform->addElement('static', '_logo', get_string('label:image', 'block_coupon'),
                get_string('forcelogo_exp', 'block_coupon'));
        $mform->addElement('advcheckbox', 'allowselectlogo', '',
                get_string('userconfig:allowselectlogo', 'block_coupon'));
        $mform->setDefault('allowselectlogo', 1);
        $mform->addElement('select', 'logo', get_string('label:forcelogo', 'block_coupon'),
                \block_coupon\logostorage::get_file_menu());
        $mform->hideIf('logo', 'allowselectlogo', 'checked');
        $mform->addHelpButton('logo', 'label:forcelogo', 'block_coupon');

        // Role selection.
        $mform->addElement('static', '_role', get_string('label:coupon_role', 'block_coupon'),
                get_string('forcerole_exp', 'block_coupon'));
        $mform->addElement('advcheckbox', 'allowselectrole', '',
                get_string('userconfig:allowselectrole', 'block_coupon'));
        $mform->setDefault('allowselectrole', 1);
        $roles = \block_coupon\helper::get_role_menu(null, true);
        $mform->addElement('select', 'role', get_string('label:forcerole', 'block_coupon'), $roles);
        $mform->hideIf('role', 'allowselectrole', 'checked');
        $mform->addHelpButton('role', 'label:forcerole', 'block_coupon');

        // PDF Generator selection.
        $mform->addElement('advcheckbox', 'allowselectseperatepdf',
                get_string('label:generate_pdfs', 'block_coupon'),
                get_string('userconfig:allowselectseperatepdf', 'block_coupon'));
        $mform->setDefault('allowselectseperatepdf', 0);
        $mform->addElement('advcheckbox', 'seperatepdfdefault', '',
                get_string('userconfig:seperatepdf:default', 'block_coupon'));
        $mform->setDefault('seperatepdfdefault', 0);
        $mform->disabledIf('seperatepdfdefault', 'allowselectseperatepdf', 'checked');
        $mform->addHelpButton('seperatepdfdefault', 'label:generate_pdfs', 'block_coupon');

        // QR Generator selection.
        $mform->addElement('advcheckbox', 'allowselectqr',
                get_string('label:renderqrcode', 'block_coupon'),
                get_string('userconfig:allowselectqr', 'block_coupon'));
        $mform->setDefault('allowselectqr', 1);
        $mform->addElement('advcheckbox', 'qrdefault', '',
                get_string('userconfig:renderqrcode:default', 'block_coupon'));
        $mform->setDefault('qrdefault', 0);
        $mform->disabledIf('qrdefault', 'allowselectqr', 'checked');
        $mform->addHelpButton('qrdefault', 'label:renderqrcode', 'block_coupon');

        // Enrolment period selection.
        $mform->addElement('advcheckbox', 'allowselectenrolperiod',
                get_string('label:enrolment_period', 'block_coupon'),
                get_string('userconfig:allowselectenrolperiod', 'block_coupon'));
        $mform->setDefault('allowselectenrolperiod', 1);

        $mform->addElement('duration', 'enrolperioddefault',
                get_string('label:enrolment_perioddefault', 'block_coupon'),
                array('size' => 40, 'optional' => false));
        $mform->setDefault('enrolperioddefault', '0');
        $mform->addHelpButton('enrolperioddefault', 'label:enrolment_period', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));

        // We CANNOT use set_data( this )) here, IN form definition() causes a rest of set values...
        // See https://tracker.moodle.org/browse/MDL-53889.
    }

    /**
     * Validation
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (empty($data['course'])) {
            $errors['course'] = get_string('required');
        }
        if (!(bool)$data['allowselectrole'] && empty($data['role'])) {
            $errors['role'] = get_string('required') . '<br/>' . get_string('forcerole_exp', 'block_coupon');
        }
        if (!(bool)$data['allowselectlogo'] && !isset($data['logo'])) {
            $errors['logo'] = get_string('required') . '<br/>' . get_string('forcelogo_exp', 'block_coupon');
        }
        return $errors;
    }

}
