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
 * Course grouping edit form
 *
 * File         edit.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coursegrouping;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coursegrouping\edit
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit extends \moodleform {

    /**
     * @var \stdClass
     */
    protected $instance;

    /**
     * form definition
     */
    public function definition() {
        global $CFG;
        $mform = & $this->_form;

        list($this->instance) = $this->_customdata;

        // Register element.
        $path = $CFG->dirroot . '/blocks/coupon/classes/forms/element/findcourses.php';
        \MoodleQuickForm::registerElementType('findcourses', $path, '\block_coupon\forms\element\findcourses');

        $mform->addElement('header', 'header', get_string('coupon:coursegrouping:heading', 'block_coupon', $this->instance));

        // Name.
        $mform->addElement('text', 'name', get_string('name'));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        // ID Number.
        $mform->addElement('text', 'idnumber', get_string('idnumber'));
        $mform->setType('idnumber', PARAM_TEXT);

        // Max amount.
        $mform->addElement('text', 'maxamount', get_string('numcourses', 'block_coupon'));
        $mform->setType('maxamount', PARAM_INT);
        $mform->setDefault('maxamount', 1);
        $mform->addRule('maxamount', null, 'required', null, 'client');
        $mform->addRule('maxamount', null, 'numeric', null, 'client');

        // Select courses that can be accessed.
        $select = $mform->addElement('findcourses', 'course', get_string('course'));
        $mform->addHelpButton('course', 'findcourses', 'block_coupon');
        $mform->addRule('course', null, 'required', null, 'client');
        $select->setMultiple(true);

        $this->add_action_buttons(true);

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
        global $DB;
        $errors = parent::validation($data, $files);

        if (!empty($data['idnumber'])) {
            // Check uniqueness.
            if (empty($this->instance->id)) {
                $sql = 'SELECT id FROM {block_coupon_coursegroupings} WHERE idnumber = ?';
                $params = [$data['idnumber']];
            } else {
                $sql = 'SELECT id FROM {block_coupon_coursegroupings} WHERE idnumber = ? AND id <> ?';
                $params = [$data['idnumber'], $this->instance->id];
            }
            if (count($DB->get_fieldset_sql($sql, $params)) > 0) {
                $errors['idnumber'] = get_string('err:idnumber-not-unique', 'block_coupon');
            }
        }

        return $errors;
    }

}
