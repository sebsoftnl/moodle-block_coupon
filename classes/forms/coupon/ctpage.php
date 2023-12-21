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
class ctpage extends baseform {

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

        // We need the generator options here.
        switch ($this->generatoroptions->generatormethod) {
            case 'amount':
                // Send coupons based on Amount field.
                $mform->addElement('header', 'amountForm', get_string('heading:amountForm', 'block_coupon'));

                // Set email_to variable.
                $usealternativeemail = get_config('block_coupon', 'use_alternative_email');
                $alternativeemail = get_config('block_coupon', 'alternative_email');

                // Amount of coupons.
                $mform->addElement('text', 'coupon_amount', get_string('label:coupon_amount', 'block_coupon'));
                $mform->setType('coupon_amount', PARAM_INT);
                $mform->setDefault('coupon_amount', 1);
                $mform->addRule('coupon_amount', get_string('error:numeric_only', 'block_coupon'), 'numeric', null, 'client');
                $mform->addRule('coupon_amount', get_string('required'), 'required', null, 'client');
                $mform->addHelpButton('coupon_amount', 'label:coupon_amount', 'block_coupon');

                // Use alternative email address.
                $mform->addElement('advcheckbox', 'use_alternative_email',
                        get_string('label:use_alternative_email', 'block_coupon'));
                $mform->setType('use_alternative_email', PARAM_BOOL);
                $mform->setDefault('use_alternative_email', $usealternativeemail);
                $mform->addHelpButton('use_alternative_email', 'label:alternative_email', 'block_coupon');

                // Email address to mail to.
                $mform->addElement('text', 'alternative_email', get_string('label:alternative_email', 'block_coupon'));
                $mform->setType('alternative_email', PARAM_EMAIL);
                $mform->setDefault('alternative_email', $alternativeemail);
                $mform->addRule('alternative_email', get_string('error:invalid_email', 'block_coupon'), 'email', null);
                $mform->addHelpButton('alternative_email', 'label:alternative_email', 'block_coupon');
                $mform->disabledIf('alternative_email', 'use_alternative_email', 'notchecked');

                $data = [
                    'coupon_amount' => $this->generatoroptions->amount,
                    'use_alternative_email' => $this->generatoroptions->altemail,
                ];
                if ($this->generatoroptions->altemail) {
                    $data['alternative_email'] = $this->generatoroptions->emailto;
                }

                break;

            case 'manual':
                // Determine which mailtemplate to use.
                $mailcontentdefault = '';
                switch ($this->generatoroptions->type) {
                    case 'course':
                        $mailcontentdefault = get_string('coupon_mail_csv_content', 'block_coupon');
                        break;
                    case 'cohort':
                        $mailcontentdefault = get_string('coupon_mail_csv_content_cohorts', 'block_coupon');
                        break;
                }
                // Send coupons based on CSV upload.
                $mform->addElement('header', 'manualform', get_string('heading:manualForm', 'block_coupon'));

                // Textarea recipients.
                $mform->addElement('textarea', 'coupon_recipients_manual',
                        get_string("label:coupon_recipients", 'block_coupon'), 'rows="10" cols="100"');
                $mform->addRule('coupon_recipients_manual', get_string('required'), 'required', null, 'client');
                $mform->addHelpButton('coupon_recipients_manual', 'label:coupon_recipients_txt', 'block_coupon');
                $mform->setDefault('coupon_recipients_manual', 'E-mail,Gender,Name');

                $mform->addElement('static', 'coupon_recipients_desc', '', get_string('coupon_recipients_desc', 'block_coupon'));

                $mform->addElement('header', 'mailform', get_string('heading:mailsettings', 'block_coupon'));
                // Editable email message.
                \block_coupon\emailtemplates::add_form_element($mform);
                $mform->addElement('editor', 'email_body_manual', get_string('label:email_body', 'block_coupon'), ['noclean' => 1]);
                $mform->setType('email_body_manual', PARAM_RAW);
                $mform->setDefault('email_body_manual', array('text' => $mailcontentdefault));
                $mform->addRule('email_body_manual', get_string('required'), 'required', null, 'client');
                $mform->addHelpButton('email_body_manual', 'label:email_body', 'block_coupon');

                // When do we send the coupons?
                $mform->addElement('date_selector', 'date_send_coupons_manual',
                        get_string('label:date_send_coupons', 'block_coupon'));
                $mform->addRule('date_send_coupons_manual', get_string('required'), 'required');
                $mform->addHelpButton('date_send_coupons_manual', 'label:date_send_coupons', 'block_coupon');

                $recips = ['Email,Gender,Name'];
                foreach ($this->generatoroptions->recipients as $data) {
                    $recips[] = "{$data->email},{$data->gender},{$data->name}";
                }
                $data = [
                    'coupon_recipients_manual' => implode("\n", $recips),
                    'date_send_coupons_manual' => $this->generatoroptions->senddate,
                ];
                if (!empty($this->generatoroptions->emailbody)) {
                    $data['email_body_manual'] = ['text' => $this->generatoroptions->emailbody, 'format' => FORMAT_HTML];
                }

                break;

            case 'csv':
                // Determine which mailtemplate to use.
                $mailcontentdefault = '';
                switch ($this->generatoroptions->type) {
                    case 'course':
                        $mailcontentdefault = get_string('coupon_mail_csv_content', 'block_coupon');
                        break;
                    case 'cohort':
                        $mailcontentdefault = get_string('coupon_mail_csv_content_cohorts', 'block_coupon');
                        break;
                }
                // Send coupons based on CSV upload.
                $mform->addElement('header', 'csvform', get_string('heading:csvForm', 'block_coupon'));

                // Filepicker.
                $urldownloadcsv = new \moodle_url($CFG->wwwroot . '/blocks/coupon/sample.csv');
                $mform->addElement('filepicker', 'coupon_recipients',
                        get_string('label:coupon_recipients', 'block_coupon'), null, array('accepted_types' => 'csv'));
                $mform->addHelpButton('coupon_recipients', 'label:coupon_recipients', 'block_coupon');
                $mform->addElement('static', 'coupon_recipients_desc', '', get_string('coupon_recipients_desc', 'block_coupon'));
                $mform->addElement('static', 'sample_csv', '', '<a href="' . $urldownloadcsv
                        . '" target="_blank">' . get_string('download-sample-csv', 'block_coupon') . '</a>');

                $choices = helper::get_delimiter_list();
                $mform->addElement('select', 'csvdelimiter', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
                if (get_string('listsep', 'langconfig') == ';') {
                    $mform->setDefault('csvdelimiter', 'semicolon');
                } else {
                    $mform->setDefault('csvdelimiter', 'comma');
                }

                $mform->addElement('header', 'mailform', get_string('heading:mailsettings', 'block_coupon'));
                // Editable email message.
                \block_coupon\emailtemplates::add_form_element($mform);
                $mform->addElement('editor', 'email_body', get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
                $mform->setType('email_body', PARAM_RAW);
                $mform->setDefault('email_body', array('text' => $mailcontentdefault));
                $mform->addRule('email_body', get_string('required'), 'required', null, 'client');
                $mform->addHelpButton('email_body', 'label:email_body', 'block_coupon');

                // When do we send the coupons?
                $mform->addElement('date_selector', 'date_send_coupons', get_string('label:date_send_coupons', 'block_coupon'));
                $mform->addRule('date_send_coupons', get_string('required'), 'required');
                $mform->addHelpButton('date_send_coupons', 'label:date_send_coupons', 'block_coupon');

                $data = [
                    'csvdelimiter' => $this->generatoroptions->csvdelimitername,
                    'date_send_coupons' => $this->generatoroptions->senddate,
                ];
                if (!empty($this->generatoroptions->emailbody)) {
                    $data['email_body'] = ['text' => $this->generatoroptions->emailbody, 'format' => FORMAT_HTML];
                }
                break;
        }

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);

        $this->set_data($data);
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
        return $errors;
    }

}
