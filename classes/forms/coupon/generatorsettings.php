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

namespace block_coupon\forms\coupon;

use block_coupon\forms\baseform;
use block_coupon\helper;

/**
 * block_coupon\forms\coupon\course\page1
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generatorsettings extends baseform {

    /**
     * Get reference to database
     * @return \moodle_database
     */
    protected function db() {
        global $DB;
        return $DB;
    }

    /**
     * form definition
     */
    public function definition() {
        global $CFG;
        $mform = & $this->_form;

        // Register element.
        $path = $CFG->dirroot . '/blocks/coupon/classes/forms/element/durationext.php';
        \MoodleQuickForm::registerElementType('durationext', $path, '\block_coupon\forms\element\durationext');

        list($this->generatoroptions) = $this->_customdata;

        // Nasty++.
        \MoodleQuickForm::registerRule('positiveint', 'regex', '/(^\d\d*$)/');

        $mform->addElement('header', 'header', get_string('generatorsettings', 'block_coupon'));

        // Add custom batchid.
        $mform->addElement('text', 'batchid', get_string('label:batchid', 'block_coupon'), ['maxlength' => 255]);
        $mform->setType('batchid', PARAM_TEXT);
        $mform->addHelpButton('batchid', 'label:batchid', 'block_coupon');
        $mform->addRule('batchid', null, 'maxlength', 255, 'client');

        // What i wish here:
        // - static code | generated code.
        // I *think* I can pull this off AFTER the "campaign type".
        // When a campaign type does not support "1 code; N useages", we have a different form.

        // Add generator limiting.
        helper::add_generator_flags_options($mform);

        // Add custom code size.
        $mform->addElement('text', 'codesize', get_string('label:coupon_code_length', 'block_coupon'), ['maxlength' => 64]);
        $mform->setType('codesize', PARAM_INT);
        $mform->addHelpButton('codesize', 'label:coupon_code_length', 'block_coupon');
        $mform->addRule('codesize', null, 'required', null, 'client');
        $mform->addRule('codesize', null, 'maxlength', 64, 'client');
        $mform->addRule('codesize', get_string('invalidnum', 'error'), 'positiveint', null, 'client');
        $mform->setDefault('codesize', get_config('block_coupon', 'coupon_code_length'));

        // Generate codesonly checkbox.
        $mform->addElement('advcheckbox', 'generatecodesonly', get_string('label:generatecodesonly', 'block_coupon'));
        $mform->addHelpButton('generatecodesonly', 'label:generatecodesonly', 'block_coupon');

        // Settings that influence expiry.
        helper::add_expiry_elements($mform);

        // Configurable redirect url.
        $mform->addElement('text', 'redirect_url', get_string('label:redirect_url', 'block_coupon'));
        $mform->setType('redirect_url', PARAM_LOCALURL);
        $mform->setDefault('redirect_url', $CFG->wwwroot . '/my');
        $mform->addRule('redirect_url', get_string('required'), 'required');
        $mform->addHelpButton('redirect_url', 'label:redirect_url', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);

        $data = [
            'batchid' => $this->generatoroptions->batchid,
            'codesize' => $this->generatoroptions->codesize,
            'generatecodesonly' => $this->generatoroptions->generatecodesonly ? 1 : 0,
            'redirecturl' => $this->generatoroptions->redirecturl,
            'flags' => [
                1 => (($this->generatoroptions->generatorflags & 1) == 1) ? 1 : 0,
                2 => (($this->generatoroptions->generatorflags & 2) == 2) ? 1 : 0,
                4 => (($this->generatoroptions->generatorflags & 4) == 4) ? 1 : 0,
            ],
            'expirationmethod' => $this->generatoroptions->expirymethod,
            'expiresin' => $this->generatoroptions->expiresin,
            'expiresat' => $this->generatoroptions->expiresat
        ];

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
        global $DB;
        // Make sure batch id is unique if provided.
        $err = parent::validation($data, $files);
        if (!empty($data['batchid']) && $DB->record_exists('block_coupon', ['batchid' => $data['batchid']])) {
            $err['batchid'] = get_string('err:batchid', 'block_coupon');
        }
        helper::validate_generator_flags($data, $err);
        helper::validate_codesize($data, $err);
        return $err;
    }

}
