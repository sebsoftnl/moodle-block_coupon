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
 * Helper
 *
 * File         helper.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\helper
 *
 * Helper class for various functionality
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * __construct() HIDE: WE'RE STATIC
     */
    protected function __construct() {
        // Static's only please!
    }

    /**
     * Collect all courses connected to the provided cohort ID
     *
     * @param int $cohortid cohortid
     * @return bool false if no courses are connected or an array of course records
     */
    final static public function get_courses_by_cohort($cohortid) {
        global $DB;

        $sql = "
            SELECT c.id, c.fullname FROM {enrol} e
            LEFT JOIN {course} c ON e.courseid = c.id
            WHERE customint1 = ?
            AND e.enrol = 'cohort' AND c.visible = 1 AND c.id != 1
            ORDER BY c.fullname ASC";
        $cohortcourses = $DB->get_records_sql($sql, array($cohortid));

        return (count($cohortcourses) > 0) ? $cohortcourses : false;
    }

    /**
     * Get a list of courses that have NOT been enabled for cohort enrolment for a given cohort.
     *
     * @param int $cohortid
     * @return array
     */
    final static public function get_unconnected_cohort_courses($cohortid) {
        global $DB;

        $sql = "
            SELECT c.id, c.fullname FROM {course} c
            WHERE c.id != 1 AND c.visible = 1
            AND c.id NOT IN (
                SELECT courseid FROM {enrol} e
                WHERE e.customint1 = ?
                AND e.enrol = 'cohort'
            )
            ORDER BY c.fullname ASC";
        $unconnectedcourses = $DB->get_records_sql($sql, array($cohortid));

        return (!empty($unconnectedcourses)) ? $unconnectedcourses : false;
    }

    /**
     * Get a list of all cohorts
     *
     * @param string $fields the fields to get
     * @return array
     */
    static public final function get_cohorts($fields = 'id,name,idnumber') {
        global $DB;
        $cohorts = $DB->get_records('cohort', null, 'name ASC', $fields);
        return (!empty($cohorts)) ? $cohorts : false;
    }

    /**
     * Get a list of all visible courses
     *
     * @param string $fields the fields to get
     * @return array
     */
    static public final function get_visible_courses($fields = 'id,shortname,fullname,idnumber') {
        global $DB;
        $select = "id != 1 AND visible = 1";
        $courses = $DB->get_records_select('course', $select, null, 'fullname ASC', $fields);

        return (!empty($courses)) ? $courses : false;
    }

    /**
     * Get a list of coupons for a given owner.
     * If the owner is NULL or 0, this gets all coupons.
     *
     * @param int|null $ownerid
     * @return array
     */
    static public final function get_coupons_by_owner($ownerid = null) {
        global $DB;

        $params = array();
        $sql = "SELECT * FROM {block_coupon} WHERE userid IS NOT NULL";
        if (!empty($ownerid)) {
            $sql .= "AND ownerid = ?";
            $params[] = $ownerid;
        }
        $coupons = $DB->get_records_sql($sql);

        return (!empty($coupons)) ? $coupons : false;
    }

    /**
     * Gather all coupons that need sending out.
     *
     * @return array
     */
    public static final function get_coupons_to_send() {
        global $DB;
        $senddate = time();
        $sql = "
            SELECT * FROM {block_coupon} v
            WHERE senddate < ? AND issend = 0 AND for_user_email IS NOT NULL
            LIMIT 500";
        $coupons = $DB->get_records_sql($sql, array($senddate));

        return $coupons;
    }

    /**
     * Checks if the cron has send all the coupons generated at specific time by specific owner.
     *
     * @param int $ownerid
     * @param int $timecreated
     * @return bool
     */
    public static final function has_sent_all_coupons($ownerid, $timecreated) {
        global $DB;
        $conditions = array(
            'issend' => 0,
            'ownerid' => $ownerid,
            'timecreated' => $timecreated
        );
        return ($DB->count_records('block_coupon', $conditions) === 0);
    }

    /**
     * Claim a coupon
     *
     * @param string $code coupon submission code
     * @param int $foruserid user for which coupon is claimed. If not given: current user.
     */
    public static function claim_coupon($code, $foruserid = null) {
        global $CFG, $DB;
        // Get record.
        $conditions = array(
            'submission_code' => $code,
            'claimed' => 0,
        );
        $coupon = $DB->get_record('block_coupon', $conditions);
        // Base validation.
        if (empty($coupon)) {
            throw new exception('error:invalid_coupon_code');
        } else if (!is_null($coupon->userid) && $coupon->typ != coupon\generatoroptions::ENROLEXTENSION) {
            throw new exception('error:coupon_already_used');
        }

        // All these checks aren't strictly needed but alas, OOP FTW.
        $class = '\\block_coupon\\coupon\\types\\' . $coupon->typ;
        if (!class_exists($class)) {
            throw new exception('err:no-such-processor', $coupon->typ);
        }
        $rc = new \ReflectionClass($class);
        if (!$rc->implementsInterface('\\block_coupon\\coupon\\icoupontype')) {
            throw new exception('err:processor-implements', $coupon->typ);
        }

        // Load coupon type and claim().
        $instance = $rc->newInstance($coupon);
        $instance->claim($foruserid);

        return (empty($coupon->redirect_url)) ? $CFG->wwwroot . "/my" : $coupon->redirect_url;
    }

    /**
     * MailCoupons
     * This function will mail the generated coupons.
     *
     * @param array $coupons An array of generated coupons
     * @param string $emailto The email address the coupons are to be send to
     * @param bool $generatesinglepdfs Whether each coupon gets a PDF or 1 PDF for all coupons
     * @param bool $emailbody string|bool email body or false of it'll be autogenerated
     * @param bool $initiatedbycron whether or not this method was called by cron
     */
    public static final function mail_coupons($coupons, $emailto, $generatesinglepdfs = false,
            $emailbody = false, $initiatedbycron = false) {
        global $DB, $CFG;
        raise_memory_limit(MEMORY_HUGE);
        // One PDF for each coupon.
        if ($generatesinglepdfs) {

            // Initiate the mailer.
            $phpmailer = self::generate_coupon_mail($emailto, $emailbody, $initiatedbycron);
            $zip = new \ZipArchive();

            $filename = "{$CFG->dataroot}/coupons.zip";
            if (file_exists($filename)) {
                unlink($filename);
            }

            $zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $increment = 1;
            foreach ($coupons as $coupon) {
                // Generate the PDF.
                $pdfgen = new coupon\pdf(get_string('pdf:titlename', 'block_coupon'));
                // Fill the coupon with text.
                $pdfgen->set_templatemain(get_string('default-coupon-page-template-main', 'block_coupon'));
                $pdfgen->set_templatebotleft(get_string('default-coupon-page-template-botleft', 'block_coupon'));
                $pdfgen->set_templatebotright(get_string('default-coupon-page-template-botright', 'block_coupon'));
                // Generate it.
                $pdfgen->generate($coupon);
                // FI enables storing on local system, this could be nice to have?
                $pdfstr = $pdfgen->Output('coupon_' . $increment . '.pdf', 'S');
                // Add PDF to the zip.
                $zip->addFromString("coupon_$increment.pdf", $pdfstr);
                // And up the increment.
                $increment++;
            }

            $zip->close();
            // Add zip to the attachment.
            $phpmailer->AddAttachment($filename);

            // All coupons in 1 PDF.
        } else {

            $phpmailer = self::generate_coupon_mail($emailto, $emailbody, $initiatedbycron);

            $pdfgen = new coupon\pdf(get_string('pdf:titlename', 'block_coupon'));
            $pdfgen->set_templatemain(get_string('default-coupon-page-template-main', 'block_coupon'));
            $pdfgen->set_templatebotleft(get_string('default-coupon-page-template-botleft', 'block_coupon'));
            $pdfgen->set_templatebotright(get_string('default-coupon-page-template-botright', 'block_coupon'));
            $pdfgen->generate($coupons);
            // FI enables storing on local system, this could be nice to have?
            $pdfstr = $pdfgen->Output('coupons.pdf', 'S');
            $phpmailer->AddStringAttachment($pdfstr, 'coupons.pdf');
        }

        $mailstatus = $phpmailer->Send();
        if ($mailstatus) {
            // Set the coupons to send state.
            foreach ($coupons as $count => $coupon) {
                $coupon->senddate = time();
                $coupon->issend = 1;
                $DB->update_record('block_coupon', $coupon);
            }
        } else {
            // We NEED a notification somehow.
            foreach ($coupons as $count => $coupon) {
                $error = new \stdClass();
                $error->couponid = $coupon->id;
                $error->errortype = 'email';
                $error->errormessage = get_string('coupon:send:fail', 'block_coupon', $phpmailer->ErrorInfo);
                $error->timecreated = time();
                $DB->insert_record('block_coupon_errors', $error);
            }
        }

        return $mailstatus;
    }

    /**
     * Generate coupon email to send.
     *
     * @param \stdClass $emailto
     * @param string $emailbody
     * @param bool $initiatedbycron
     * @return \moodle_phpmailer
     */
    protected static final function generate_coupon_mail($emailto, $emailbody = false, $initiatedbycron = false) {
        global $USER;

        // Instantiate mailer.
        $phpmailer = get_mailer();

        // Set email from.
        if ($initiatedbycron) {
            // Get supportuser.
            $supportuser = \core_user::get_support_user();
            $phpmailer->FromName = fullname($supportuser);
            $phpmailer->From = $supportuser->email;
        } else {
            $phpmailer->FromName = fullname($USER);
            $phpmailer->From = $USER->email;
        }

        // Set email body.
        if ($emailbody !== false) {
            $phpmailer->Body = $emailbody;
        } else {
            $bodyparams = array(
                'to_name' => fullname($USER),
                'from_name' => fullname($USER)
            );
            $phpmailer->Body = get_string('coupon_mail_content', 'block_coupon', $bodyparams);
        }

        // Set last phpMailer params.
        $phpmailer->AltBody = strip_tags($phpmailer->Body);
        $phpmailer->Subject = get_string('coupon_mail_subject', 'block_coupon');
        $phpmailer->IsHTML(true);
        // Might not have a name cause the coupon recipients aren't neccesarily Moodle users.
        $phpmailer->AddAddress($emailto);
        $phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName);
        $phpmailer->AddCustomHeader("X-COUPON-Send: " . time());

        return $phpmailer;
    }

    /**
     * Send confirmation email when the cron has send all the coupons
     *
     * @param int $ownerid
     * @param int $timecreated
     * @return bool
     */
    public static final function confirm_coupons_sent($ownerid, $timecreated) {
        global $DB;

        $owner = $DB->get_record('user', array('id' => $ownerid));
        $supportuser = \core_user::get_support_user();
        $mailcontent = get_string("confirm_coupons_sent_body", 'block_coupon', array('timecreated' => date('Y-m-d', $timecreated)));

        // Send.
        $phpmailer = get_mailer();
        $phpmailer->Body = $mailcontent;
        $phpmailer->AltBody = strip_tags($mailcontent);
        $phpmailer->From = $supportuser->email;
        $phpmailer->FromName = trim($supportuser->firstname . ' ' . $supportuser->lastname);
        $phpmailer->IsHTML(true);
        $phpmailer->Subject = get_string('confirm_coupons_sent_subject', 'block_coupon');
        $phpmailer->AddAddress($owner->email);

        return $phpmailer->Send();
    }

    /**
     * Load the course completion info
     *
     * @param object $user User object from database
     * @param object $cinfo Course object from database
     */
    public static final function load_course_completioninfo($user, $cinfo) {
        global $DB, $CFG;
        static $cstatus, $completioninfo = array();

        require_once($CFG->dirroot . '/lib/gradelib.php');
        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->dirroot . '/lib/completionlib.php');

        // Completion status 'cache' values (speed up, lass!).
        if ($cstatus === null) {
            $cstatus = array();
            $cstatus['started'] = get_string('report:status_started', 'block_coupon');
            $cstatus['notstarted'] = get_string('report:status_not_started', 'block_coupon');
            $cstatus['complete'] = get_string('report:status_completed', 'block_coupon');
        }
        // Completion info 'cache' (speed up, lass!).
        if (!isset($completioninfo[$cinfo->id])) {
            $completioninfo[$cinfo->id] = new \completion_info($cinfo);
        }

        $ci = new \stdClass();
        $ci->complete = false;
        $ci->str_status = $cstatus['notstarted'];
        $ci->date_started = '-';
        $ci->date_complete = '-';
        $ci->str_grade = '-';
        $ci->gradeinfo = null;

        // Ok, fill out real data according to completion status/info.
        $com = $completioninfo[$cinfo->id];
        if ($com->is_tracked_user($user->id)) {
            // Do we have an enrolment for the course for this user.
            $sql = 'SELECT ue.* FROM {user_enrolments} ue
                    JOIN {enrol} e ON ue.enrolid=e.id
                    WHERE ue.userid = ? AND e.courseid = ?
                    ORDER BY timestart ASC, timecreated ASC';
            $records = $DB->get_records_sql($sql, array($user->id, $cinfo->id));

            if (count($records) === 1) {
                $record = array_shift($records);
                $ci->time_started = (($record->timestart > 0) ? $record->timestart : $record->timecreated);
                $ci->date_started = date('d-m-Y H:i:s', $ci->time_started);
            } else {
                $started = 0;
                $created = 0;

                foreach ($records as $record) {
                    if ($record->timestart > 0) {
                        $started = ($started == 0) ? $record->timestart : min($record->timestart, $started);
                    }
                    $created = ($created == 0) ? $record->timecreated : min($record->timecreated, $created);
                }

                $ci->time_started = (($started > 0) ? $started : $created);
                $ci->date_started = date('d-m-Y H:i:s', ($started > 0) ? $started : $created);
            }

            if ($com->is_course_complete($user->id)) {
                // Fetch details for course completion.
                $ci->complete = true;
                $comcom = new \completion_completion(array(
                    'userid' => $user->id,
                    'course' => $cinfo->id
                ));
                $ci->date_complete = date('d-m-Y H:i:s', $comcom->timecompleted);
                $ci->gradeinfo = grade_get_course_grade($user->id, $cinfo->id);
                if ($ci->gradeinfo !== false) {
                    $ci->str_grade = $ci->gradeinfo->str_grade;
                }
                $ci->str_status = $cstatus['complete'];
            } else {
                $ci->str_status = $cstatus['started'];
            }
        }

        return $ci;
    }

    /**
     * Sync all cohort course links.
     *
     * @return int 0 means ok, 1 means error, 2 means plugin disabled
     */
    public static final function enrol_cohort_sync() {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/cohort/locallib.php');
        if ($CFG->version < 2013051400) {
            return enrol_cohort_sync();
        } else {
            $trace = new \null_progress_trace();
            return enrol_cohort_sync($trace);
        }
    }

    /**
     * Format a datestring in short or long format
     *
     * @param int $time
     * @param bool $inctime
     * @return string user date
     */
    final static public function render_date($time, $inctime = true) {
        return userdate($time, get_string($inctime ? 'report:dateformat' : 'report:dateformatymd', 'block_coupon'));
    }

    /**
     * Make sure editing mode is off and moodle doesn't use complete overview
     * @param moodle_url $redirecturl
     */
    public static function force_no_editing_mode($redirecturl = '') {
        global $USER, $PAGE;
        if (!empty($USER->editing)) {
            $USER->editing = 0;

            if (empty($redirecturl)) {
                $params = $PAGE->url->params();
                $redirecturl = new \moodle_url($PAGE->url, $params);
            }
            redirect($redirecturl);
        }
    }

    /**
     * Load recipients from a CSV string
     * @param string $recipientsstr
     * @return boolean|\stdClass
     */
    public static final function get_recipients_from_csv($recipientsstr) {

        $recipients = array();
        $count = 0;

        // Split up in rows.
        $expectedcolumns = array('e-mail', 'gender', 'name');
        if (!$csvdata = str_getcsv($recipientsstr, "\n")) {
            return false;
        }
        // Split up in columns.
        foreach ($csvdata as &$row) {

            // Get the next row.
            $row = str_getcsv($row, ",");

            // Check if we're looking at the first row.
            if ($count == 0) {

                $expectedrow = array();
                // Set the columns we'll need.
                foreach ($row as $key => &$column) {

                    $column = trim(strtolower($column));
                    if (!in_array($column, $expectedcolumns)) {
                        continue;
                    }

                    $expectedrow[$key] = $column;
                }
                // If we're missing columns.
                if (count($expectedcolumns) != count($expectedrow)) {
                    return false;
                }

                // Now set which columns we'll need to use when extracting the information.
                $namekey = array_search('name', $expectedrow);
                $emailkey = array_search('e-mail', $expectedrow);
                $genderkey = array_search('gender', $expectedrow);

                $count++;
                continue;
            }

            $recipient = new \stdClass();
            $recipient->name = trim($row[$namekey]);
            $recipient->email = trim($row[$emailkey]);
            $recipient->gender = trim($row[$genderkey]);

            $recipients[] = $recipient;
        }

        return $recipients;
    }

    /**
     * Validate given recipients
     * @param array $csvdata
     * @return array|true true if valid, array or error messages if invalid
     */
    public static final function validate_coupon_recipients($csvdata) {

        $error = false;
        $maxcoupons = get_config('block_coupon', 'max_coupons');

        if (!$recipients = self::get_recipients_from_csv($csvdata)) {
            // Required columns aren't found in the csv.
            $error = get_string('error:recipients-columns-missing', 'block_coupon');
        } else {
            // No recipient rows were added to the csv.
            if (empty($recipients)) {
                $error = get_string('error:recipients-empty', 'block_coupon');
                // Check max of the file.
            } else if (count($recipients) > $maxcoupons) {
                $error = get_string('error:recipients-max-exceeded', 'block_coupon');
            } else {
                // Lets run through the file to check on email addresses.
                foreach ($recipients as $recipient) {
                    if (!filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
                        $error = get_string('error:recipients-email-invalid', 'block_coupon', $recipient);
                    }
                }
            }
        }

        return ($error === false) ? true : $error;
    }

    /**
     * Get default assigned role to use with coupons.
     *
     * @return false|\stdClass
     */
    public static function get_default_coupon_role() {
        global $DB;
        $config = get_config('block_coupon');
        $role = $DB->get_record('role', array('id' => $config->defaultrole));
        return $role;
    }

    /**
     * Get course connected to coupons
     *
     * @param \stdClass $coupon
     * @return array result, keys are courseids, values are course shortnames
     */
    public static function get_coupon_courses($coupon) {
        global $DB;
        $sqls = array();
        $params = array();
        $sqls[] = 'SELECT c.id,c.shortname FROM {course} c JOIN {block_coupon_courses} cc ON cc.courseid=c.id AND cc.couponid = ?';
        $params[] = $coupon->id;
        $sqls[] = 'SELECT c.id,c.shortname FROM {block_coupon_cohorts} cc
                JOIN {enrol} e ON (e.customint1=cc.cohortid AND e.enrol=?)
                JOIN {course} c ON e.courseid=c.id
                WHERE cc.couponid = ?';
        $params[] = 'cohort';
        $params[] = $coupon->id;
        $sql = 'SELECT * FROM ((' . implode(') UNION (', $sqls) . ')) as x';
        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Get courses connected to all coupons
     *
     * @param bool $includeempty whether or not to include an empty element
     * @return array result, keys are courseids, values are course shortnames
     */
    public static function get_coupon_course_menu($includeempty = true) {
        global $DB;
        $sqls = array();
        $params = array();
        $sqls[] = 'SELECT c.id,c.shortname FROM {course} c JOIN {block_coupon_courses} cc ON cc.courseid=c.id';
        $sqls[] = 'SELECT c.id,c.shortname FROM {block_coupon_cohorts} cc
                JOIN {enrol} e ON (e.customint1=cc.cohortid AND e.enrol=?)
                JOIN {course} c ON e.courseid=c.id
            ';
        $params[] = 'cohort';
        $sql = 'SELECT DISTINCT * FROM ((' . implode(') UNION (', $sqls) . ')) as x';
        $rs = $DB->get_records_sql_menu($sql, $params);
        if ($includeempty) {
            $rs = array(0 => '...') + $rs;
        }
        return $rs;
    }

    /**
     * Get cohort connected to all coupons
     *
     * @param bool $includeempty whether or not to include an empty element
     * @return array result, keys are cohort ids, values are cohort names
     */
    public static function get_coupon_cohort_menu($includeempty = true) {
        global $DB;
        $sql = 'SELECT DISTINCT c.id,c.name FROM {block_coupon_cohorts} cc
                JOIN {cohort} c ON cc.cohortid=c.id';
        $rs = $DB->get_records_sql_menu($sql);
        if ($includeempty) {
            $rs = array(0 => '...') + $rs;
        }
        return $rs;
    }

    /**
     * Cleanup coupons given the options
     * @param \stdClass $options
     * @param string $operator (SELECT, DELETE)
     * @param string $fields
     */
    public static function cleanup_coupons_query($options, $operator = 'SELECT', $fields = 'id') {
        global $DB;
        $options = (object)(array)$options;
        if (!isset($options->type)) {
            $options->type = 0; // All.
        }
        if (!isset($options->used)) {
            $options->used = 1; // Used only.
        }
        $params = array();
        $where = array();
        // Assemble query.
        // Owner.
        if (!empty($options->ownerid)) {
            $where[] = 'ownerid = :ownerid';
            $params['ownerid'] = $options->ownerid;
        }
        // Timing.
        if (!empty($options->timebefore)) {
            $where[] = 'timecreated < :timebefore';
            $params['timebefore'] = $options->timebefore;
        }
        if (!empty($options->timeafter)) {
            $where[] = 'timecreated > :timeafter';
            $params['timeafter'] = $options->timeafter;
        }
        // Usage.
        switch($options->used) {
            case 2: // Unused.
                $where[] = '(userid IS NULL or userid = 0 OR claimed = 0)';
                break;
            case 1: // Used.
                $where[] = '(userid IS NOT NULL AND userid <> 0 OR claimed = 1)';
                break;
            case 0:
            default:
                break;
        }
        // Removal query.
        if ($options->type == 1) {
            // Course coupons.
            $subselect = 'SELECT DISTINCT couponid FROM {block_coupon_courses}';
            $inparams = array();
            if (!empty($options->course)) {
                list($insql, $inparams) = $DB->get_in_or_equal($options->course, SQL_PARAMS_NAMED, 'courseid', true, 0);
                $subselect .= ' WHERE courseid ' . $insql;
                $where[] = 'id IN ('.$subselect.')';
                $params += $inparams;
            }
        } else if ($options->type == 2) {
            // Cohort coupons.
            $subselect = 'SELECT DISTINCT couponid FROM {block_coupon_cohorts}';
            $inparams = array();
            if (!empty($options->cohort)) {
                list($insql, $inparams) = $DB->get_in_or_equal($options->cohort, SQL_PARAMS_NAMED, 'cohortid', true, 0);
                $subselect .= ' WHERE cohortid ' . $insql;
                $where[] = 'id IN ('.$subselect.')';
                $params += $inparams;
            }
        }
        $sqlparts = array($operator, $fields, 'FROM {block_coupon}');
        if (!empty($where)) {
            $sqlparts[] = 'WHERE ' . implode(' AND ', $where);
        }
        return array(implode(' ', $sqlparts), $params);

    }

    /**
     * Cleanup coupons given the options
     * @param \stdClass $options
     */
    public static function cleanup_coupons($options) {
        global $DB;
        list($idquery, $idparams) = self::cleanup_coupons_query($options, 'SELECT', 'id');
        $couponids = $DB->get_fieldset_sql($idquery, $idparams);
        if (!empty($couponids)) {
            $DB->delete_records_list('block_coupon', 'id', $couponids);
            $DB->delete_records_list('block_coupon_courses', 'couponid', $couponids);
            $DB->delete_records_list('block_coupon_cohorts', 'couponid', $couponids);
            $DB->delete_records_list('block_coupon_groups', 'couponid', $couponids);
        }
    }

    /**
     * Find first block instance id for block_coupon
     *
     * @return int
     */
    public static function find_block_instance_id() {
        global $DB;
        $rec = $DB->get_record('block_instances', array('blockname' => 'coupon'));
        if (empty($rec)) {
            return 0;
        }
        return $rec->id;
    }

    /**
     * Add default configuration form elements for the coupon generator for course or cohort type coupons.
     *
     * @param \HTML_QuickForm $mform
     * @param string $type 'cohort' or 'course'
     */
    public static function std_coupon_add_default_confirm_form_elements($mform, $type) {
        global $CFG;
        // Determine which mailtemplate to use.
        $mailcontentdefault = '';
        switch ($type) {
            case 'course':
                $mailcontentdefault = get_string('coupon_mail_csv_content', 'block_coupon');
                break;
            case 'cohort':
                $mailcontentdefault = get_string('coupon_mail_csv_content_cohorts', 'block_coupon');
                break;
        }

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
        $urldownloadcsv = new \moodle_url($CFG->wwwroot . '/blocks/coupon/sample.csv');
        $mform->addElement('filepicker', 'coupon_recipients',
                get_string('label:coupon_recipients', 'block_coupon'), null, array('accepted_types' => 'csv'));
        $mform->addHelpButton('coupon_recipients', 'label:coupon_recipients', 'block_coupon');
        $mform->addElement('static', 'coupon_recipients_desc', '', get_string('coupon_recipients_desc', 'block_coupon'));
        $mform->addElement('static', 'sample_csv', '', '<a href="' . $urldownloadcsv
                . '" target="_blank">' . get_string('download-sample-csv', 'block_coupon') . '</a>');

        // Editable email message.
        $mform->addElement('editor', 'email_body', get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
        $mform->setType('email_body', PARAM_RAW);
        $mform->setDefault('email_body', array('text' => $mailcontentdefault));
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
                get_string("label:coupon_recipients", 'block_coupon'), 'rows="10" cols="100"');
        $mform->addRule('coupon_recipients_manual', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('coupon_recipients_manual', 'label:coupon_recipients_txt', 'block_coupon');
        $mform->setDefault('coupon_recipients_manual', 'E-mail,Gender,Name');

        $mform->addElement('static', 'coupon_recipients_desc', '', get_string('coupon_recipients_desc', 'block_coupon'));

        // Editable email message.
        $mform->addElement('editor', 'email_body_manual', get_string('label:email_body', 'block_coupon'), array('noclean' => 1));
        $mform->setType('email_body_manual', PARAM_RAW);
        $mform->setDefault('email_body_manual', array('text' => $mailcontentdefault));
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
    }

    /**
     * Get all coupons based on the given parameters
     *
     * @param string $type Type of coupons to get reports for ('course', 'cohort', 'enrolext' or 'all' (default))
     * @param int $ownerid ID of the creator of the coupons.
     * @param date $fromdate Request coupon reports created from this date.
     *          If given this should be passed in American format (yyyy-mm-dd)
     * @param date $todate Request coupon reports created until this date.
     *          If given this should be passed in American format (yyyy-mm-dd)
     */
    public static function get_all_coupons($type = 'all', $ownerid = null, $fromdate = null, $todate = null) {
        global $DB;
        $params = [];
        $where = [];
        if ($type !== 'all') {
            $where[] = 'type = :typ';
            $params['typ'] = $type;
        }
        if (!empty($ownerid)) {
            $where[] = 'ownerid = :ownerid';
            $params['ownerid'] = $ownerid;
        }
        if (!empty($fromdate)) {
            $where[] = 'timecreated >= :fromdate';
            $params['fromdate'] = $fromdate;
        }
        if (!empty($todate)) {
            $where[] = 'timecreated <= :todate';
            $params['todate'] = $todate;
        }
        $sql = 'SELECT c.id, c.submission_code, c.timecreated, c.claimed, c.userid, c.typ
            , ' . $DB->sql_fullname() . ' as userfullname, u.email as useremail, u.idnumber as useridnumber
            FROM {block_coupon} c
            LEFT JOIN {user} u ON c.userid=u.id';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        return $DB->get_records_sql($sql, $params);
    }

}
