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
 * Course coupon generator form (step 5)
 *
 * File         page5.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon;

use block_coupon\forms\baseform;
use block_coupon\helper;

/**
 * block_coupon\forms\coupon\course\page5
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csvrecips extends baseform {

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;

        list($this->generatoroptions) = $this->_customdata;

        $csvrecips = ['e-mail,gender,name'];
        foreach ($this->generatoroptions->csvrecipients as $recip) {
            $csvrecips[] = "{$recip->email},{$recip->gender},{$recip->name}";
        }
        $csvrecips = implode("\n", $csvrecips);

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));

        $mform->addElement('textarea', 'coupon_recipients',
                get_string("label:coupon_recipients", 'block_coupon'), 'rows="20" cols="50"');
        $mform->addRule('coupon_recipients', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('coupon_recipients', 'label:coupon_recipients_txt', 'block_coupon');
        $mform->setDefault('coupon_recipients', $csvrecips);
        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);
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
        $errors = parent::validation($data, $files);
        $delimiter = helper::get_delimiter($this->generatoroptions->csvdelimitername);
        $recipientserror = helper::validate_coupon_recipients($data['coupon_recipients'], $delimiter);
        if ($recipientserror !== true) {
            $errors['coupon_recipients'] = $recipientserror;
        }
        return $errors;
    }

}
