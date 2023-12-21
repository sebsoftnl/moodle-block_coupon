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
 * Enrolment extension coupon generator form (step 2)
 *
 * File         recips.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\extendenrolment;

use block_coupon\forms\baseform;

/**
 * block_coupon\forms\coupon\extendenrolment\page2
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recips extends baseform {

    /**
     * form definition
     */
    protected function definition() {
        $mform = & $this->_form;
        list($this->generatoroptions) = $this->_customdata;

        $mform->addElement('header', '_recipients', get_string('heading:extendenrolment', 'block_coupon'));

        if ($this->generatoroptions->extendusersrecipient == 'me') {
            // Set email_to variable.
            $usealternativeemail = 0;
            $alternativeemail = get_config('block_coupon', 'alternative_email');

            // Use alternative email address.
            $mform->addElement('checkbox', 'use_alternative_email',
                    get_string('label:use_alternative_email', 'block_coupon'));
            $mform->setType('use_alternative_email', PARAM_BOOL);
            $mform->setDefault('use_alternative_email', $usealternativeemail);

            // Email address to mail to.
            $mform->addElement('text', 'alternative_email',
                    get_string('label:alternative_email', 'block_coupon'), array('size' => 40));
            $mform->setType('alternative_email', PARAM_EMAIL);
            $mform->setDefault('alternative_email', $alternativeemail);
            $mform->addRule('alternative_email', get_string('error:invalid_email', 'block_coupon'), 'email', null);
            $mform->addHelpButton('alternative_email', 'label:alternative_email', 'block_coupon');
            $mform->hideIf('alternative_email', 'use_alternative_email', 'notchecked');

        } else {
            // Editable email message.
            $mailcontentdefault = get_string('coupon_mail_extend_content', 'block_coupon');

            \block_coupon\emailtemplates::add_form_element($mform);
            $mform->addElement('editor', 'email_body_manual',
                    get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
            $mform->setType('email_body_manual', PARAM_RAW);
            $mform->setDefault('email_body_manual', array('text' => $mailcontentdefault));
            $mform->addRule('email_body_manual', get_string('required'), 'required');
            $mform->addHelpButton('email_body_manual', 'label:email_body', 'block_coupon');
        }

        // Configurable enrolment time.
        $options = array('optional' => true);
        $mform->addElement('date_selector', 'date_send_coupons_manual',
                get_string('label:date_send_coupons', 'block_coupon'), $options);
        $mform->addHelpButton('date_send_coupons_manual', 'label:date_send_coupons', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);
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
