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
class courseusers extends baseform {

    /**
     * form definition
     */
    protected function definition() {
        $mform = &$this->_form;
        $this->generatoroptions = $this->_customdata['generatoroptions'];

        // General options.
        $mform->addElement('hidden', 'abort');
        $mform->setType('abort', PARAM_INT);

        // Select course users.
        $users = $this->get_course_users($this->generatoroptions->courses);
        if (count($users) === 0) {
            // We cannot process.
            $err = get_string('extendenrol:abort-no-users', 'block_coupon');
            // Also add error message to session stack.
            \core\notification::add($err);
            $mform->addElement('static', '_errabort', '', $err);
            $mform->setConstant('abort', 1);
        } else {
            $mform->setConstant('abort', 0);

            // User selection.
            $mform->addElement('static', '_extendusers', '', get_string('label:extendusers:desc', 'block_coupon'));
            $attributes = array('size' => min(10, max(4, count($users))));
            $select = $mform->addElement('select', 'extendusers',
                    get_string('label:users', 'block_coupon'), $users, $attributes);
            $select->setMultiple(true);
            $mform->addRule('extendusers', null, 'required', null, 'client');
        }

        // Who or how do we send this to?
        $mform->addElement('radio', 'extendusersrecipient', get_string('extendusers:recipient', 'block_coupon'),
                get_string('extendusers:recipient:users', 'block_coupon'), 'users');
        $mform->addElement('radio', 'extendusersrecipient', '',
                get_string('extendusers:recipient:me', 'block_coupon'), 'me');
        $mform->setType('extendusers_recipient', PARAM_ALPHA);
        $mform->setDefault('extendusers_recipient', 'me');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);

        // Set data.
        $data = [
            'extendusers' => $this->generatoroptions->extendusers,
            'extendusersrecipient' => $this->generatoroptions->extendusersrecipient,
        ];
        $this->set_data($data);
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
                \block_coupon\helper::get_all_user_name_fields(true, 'u') . ",
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
