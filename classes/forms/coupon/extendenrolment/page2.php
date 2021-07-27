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
 * File         page2.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\extendenrolment;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\extendenrolment\page2
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page2 extends \moodleform {

    /**
     * form definition
     */
    protected function definition() {
        global $CFG;
        $mform = & $this->_form;
        $generatoroptions = $this->_customdata['generatoroptions'];

        // General options.
        $mform->addElement('hidden', 'abort');
        $mform->setType('abort', PARAM_INT);

        // Select course users.
        $users = $this->get_course_users($generatoroptions->courses);
        if (count($users) === 0) {
            // We cannot process.
            $err = get_string('extendenrol:abort-no-users', 'block_coupon');
            // Also add error message to session stack.
            \core\notification::add($err);
            $mform->addElement('static', '_errabort', '', $err);
            $mform->setConstant('abort', 1);
        } else {
            $mform->setConstant('abort', 0);
            // Coupon logo selection.
            \block_coupon\logostorage::add_select_form_elements($mform);

            // User selection.
            $mform->addElement('static', '_extendusers', '', get_string('label:extendusers:desc', 'block_coupon'));
            $attributes = array('size' => min(10, max(4, count($users))));
            $select = $mform->addElement('select', 'extendusers',
                    get_string('label:users', 'block_coupon'), $users, $attributes);
            $select->setMultiple(true);
            $mform->addRule('extendusers', null, 'required');

            // Settings that apply for both csv and amount.
            $mform->addElement('header', 'lastSettings',
                    get_string('heading:general_settings', 'block_coupon'));

            // Configurable redirect url.
            $mform->addElement('text', 'redirect_url',
                    get_string('label:redirect_url', 'block_coupon'), array('size' => 40));
            $mform->setType('redirect_url', PARAM_LOCALURL);
            $mform->setDefault('redirect_url', $CFG->wwwroot . '/my');
            $mform->addRule('redirect_url', get_string('required'), 'required');
            $mform->addHelpButton('redirect_url', 'label:redirect_url', 'block_coupon');

            // Configurable enrolment extension time.
            $mform->addElement('static', '_enrolperiod', '',
                    get_string('label:extendperiod:desc', 'block_coupon'));
            $mform->addElement('duration', 'enrolperiod',
                    get_string('label:extendperiod', 'block_coupon'), array('size' => 40, 'optional' => true));
            $mform->setDefault('enrolperiod', 86400);

            // Set email_to variable.
            $usealternativeemail = 0;
            $alternativeemail = get_config('block_coupon', 'alternative_email');

            // Use alternative email address.
            $mform->addElement('checkbox', 'use_alternative_email',
                    get_string('label:use_alternative_email', 'block_coupon'));
            $mform->setType('use_alternative_email', PARAM_BOOL);
            $mform->setDefault('use_alternative_email', $usealternativeemail);
            $mform->disabledIf('use_alternative_email', 'mailusers', 'checked');

            // Email address to mail to.
            $mform->addElement('text', 'alternative_email',
                    get_string('label:alternative_email', 'block_coupon'), array('size' => 40));
            $mform->setType('alternative_email', PARAM_EMAIL);
            $mform->setDefault('alternative_email', $alternativeemail);
            $mform->addRule('alternative_email', get_string('error:invalid_email', 'block_coupon'), 'email', null);
            $mform->addHelpButton('alternative_email', 'label:alternative_email', 'block_coupon');
            $mform->disabledIf('alternative_email', 'use_alternative_email', 'notchecked');

            // Generate_pdf checkbox.
            $mform->addElement('advcheckbox', 'generate_pdf', get_string('label:generate_pdfs', 'block_coupon'));
            $mform->addHelpButton('generate_pdf', 'label:generate_pdfs', 'block_coupon');
            $mform->disabledIf('generate_pdf', 'use_alternative_email', 'notchecked');

            // Render QR code checkbox.
            $mform->addElement('checkbox', 'renderqrcode', get_string('label:renderqrcode', 'block_coupon'));
            $mform->addHelpButton('renderqrcode', 'label:renderqrcode', 'block_coupon');
            $mform->setDefault('renderqrcode', 1);

            $mform->addElement('header', 'manualForm', get_string('heading:manualForm', 'block_coupon'));

            // Editable email message.
            $mailcontentdefault = get_string('coupon_mail_extend_content', 'block_coupon');
            $mform->addElement('editor', 'email_body_manual',
                    get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
            $mform->setType('email_body_manual', PARAM_RAW);
            $mform->setDefault('email_body_manual', array('text' => $mailcontentdefault));
            $mform->addRule('email_body_manual', get_string('required'), 'required');
            $mform->addHelpButton('email_body_manual', 'label:email_body', 'block_coupon');

            // Configurable enrolment time.
            $options = array('optional' => true);
            $mform->addElement('date_selector', 'date_send_coupons_manual',
                    get_string('label:date_send_coupons', 'block_coupon'), $options);
            $mform->addHelpButton('date_send_coupons_manual', 'label:date_send_coupons', 'block_coupon');
        }

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

    /**
     * Load course users that have an enrolment with an end date.
     *
     * @param array $courseids
     * @param string $withcapability
     * @param bool $manualenrolmentsonly
     * @return array
     */
    protected function get_course_users($courseids, $withcapability = '', $manualenrolmentsonly = true) {
        global $DB;
        if (!is_array($courseids)) {
            $courseids = array($courseids);
        }

        $rs = [];
        foreach ($courseids as $cid) {
            $extsql = '';
            $params = [];
            if ($manualenrolmentsonly) {
                $extsql = ' AND e.enrol = ?';
                $params[] = 'manual';
            }
            $sql = "select u.id, u.deleted, u.suspended, u.email, u.username, " .
                get_all_user_name_fields(true, 'u') . ",
                ue.timeend
                FROM {user_enrolments} ue
                JOIN {enrol} e ON (e.id=ue.enrolid{$extsql})
                JOIN {user} u ON (u.id=ue.userid AND u.deleted = 0)
                WHERE (ue.timeend <> 0 AND ue.timeend IS NOT NULL)
                AND e.courseid = ?";
            $params[] = $cid;
            $cusers = $DB->get_records_sql($sql, $params);
            foreach ($cusers as $user) {
                $rs[$user->id] = fullname($user) . " ({$user->username})";
            }
        }

        return $rs;
    }

}
