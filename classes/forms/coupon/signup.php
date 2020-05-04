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
 * Coupon signup form
 *
 * File         signup.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/login/signup_form.php');

/**
 * block_coupon\forms\coupon\signup
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class signup extends \login_signup_form {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        // Add coupon submission code entry.
        $mform->addElement('text', 'submissioncode', get_string('label:coupon_code', 'block_coupon'));
        $mform->addRule('submissioncode', get_string('error:required', 'block_coupon'), 'required', null, 'client');
        $mform->addRule('submissioncode', get_string('error:required', 'block_coupon'), 'required', null, 'server');
        $mform->setType('submissioncode', PARAM_ALPHANUM);
        $mform->addHelpButton('submissioncode', 'label:coupon_code', 'block_coupon');
        parent::definition();
    }

    /**
     * Validate form input
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        $conditions = array(
            'submission_code' => $data['submissioncode'],
            'claimed' => 0,
        );
        $coupon = $DB->get_record('block_coupon', $conditions);
        if (empty($coupon)) {
            $errors['submissioncode'] = get_string('error:invalid_coupon_code', 'block_coupon');
        } else if (!is_null($coupon->userid) && $coupon->typ != \block_coupon\coupon\generatoroptions::ENROLEXTENSION) {
            $errors['submissioncode'] = get_string('error:coupon_already_used', 'block_coupon');
        }

        return $errors;
    }
}
