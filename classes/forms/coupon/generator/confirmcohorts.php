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
 * Coupon generator cohorts confirmation form
 *
 * File         confirmcohorts.php
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
 * block_coupon\forms\coupon\generator\confirmcohorts
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirmcohorts extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        global $DB, $SESSION;
        $mform = & $this->_form;

        // Add standard form elements.
        helper::std_coupon_add_default_confirm_form_elements($mform, 'cohort');

        // Collect cohort records.
        $cohorts = $DB->get_records_list('cohort', 'id', $SESSION->generatoroptions->cohorts);

        // Cohorts to add.
        foreach ($cohorts as $cohort) {
            $mform->addElement('header', 'cohortsheader[]', $cohort->name);
            // Fetch the courses that are connected to this cohort.
            if ($cohortcourses = helper::get_courses_by_cohort($cohort->id)) {
                $headingstr = array();
                foreach ($cohortcourses as $course) {
                    $headingstr[] = $course->fullname;
                }
                $mform->addElement('static', 'connected_courses',
                        get_string('label:connected_courses', 'block_coupon'), implode('<br/>', $headingstr));
            } else {
                $mform->addElement('static', 'connected_courses[' . $cohort->id . ']',
                        get_string('label:connected_courses', 'block_coupon'),
                        get_string('label:no_courses_connected', 'block_coupon'));
            }
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