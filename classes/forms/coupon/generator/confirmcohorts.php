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

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_coupon_confirm')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);

        // Determine which type of settings we'll use.
        $radioarray = array();
        $radioarray[] = & $mform->createElement('radio', 'showform', '',
                get_string('showform-amount', 'block_coupon'), 'amount', array('onchange' => 'showHide(this.value)'));
        $radioarray[] = & $mform->createElement('radio', 'showform', '',
                get_string('showform-csv', 'block_coupon'), 'csv', array('onchange' => 'showHide(this.value)'));
        $radioarray[] = & $mform->createElement('radio', 'showform', '',
                get_string('showform-manual', 'block_coupon'), 'manual', array('onchange' => 'showHide(this.value)'));
        $mform->addGroup($radioarray, 'radioar', get_string('label:showform', 'block_coupon'), array('<br/>'), false);
        $mform->setDefault('showform', 'amount');

        // Send coupons based on CSV upload.
        $mform->addElement('header', 'csvForm', get_string('heading:csvForm', 'block_coupon'));

        // Filepicker.
        $urldownloadcsv = new \moodle_url('/blocks/coupon/sample.csv');
        $mform->addElement('filepicker', 'coupon_recipients',
                get_string('label:coupon_recipients', 'block_coupon'), null, array('accepted_types' => 'csv'));
        $mform->addHelpButton('coupon_recipients', 'label:coupon_recipients', 'block_coupon');
        $mform->addElement('static', 'coupon_recipients_desc', '', get_string('coupon_recipients_desc', 'block_coupon'));
        $mform->addElement('static', 'sample_csv', '', '<a href="' . $urldownloadcsv
                . '" target="_blank">' . get_string('download-sample-csv', 'block_coupon') . '</a>');

        // Editable email message.
        $mform->addElement('editor', 'email_body', get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
        $mform->setType('email_body', PARAM_RAW);
        $mform->setDefault('email_body', array('text' => get_string('coupon_mail_csv_content_cohorts', 'block_coupon')));
        $mform->addRule('email_body', get_string('required'), 'required');
        $mform->addHelpButton('email_body', 'label:email_body', 'block_coupon');

        // Configurable enrolment time.
        $mform->addElement('date_selector', 'date_send_coupons', get_string('label:date_send_coupons', 'block_coupon'));
        $mform->addRule('date_send_coupons', get_string('required'), 'required');
        $mform->addHelpButton('date_send_coupons', 'label:date_send_coupons', 'block_coupon');

        // Send coupons based on CSV upload.
        $mform->addElement('header', 'manualForm', get_string('heading:manualForm', 'block_coupon'));

        // Textarea recipients.
        $mform->addElement('textarea', 'coupon_recipients_manual',
                get_string("label:coupon_recipients", 'block_coupon'), 'rows="20" cols="50"');
        $mform->addRule('coupon_recipients_manual', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('coupon_recipients_manual', 'label:coupon_recipients_txt', 'block_coupon');
        $mform->setDefault('coupon_recipients_manual', 'E-mail,Gender,Name');

        $mform->addElement('static', 'coupon_recipients_desc', '', get_string('coupon_recipients_desc', 'block_coupon'));

        // Editable email message.
        $mform->addElement('editor', 'email_body_manual', get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
        $mform->setType('email_body_manual', PARAM_RAW);
        $mform->setDefault('email_body_manual', array('text' => get_string('coupon_mail_csv_content_cohorts', 'block_coupon')));
        $mform->addRule('email_body_manual', get_string('required'), 'required');
        $mform->addHelpButton('email_body_manual', 'label:email_body', 'block_coupon');

        // Configurable enrolment time.
        $mform->addElement('date_selector', 'date_send_coupons_manual', get_string('label:date_send_coupons', 'block_coupon'));
        $mform->addRule('date_send_coupons_manual', get_string('required'), 'required');
        $mform->addHelpButton('date_send_coupons_manual', 'label:date_send_coupons', 'block_coupon');

        // Send coupons based on Amount field.
        $mform->addElement('header', 'amountForm', get_string('heading:amountForm', 'block_coupon'));

        // Set email_to variable.
        $usealternativeemail = get_config('block_coupon', 'use_alternative_email');
        $alternativeemail = get_config('block_coupon', 'alternative_email');

        // Amount of coupons.
        $mform->addElement('text', 'coupon_amount', get_string('label:coupon_amount', 'block_coupon'));
        $mform->setType('coupon_amount', PARAM_INT);
        $mform->addRule('coupon_amount', get_string('error:numeric_only', 'block_coupon'), 'numeric');
        $mform->addRule('coupon_amount', get_string('required'), 'required');
        $mform->addHelpButton('coupon_amount', 'label:coupon_amount', 'block_coupon');

        // Use alternative email address.
        $mform->addElement('checkbox', 'use_alternative_email', get_string('label:use_alternative_email', 'block_coupon'));
        $mform->setType('use_alternative_email', PARAM_BOOL);
        $mform->setDefault('use_alternative_email', $usealternativeemail);

        // Email address to mail to.
        $mform->addElement('text', 'alternative_email', get_string('label:alternative_email', 'block_coupon'));
        $mform->setType('alternative_email', PARAM_EMAIL);
        $mform->setDefault('alternative_email', $alternativeemail);
        $mform->addRule('alternative_email', get_string('error:invalid_email', 'block_coupon'), 'email', null);
        $mform->addHelpButton('alternative_email', 'label:alternative_email', 'block_coupon');
        $mform->disabledIf('alternative_email', 'use_alternative_email', 'notchecked');

        // Generate_pdf checkbox.
        $mform->addElement('checkbox', 'generate_pdf', get_string('label:generate_pdfs', 'block_coupon'));
        $mform->addHelpButton('generate_pdf', 'label:generate_pdfs', 'block_coupon');

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