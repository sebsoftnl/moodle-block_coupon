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
 * Mail template edit form
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

namespace block_coupon\forms\mailtemplates;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\mailtemplates\edit
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
        $mform = & $this->_form;

        list($this->instance) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('mailtemplates:title', 'block_coupon', $this->instance));

        // Name.
        $mform->addElement('text', 'name', get_string('name'));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', null, 'maxlength', 200, 'client');
        $mform->setType('name', PARAM_TEXT);

        // Subject.
        $mform->addElement('text', 'subject', get_string('subject'));
        $mform->addRule('subject', null, 'required', null, 'client');
        $mform->addRule('subject', null, 'maxlength', 200, 'client');
        $mform->setType('subject', PARAM_TEXT);

        // Editable email message.
        \block_coupon\emailtemplates::add_form_element($mform);
        $eopts = [
            'changeformat' => 0,
            'context' => null,
            'noclean' => 1,
            'trusttext' => 0,
            'enable_filemanagement' => false
        ];
        $mform->addElement('editor', 'body', get_string('label:email_body', 'block_coupon'), $eopts);
        $mform->setType('body', PARAM_RAW);
        $mform->addRule('body', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('body', 'label:email_body', 'block_coupon');

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
                $sql = 'SELECT id FROM {block_coupon_mailtemplatess} WHERE idnumber = ?';
                $params = [$data['idnumber']];
            } else {
                $sql = 'SELECT id FROM {block_coupon_mailtemplatess} WHERE idnumber = ? AND id <> ?';
                $params = [$data['idnumber'], $this->instance->id];
            }
            if (count($DB->get_fieldset_sql($sql, $params)) > 0) {
                $errors['idnumber'] = get_string('err:idnumber-not-unique', 'block_coupon');
            }
        }

        return $errors;
    }

}
