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
 * Message implementation
 *
 * File         couponnotification.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\couponnotification
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class couponnotification {

    /**
     * Initialize message instance
     *
     * @param string $subject
     * @return \core\message\message message
     */
    protected static function initialize_message($subject = null) {
        $supportuser = \core_user::get_support_user();
        $noreplyuser = \core_user::get_noreply_user();
        $message = new \core\message\message();
        $message->component = 'block_coupon';
        $message->name = 'coupon_notification';
        $message->userfrom = $supportuser;
        $message->userto = null;
        $message->subject = $subject;
        $message->fullmessage = '';
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = '';
        $message->smallmessage = '';
        $message->notification = 1;
        $message->contexturl = null;
        $message->contexturlname = null;
        $message->replyto = $noreplyuser->email;
        $message->attachment = '';
        $message->attachname = '';
        // Below is needed on Moodle 3.2.
        $message->courseid = 0;
        $message->modulename = 'moodle';
        $message->timecreated = time();

        return $message;
    }

    /**
     * Send message coupons were generated
     *
     * @param int $userid
     * @param string $batchid
     * @param int $ts
     */
    public static function send_notification($userid, $batchid, $ts) {
        global $CFG;

        $from = \core_user::get_noreply_user();
        $site = get_site();
        $recipient = \core_user::get_user($userid);
        $course = $site;
        $contextname = get_string('pluginname', 'block_coupon');
        $contexturl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php',
                ['id' => helper::find_block_instance_id(), 'tab' => 'unused']);

        $downloadurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/download.php', ['bid' => $batchid, 't' => $ts]);

        $a = new \stdClass();
        $a->fullname = fullname($recipient);
        $a->course = $course->fullname;
        $a->signoff = generate_email_signoff();
        $a->batchid = $batchid;
        $a->downloadlink = \html_writer::link($downloadurl, get_string('here', 'block_coupon'));

        $subject = get_string('coupon_notification_subject', 'block_coupon', $a);
        $fullmessage = get_string('coupon_notification_content', 'block_coupon', $a);
        $smallmessage = $fullmessage;

        $message = self::initialize_message($subject);
        $message->name = 'coupon_notification';
        $message->userto = $recipient;
        $message->userfrom = $from;
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $fullmessage;
        $message->smallmessage = $smallmessage;
        $message->contexturl = $contexturl;
        $message->contexturlname = $contextname;
        $message->courseid = empty($course->id) ? SITEID : $course->id;

        message_send($message);
    }


    /**
     * Send message coupons were generated
     *
     * @param int $userid
     * @param string $batchid
     * @param int $ts
     * @param string $extramessage
     */
    public static function send_request_accept_notification($userid, $batchid, $ts, $extramessage = '') {
        global $CFG;

        $from = \core_user::get_noreply_user();
        $site = get_site();
        $recipient = \core_user::get_user($userid);
        $course = $site;
        $contextname = get_string('pluginname', 'block_coupon');
        $contexturl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php',
                ['id' => helper::find_block_instance_id(), 'action' => 'batchlist']);

        $downloadurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/download.php', ['bid' => $batchid, 't' => $ts]);

        $a = new \stdClass();
        $a->fullname = fullname($recipient);
        $a->course = $course->fullname;
        $a->signoff = generate_email_signoff();
        $a->batchid = $batchid;
        $a->downloadlink = \html_writer::link($downloadurl, get_string('here', 'block_coupon'));
        $a->custommessage = '';
        if (!empty($extramessage)) {
            $a->custommessage = get_string('request:accept:custommessage', 'block_coupon', $extramessage);
        }

        $subject = get_string('request:accept:subject', 'block_coupon', $a);
        $fullmessage = get_string('request:accept:content', 'block_coupon', $a);
        $smallmessage = $fullmessage;

        $message = self::initialize_message($subject);
        $message->name = 'coupon_notification';
        $message->userto = $recipient;
        $message->userfrom = $from;
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $fullmessage;
        $message->smallmessage = $smallmessage;
        $message->contexturl = $contexturl;
        $message->contexturlname = $contextname;
        $message->courseid = empty($course->id) ? SITEID : $course->id;

        message_send($message);
    }

    /**
     * Send notification about coupn task completed.
     *
     * @param int $userid
     * @param string $batchid
     * @param int $timeexecuted
     */
    public static function send_task_notification($userid, $batchid, $timeexecuted) {
        global $CFG;

        $from = \core_user::get_noreply_user();
        $site = get_site();
        $recipient = \core_user::get_user($userid);
        $course = $site;
        $contextname = get_string('pluginname', 'block_coupon');
        $contexturl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php',
                ['id' => helper::find_block_instance_id(), 'tab' => 'unused']);

        $a = new \stdClass();
        $a->fullname = fullname($recipient);
        $a->course = $course->fullname;
        $a->signoff = generate_email_signoff();
        $a->timecreated = userdate($timeexecuted, get_string('strftimedate', 'langconfig'));
        $a->batchid = $batchid;
        $subject = get_string('confirm_coupons_sent_subject', 'block_coupon', $a);
        $fullmessage = get_string('confirm_coupons_sent_body', 'block_coupon', $a);
        $smallmessage = get_string('confirm_coupons_sent_body', 'block_coupon', $a);

        $message = self::initialize_message($subject);
        $message->name = 'coupon_task_notification';
        $message->userto = $recipient;
        $message->userfrom = $from;
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $fullmessage;
        $message->smallmessage = $smallmessage;
        $message->contexturl = $contexturl;
        $message->contexturlname = $contextname;
        $message->courseid = empty($course->id) ? SITEID : $course->id;

        message_send($message);
    }

}
