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
 * Form implementation to let a user input a coupon code.
 *
 * File         validator.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\validator
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class validator extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;
        $mform->addElement('header', 'header', get_string('heading:input_coupon', 'block_coupon'));
        // All we need is the coupon code.
        $mform->addElement('text', 'coupon_code', get_string('label:coupon_code', 'block_coupon'));
        $mform->addRule('coupon_code', get_string('error:required', 'block_coupon'), 'required', null, 'client');
        $mform->addRule('coupon_code', get_string('error:required', 'block_coupon'), 'required', null, 'server');
        $mform->setType('coupon_code', PARAM_ALPHANUM);
        $mform->addHelpButton('coupon_code', 'label:coupon_code', 'block_coupon');

        $this->add_action_buttons(false, get_string('button:submit_coupon_code', 'block_coupon'));
    }

    /**
     * Perform validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        global $USER;
        $errors = parent::validation($data, $files);

        // Get type processor.
        $typeproc = \block_coupon\coupon\typebase::get_type_instance($data['coupon_code']);
        try {
            // Assert not yet used.
            $typeproc->assert_not_claimed();
            // Assert specialized.
            $typeproc->assert_internal_checks($USER->id);
        } catch (Exception $ex) {
            $errors['coupon_code'] = $ex->getMessage();
        }

        return $errors;
    }

    /**
     * Override form identifier. This is to fix namespace issues for Moodle < 2.9
     * @return string
     */
    protected function get_form_identifier() {
        $class = get_class($this);
        return preg_replace('/[^a-z0-9_]/i', '_', $class);
    }

}
