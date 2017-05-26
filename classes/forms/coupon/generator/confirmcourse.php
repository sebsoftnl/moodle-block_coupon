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
 * Coupon generator course confirmation form
 *
 * File         confirmcourse.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\generator;

defined('MOODLE_INTERNAL') || die();

use block_coupon\helper;
require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\generator\confirmcourse
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirmcourse extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        global $CFG, $DB, $SESSION;
        $mform = & $this->_form;

        // Add standard form elements.
        helper::std_coupon_add_default_confirm_form_elements($mform, 'course');

        // Settings that apply for both csv and amount.
        $mform->addElement('header', 'lastSettings', get_string('heading:general_settings', 'block_coupon'));

        // Configurable redirect url.
        $mform->addElement('text', 'redirect_url', get_string('label:redirect_url', 'block_coupon'));
        $mform->setType('redirect_url', PARAM_RAW);
        $mform->setDefault('redirect_url', $CFG->wwwroot . '/my');
        $mform->addRule('redirect_url', get_string('required'), 'required');
        $mform->addHelpButton('redirect_url', 'label:redirect_url', 'block_coupon');

        // Configurable enrolment time.
        $mform->addElement('duration', 'enrolment_period',
                get_string('label:enrolment_period', 'block_coupon'), array('size' => 40, 'optional' => true));
        $mform->setDefault('enrolment_period', '0');
        $mform->addHelpButton('enrolment_period', 'label:enrolment_period', 'block_coupon');

        // Course fullname.
        list($cinsql, $cparams) = $DB->get_in_or_equal($SESSION->generatoroptions->courses);
        $courses = implode('<br/>', $DB->get_fieldset_select('course', 'fullname', 'id ' . $cinsql, $cparams));
        $mform->addElement('static', 'coupon_courses', get_string('label:selected_courses', 'block_coupon'), $courses);

        // Selected groups.
        if (!empty($SESSION->generatoroptions->groups)) {
            list($ginsql, $gparams) = $DB->get_in_or_equal($SESSION->generatoroptions->groups);
            $groups = implode('<br/>', $DB->get_fieldset_select('groups', 'name', 'id ' . $ginsql, $gparams));
            $mform->addElement('static', 'coupon_groups', get_string('label:selected_groups', 'block_coupon'), $groups);
        }

        // All elements added, add the custom js function and submit buttons.
        $mform->addElement('html', "
            <script type='text/javascript'>
            window.onload=function(){
                if (document.getElementById('id_showform_csv').checked == true) {
                    showHide('csv');
                } else if (document.getElementById('id_showform_amount').checked == true) {
                    showHide('amount');
                } else {
                    showHide('manual');
                }
            }

            function showHide(fieldValue) {

                switch(fieldValue) {

                    case 'csv':
                        document.getElementById('id_amountForm').style.display='none';
                        document.getElementById('id_manualForm').style.display='none';
                        break;
                    case 'amount':
                        document.getElementById('id_csvForm').style.display='none';
                        document.getElementById('id_manualForm').style.display='none';
                        break;
                    case 'manual':
                        document.getElementById('id_csvForm').style.display='none';
                        document.getElementById('id_amountForm').style.display='none';
                        break;
                }

                document.getElementById('id_' + fieldValue + 'Form').style.display='block';
            }
            </script>
        ");

        // Submit button.
        $this->add_action_buttons(true, get_string('button:save', 'block_coupon'));
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

        // Set which fields to validate, depending on form used.
        if ($data['showform'] == 'csv' || $data['showform'] == 'manual') {
            $data2validate = array(
                'email_body' => $data['email_body'],
                'date_send_coupons' => $data['date_send_coupons']
            );
        } else {
            $data2validate = array(
                'coupon_amount' => $data['coupon_amount'],
                'alternative_email' => $data['alternative_email']
            );
        }
        $data2validate['redirect_url'] = $data['redirect_url'];
        $data2validate['enrolment_period'] = $data['enrolment_period'];

        // Validate.
        $errors = parent::validation($data2validate, $files);

        // Custom validate.
        if ($data['showform'] == 'amount') {
            // Max amount of coupons.
            $maxcouponsamount = get_config('block_coupon', 'max_coupons');
            if ($data['coupon_amount'] > $maxcouponsamount || $data['coupon_amount'] < 1) {
                $errors['coupon_amount'] = get_string('error:coupon_amount_too_high',
                        'block_coupon', array('min' => '0', 'max' => $maxcouponsamount));
            }
            // Alternative email required if use_alternative_email is checked.
            if (isset($data['use_alternative_email']) && empty($data['alternative_email'])) {
                $errors['alternative_email'] = get_string('error:alternative_email_required', 'block_coupon');
            }
        } else if ($data['showform'] == 'csv') {
            $csvcontent = $this->get_file_content('coupon_recipients');
            if (!$csvcontent || empty($csvcontent)) {
                $errors['coupon_recipients'] = get_string('required');
            }
        } else {
            $validationresult = helper::validate_coupon_recipients($data['coupon_recipients_manual']);
            if ($validationresult !== true) {
                $errors['coupon_recipients_manual'] = $validationresult;
            }
        }

        return $errors;
    }

    /**
     * Get content of uploaded file.
     *
     * @param string $elname name of file upload element
     * @return string|bool false in case of failure, string if ok
     */
    public function get_file_content($elname) {
        global $USER;

        $element = $this->_form->getElement($elname);
        if ($element instanceof \MoodleQuickForm_filepicker || $element instanceof \MoodleQuickForm_filemanager) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $fs = get_file_storage();
            $context = \context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                return false;
            }
            $file = reset($files);

            return $file->get_content();
        } else if (isset($_FILES[$elname])) {
            return file_get_contents($_FILES[$elname]['tmp_name']);
        }

        return false;
    }

}