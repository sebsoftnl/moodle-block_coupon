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
 * Coupon code generator options
 *
 * File         generatoroptions.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon;

/**
 * block_coupon\coupon\generatoroptions
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generatoroptions {
    /**
     * COURSE type generator
     */
    const COURSE = 'course';
    /**
     * COHORT type generator
     */
    const COHORT = 'cohort';
    /**
     * ENROLEXTENSION type generator
     */
    const ENROLEXTENSION = 'enrolext';
    /**
     * COURSEGROUPING type generator
     */
    const COURSEGROUPING = 'coursegrouping';

    /**
     * generator type
     * @var string 'course' of 'cohort'
     */
    public $type;
    /**
     * coupon code size
     * @var int
     */
    public $codesize;
    /**
     * Number of coupons to generate
     * @var int
     */
    public $amount;
    /**
     * moodle user id that generated the coupons
     * @var int
     */
    public $ownerid;
    /**
     * Length of enrolment (only applicable for course type coupons)
     * @var int
     */
    public $enrolperiod = 0;
    /**
     * URL to redirect to after coupon submission
     * @var string
     */
    public $redirecturl;
    /**
     * coupon recipients (only applicable when personalized)
     * @var array
     */
    public $recipients;
    /**
     * Date to send out coupons (only applicable when personalized)
     * @var int
     */
    public $senddate = 0;
    /**
     * Email template (only applicable when personalized)
     * @var string
     */
    public $emailbody;
    /**
     * Cohort IDS the coupons are generated for
     * @var array
     */
    public $cohorts = array();
    /**
     * Course IDS the coupons are generated for
     * @var array
     */
    public $courses = array();
    /**
     * Group IDS the coupons are generated for (only applicable for course type)
     * @var array
     */
    public $groups = array();
    /**
     * Grouping IDS the coupons are generated for (only applicable for coursegrouping type)
     * @var array
     */
    public $groupings = array();

    /**
     * Send to alternative email
     * @var bool
     */
    public $altemail = 0;

    /**
     * Recipient's emailaddress to either send a status to, or the coupons itself
     * @var array
     */
    public $emailto;

    /**
     * Do we render one PDF with coupons? Or are they all generated seperately?
     * @var bool
     */
    public $generatesinglepdfs = false;

    /**
     * Do we render one PDF with coupons? Or are they all generated seperately?
     * @var string
     */
    public $pdftype = 'logo';

    /**
     * CSV string indicating the recipients for personalized coupons
     * @var string
     */
    public $csvrecipients;

    /**
     * coupon extend targets (only applicable when using extend enrolment type coupon)
     * @var array
     */
    public $extendusers;

    /**
     * coupon extend target recipients (either "me" or "users")
     * @var string
     */
    public $extendusersrecipient;

    /**
     * coupon role id
     * @var int
     */
    public $roleid;

    /**
     * coupon batch id
     * @var string
     */
    public $batchid;

    /**
     * Only generate codes?
     * @var bool
     */
    public $generatecodesonly = false;

    /**
     * What generator method do we use?
     * @var string
     */
    public $generatormethod = 'amount';

    /**
     * CSV Delimiter if applicable
     * @var string
     */
    public $csvdelimitername = ',';

    /**
     * Font used for the PDF
     *
     * @var string
     */
    public $font = 'helvetica';

    /**
     * Coupon code prefix
     *
     * @var string
     */
    public $ccprefix = '';

    /**
     * Coupon code postfix
     *
     * @var string
     */
    public $ccpostfix = '';

    /**
     * Coupon code generator flags
     *
     * @var int
     */
    public $generatorflags;

    /**
     * Coupon code generator flags
     *
     * @var int
     */
    public $generatorexcludechars;

    /**
     * Coupon expiry method
     *
     * @var int
     */
    public $expirymethod = 0;

    /**
     * Coupon expiry value
     *
     * @var int
     */
    public $expiresin = 0;

    /**
     * Coupon expiry date
     *
     * @var int
     */
    public $expiresat = null;

    /**
     * coupon logo ID (0 indicates default, all other values refer to file IDs)
     * @var int
     */
    public $logoid = 0;

    /**
     * Render QR code?
     * @var int
     */
    public $renderqrcode = true;

    /**
     * Render QR code?
     * @var int
     */
    public $templateid = null;

    /**
     * create a new instance
     */
    public function __construct() {
        $this->codesize = get_config('block_coupon', 'coupon_code_length');
        if (!$this->codesize) {
            $this->codesize = 16;
        }
        $this->batchid = md5(uniqid((string)microtime(true), true));

        $this->generatorflags = codegenerator::ALL;
        $this->generatorexcludechars = ['i', 'I', 'l', 'L', 1, 0, 'o', 'O'];
        $this->expiresat = null;
    }

    /**
     * Serialize options to session.
     */
    public function to_session() {
        global $SESSION;
        $SESSION->generatoroptions = json_decode(json_encode($this));
    }

    /**
     * Load generatoroptions from session
     * @return \self
     */
    public static function from_session() {
        global $SESSION;
        $generatoroptions = new self();
        if (isset($SESSION->generatoroptions)) {
            $options = $SESSION->generatoroptions;
            foreach ($options as $key => $value) {
                $generatoroptions->{$key} = $value;
            }
        }
        return $generatoroptions;
    }

    /**
     * Clean generatoroptions from session
     */
    public static function clean_session() {
        global $SESSION;
        if (isset($SESSION->generatoroptions)) {
            unset($SESSION->generatoroptions);
        }
    }

    /**
     * Validate if we have generatoroptions in session
     */
    public static function validate_session() {
        global $SESSION;
        if (!isset($SESSION->generatoroptions)) {
            throw new \moodle_exception("error:sessions-expired", 'block_coupon');
        }
    }

    /**
     * Magic getter
     *
     * Supported:
     * - usetemplate [bool]
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($name) {
        switch ($name) {
            case 'usetemplate':
                return (intval($this->templateid) > 0);
                break;
        }

        throw new \InvalidArgumentException("magic property {$name} not valid.");
    }

    /**
     * Render confirmation info
     *
     * @return string rendered HTML
     */
    public function create_confirmation_info() {
        global $CFG, $DB, $OUTPUT, $USER;
        $exported = json_decode(json_encode($this), false);
        $template = '';
        switch ($this->type) {
            case static::COURSE:
                $courses = [];
                foreach ($exported->courses as $courseid) {
                    $course = get_course($courseid);
                    $course->fullname = format_text($course->fullname, FORMAT_MOODLE, [
                        'context' => \context_course::instance($course->id),
                        'filter' => true
                    ]);

                    $courses[] = $course;
                }
                $exported->courses = $courses;
                $exported->role = $DB->get_record('role', ['id' => $exported->roleid]);
                $exported->role->name = role_get_name($exported->role, null);

                $exported->enrolmentperiod = empty($exported->enrolperiod) ?
                        get_string('indefinite', 'block_coupon') : format_time($exported->enrolperiod);
                $exported->expirystr = empty($exported->expiresat) ?
                        get_string('never') : format_time($exported->expiresat);
                $exported->owner = \core_user::get_user($this->ownerid);
                $exported->owner->fullname = fullname($exported->owner);

                $exported->pdftemplated = !empty($exported->templateid);
                if ($exported->pdftemplated) {
                    $exported->template = $DB->get_record('block_coupon_templates', ['id' => $exported->templateid]);
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('separatepdfs', 'block_coupon') : get_string('combinedpdf', 'block_coupon');
                } else if (!empty($exported->generatecodesonly)) {
                    $exported->logo = '[[TODO]]';
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('combinedpdf', 'block_coupon') : get_string('separatepdfs', 'block_coupon');
                }

                $exported->flagstr = [];
                if (($exported->generatorflags & codegenerator::NUMERIC) === codegenerator::NUMERIC) {
                    $exported->flagstr[] = get_string('numeric', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::LETTERS) === codegenerator::LETTERS) {
                    $exported->flagstr[] = get_string('letters', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::CAPITALS) === codegenerator::CAPITALS) {
                    $exported->flagstr[] = get_string('capitals', 'block_coupon');
                }
                $exported->flagstr = implode(' | ', $exported->flagstr);
                $exported->excludechars = implode(', ', $exported->generatorexcludechars);

                if ($exported->generatormethod === 'amount') {
                    $exported->recipients = [empty($this->emailto) ? $USER->email : $this->emailto];
                } else if ($exported->generatormethod == 'manual') {
                    $exported->recipients = array_map(function($a) {
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->recipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = userdate($exported->senddate);
                } else if ($exported->generatormethod == 'csv') {
                    $exported->recipients = array_map(function($a){
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->csvrecipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = userdate($exported->senddate);
                }

                $template = 'block_coupon/generatorinfo/courseinfo';
                break;

            case static::COHORT:
                require_once($CFG->dirroot . '/cohort/lib.php');
                $context = \context_system::instance();
                $cohorts = [];
                $tmp = $DB->get_records_list('cohort', 'id', $exported->cohorts);
                foreach ($tmp as $cohort) {
                    // We don't seem to be able to use cohort_get_cohort because...
                    // ... it doesn't include "self" in the context loading.
                    if (in_array($cohort->contextid, $context->get_parent_context_ids(true))) {
                        if ($cohort->visible) {
                            $cohorts[] = $cohort;
                            continue;
                        }
                        $cohortcontext = \context::instance_by_id($cohort->contextid);
                        if (has_capability('moodle/cohort:view', $cohortcontext)) {
                            $cohorts[] = $cohort;
                            continue;
                        }
                    }
                }
                $exported->cohorts = $cohorts;
                $exported->role = $DB->get_record('role', ['id' => $exported->roleid]);
                $exported->role->name = role_get_name($exported->role, null);

                $exported->enrolmentperiod = empty($exported->enrolperiod) ?
                        get_string('indefinite', 'block_coupon') : format_time($exported->enrolperiod);
                $exported->expirystr = empty($exported->expiresat) ?
                        get_string('never') : format_time($exported->expiresat);
                $exported->owner = \core_user::get_user($this->ownerid);
                $exported->owner->fullname = fullname($exported->owner);

                $exported->pdftemplated = !empty($exported->templateid);
                if ($exported->pdftemplated) {
                    $exported->template = $DB->get_record('block_coupon_templates', ['id' => $exported->templateid]);
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('separatepdfs', 'block_coupon') : get_string('combinedpdf', 'block_coupon');
                } else if (!empty($exported->generatecodesonly)) {
                    $exported->logo = '[[TODO]]';
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('combinedpdf', 'block_coupon') : get_string('separatepdfs', 'block_coupon');
                }

                $exported->flagstr = [];
                if (($exported->generatorflags & codegenerator::NUMERIC) === codegenerator::NUMERIC) {
                    $exported->flagstr[] = get_string('numeric', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::LETTERS) === codegenerator::LETTERS) {
                    $exported->flagstr[] = get_string('letters', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::CAPITALS) === codegenerator::CAPITALS) {
                    $exported->flagstr[] = get_string('capitals', 'block_coupon');
                }
                $exported->flagstr = implode(' | ', $exported->flagstr);
                $exported->excludechars = implode(', ', $exported->generatorexcludechars);

                if ($exported->generatormethod === 'amount') {
                    $exported->recipients = [empty($this->emailto) ? $USER->email : $this->emailto];
                } else if ($exported->generatormethod == 'manual') {
                    $exported->recipients = array_map(function($a) {
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->recipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = userdate($exported->senddate);
                } else if ($exported->generatormethod == 'csv') {
                    $exported->recipients = array_map(function($a){
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->csvrecipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = userdate($exported->senddate);
                }

                $template = 'block_coupon/generatorinfo/cohortinfo';
                break;

            case static::COURSEGROUPING:
                $groupings = $DB->get_records_list('block_coupon_coursegroupings', 'id', $this->groupings);
                $exported->groupings = array_values($groupings);
                $exported->role = $DB->get_record('role', ['id' => $exported->roleid]);
                $exported->role->name = role_get_name($exported->role, null);

                $exported->enrolmentperiod = empty($exported->enrolperiod) ?
                        get_string('indefinite', 'block_coupon') : format_time($exported->enrolperiod);
                $exported->expirystr = empty($exported->expiresat) ?
                        get_string('never') : format_time($exported->expiresat);
                $exported->owner = \core_user::get_user($this->ownerid);
                $exported->owner->fullname = fullname($exported->owner);

                $exported->pdftemplated = !empty($exported->templateid);
                if ($exported->pdftemplated) {
                    $exported->template = $DB->get_record('block_coupon_templates', ['id' => $exported->templateid]);
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('separatepdfs', 'block_coupon') : get_string('combinedpdf', 'block_coupon');
                } else if (!empty($exported->generatecodesonly)) {
                    $exported->logo = '[[TODO]]';
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('combinedpdf', 'block_coupon') : get_string('separatepdfs', 'block_coupon');
                }

                $exported->flagstr = [];
                if (($exported->generatorflags & codegenerator::NUMERIC) === codegenerator::NUMERIC) {
                    $exported->flagstr[] = get_string('numeric', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::LETTERS) === codegenerator::LETTERS) {
                    $exported->flagstr[] = get_string('letters', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::CAPITALS) === codegenerator::CAPITALS) {
                    $exported->flagstr[] = get_string('capitals', 'block_coupon');
                }
                $exported->flagstr = implode(' | ', $exported->flagstr);
                $exported->excludechars = implode(', ', $exported->generatorexcludechars);

                if ($exported->generatormethod === 'amount') {
                    $exported->recipients = [empty($this->emailto) ? $USER->email : $this->emailto];
                } else if ($exported->generatormethod == 'manual') {
                    $exported->recipients = array_map(function($a) {
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->recipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = userdate($exported->senddate);
                } else if ($exported->generatormethod == 'csv') {
                    $exported->recipients = array_map(function($a){
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->csvrecipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = userdate($exported->senddate);
                }

                $template = 'block_coupon/generatorinfo/coursegroupinginfo';
                break;

            case static::ENROLEXTENSION:
                $courses = [];
                foreach ($exported->courses as $courseid) {
                    $course = get_course($courseid);
                    $course->fullname = format_text($course->fullname, FORMAT_MOODLE, [
                        'context' => \context_course::instance($course->id),
                        'filter' => true
                    ]);

                    $courses[] = $course;
                }
                $exported->courses = $courses;
                $exported->enrolmentperiod = empty($exported->enrolperiod) ?
                        get_string('indefinite', 'block_coupon') : format_time($exported->enrolperiod);
                $exported->expirystr = empty($exported->expiresat) ?
                        get_string('never') : format_time($exported->expiresat);
                $exported->owner = \core_user::get_user($this->ownerid);
                $exported->owner->fullname = fullname($exported->owner);

                $exported->pdftemplated = !empty($exported->templateid);
                if ($exported->pdftemplated) {
                    $exported->template = $DB->get_record('block_coupon_templates', ['id' => $exported->templateid]);
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('separatepdfs', 'block_coupon') : get_string('combinedpdf', 'block_coupon');
                } else if (!empty($exported->generatecodesonly)) {
                    $exported->logo = '[[TODO]]';
                    $exported->pdfcombinestr = $exported->generatesinglepdfs ?
                            get_string('combinedpdf', 'block_coupon') : get_string('separatepdfs', 'block_coupon');
                }

                $exported->flagstr = [];
                if (($exported->generatorflags & codegenerator::NUMERIC) === codegenerator::NUMERIC) {
                    $exported->flagstr[] = get_string('numeric', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::LETTERS) === codegenerator::LETTERS) {
                    $exported->flagstr[] = get_string('letters', 'block_coupon');
                }
                if (($exported->generatorflags & codegenerator::CAPITALS) === codegenerator::CAPITALS) {
                    $exported->flagstr[] = get_string('capitals', 'block_coupon');
                }
                $exported->flagstr = implode(' | ', $exported->flagstr);
                $exported->excludechars = implode(', ', $exported->generatorexcludechars);
                if ($exported->extendusersrecipient === 'me') {
                    $exported->recipients = [empty($this->emailto) ? $USER->email : $this->emailto];
                } else {
                    $exported->recipients = array_map(function($a){
                        return "{$a->email},{$a->gender},{$a->name}";
                    }, $exported->recipients);
                    $exported->mailtemplate = $exported->emailbody;
                    $exported->senddate = empty($exported->senddate) ? get_string('now') : userdate($exported->senddate);
                }

                $template = 'block_coupon/generatorinfo/extendenrolmentsinfo';
                break;

        }

        return $OUTPUT->render_from_template($template, $exported);
    }

}
