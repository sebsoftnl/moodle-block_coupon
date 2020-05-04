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
 * Enrolment extension coupon generator form (step 3)
 *
 * File         page3.php
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
 * block_coupon\forms\coupon\extendenrolment\page3
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page3 extends \moodleform {

    /**
     * form definition
     */
    protected function definition() {
        global $DB;
        $mform = & $this->_form;
        $generatoroptions = $this->_customdata['generatoroptions'];

        $courses = $DB->get_records_list('course', 'id', $generatoroptions->courses, '', 'id,shortname,fullname');

        // First display some confirmation options.
        $a = new \stdClass();
        $a->coupontype = get_string('coupon:type:enrolext', 'block_coupon');
        $a->amount = $generatoroptions->amount;
        $a->owner = fullname($DB->get_record('user', array('id' => $generatoroptions->ownerid)));
        $a->duration = format_time($generatoroptions->enrolperiod);
        $a->courses = [];
        foreach ($courses as $course) {
            $a->courses[] = $course->fullname;
        }
        $a->courses = implode('<br/>', $a->courses);
        if (!empty($generatoroptions->emailto)) {
            $a->recipient = $generatoroptions->emailto;
        } else if (!empty($generatoroptions->extendusers)) {
            $a->recipient = get_string('recipient:selected:users', 'block_coupon');
            // Display recipients.
            $recipients = [];
            foreach ($generatoroptions->recipients as $recipient) {
                $recipients[] = '<b>' . $recipient->name . '</b> (<i>' .
                        $recipient->email.'</i>)';
            }
            if (!empty($recipients)) {
                $a->recipient .= '<br/>' . implode('<br/>', $recipients);
            }
        } else {
            $a->recipient = get_string('recipient:none', 'block_coupon');
        }
        $logos = \block_coupon\logostorage::get_file_menu();
        $a->logo = $logos[$generatoroptions->logoid];
        if (empty($generatoroptions->senddate)) {
            $a->senddate = get_string('coupon:senddate:instant', 'block_coupon');
        } else {
            $a->senddate = userdate($generatoroptions->senddate);
        }
        $a->emailbody = $generatoroptions->emailbody;

        $mform->addElement('static', '_confirmdesc', '', get_string('coupon:extenrol:summary', 'block_coupon', $a));

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

}
