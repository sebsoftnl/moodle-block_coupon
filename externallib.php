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
 * Webservices implementation for block_coupon
 *
 * File         externallib.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use block_coupon\helper;

/**
 * Webservices implementation for block_coupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_coupon_external extends external_api {

    /**
     * Get all non-sidewide and visible courses.
     *
     * @return array
     */
    public static function get_courses() {
        $rs = \block_coupon\helper::get_visible_courses('id,shortname,fullname,idnumber');
        return array_values($rs);
    }

    /**
     * Is get_courses() allowed from AJAX?
     *
     * @return bool
     */
    public static function get_courses_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_courses_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function get_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'course record id'),
                    'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                    'fullname' => new external_value(PARAM_TEXT, 'course full name'),
                    'idnumber' => new external_value(PARAM_RAW, 'course id number'),
                )
            )
        );
    }

    /**
     * Get all cohorts.
     *
     * @return array
     */
    public static function get_cohorts() {
        $rs = \block_coupon\helper::get_cohorts('id,name,idnumber');
        return array_values($rs);
    }

    /**
     * Is get_cohorts() allowed from AJAX?
     *
     * @return bool
     */
    public static function get_cohorts_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_cohorts_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function get_cohorts_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'cohort record id'),
                    'name' => new external_value(PARAM_TEXT, 'cohort name'),
                    'idnumber' => new external_value(PARAM_RAW, 'cohort id number'),
                )
            )
        );
    }

    /**
     * Get all groups of the given course id.
     *
     * @param int $courseid course id
     * @return array
     */
    public static function get_course_groups($courseid) {
        global $CFG;
        require_once($CFG->libdir . '/grouplib.php');
        $rs = groups_get_all_groups($courseid, 0, 0, 'g.id, g.name');
        return array_values($rs);
    }

    /**
     * Is get_course_groups() allowed from AJAX?
     * @return bool
     */
    public static function get_course_groups_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_groups_parameters() {
        return new external_function_parameters(array(
            'courseid' => new external_value(PARAM_INT, 'course record id')
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function get_course_groups_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'group record id'),
                    'name' => new external_value(PARAM_TEXT, 'group name'),
                )
            )
        );
    }

    /**
     * Builds the coupons for the given course and returns the coupon codes.
     *
     * @param int $amount Amount of coupons to be generated.
     * @param int $courses Array of IDs of the courses the coupons will be generated for.
     * @param array $groups Array of IDs of all groups the users will be added to after using a Coupon.
     * @return array $coupon_codes Array of coupon codes.
     */
    public static function request_coupon_codes_for_course($amount, $courses, $groups = null) {
        // Get to work and have generator and options.
        list($generator, $unused) = static::_request_coupon_codes_for_course($amount, $courses, $groups);
        // We made it, so return the generated codes.
        return $generator->get_generated_couponcodes();
    }

    /**
     * Is request_coupon_codes_for_course() allowed from AJAX?
     * @return bool
     */
    public static function request_coupon_codes_for_course_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function request_coupon_codes_for_course_parameters() {
        return new external_function_parameters(array(
            'amount' => new external_value(PARAM_INT, 'amount of coupons to be generated'),
            'courses' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'array of IDs of the courses the coupons will be generated for')
                ),
            'groups' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'array of IDs of all groups the users will be added to after using a Coupon'),
                    '', VALUE_DEFAULT, array()
                    // We MUST default to an array. The webservice validation implementation is LACKING nullability.
                ),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function request_coupon_codes_for_course_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_TEXT, 'coupon code')
        );
    }

    /**
     * Generate coupons for a course.
     *
     * @param string $email Email address the coupons will be sent to.
     * @param int $amount Amount of coupons to be generated.
     * @param array $courses Array of IDs of the courses the coupons will be generated for.
     * @param array $groups Array of IDs of all groups the users will be added to after using a Coupon.
     * @param bool $generatesinglepdfs Will generate one PDF file for each coupon if true.
     * @return boolean $result
     */
    public static function generate_coupons_for_course($email, $amount, $courses, $groups = null, $generatesinglepdfs = false) {
        global $DB;

        // Let our other method do the magic of generating.
        list($generator, $generatoroptions) = static::_request_coupon_codes_for_course($amount, $courses, $groups);
        $generatedcodes = $generator->get_generated_couponcodes();
        // Get coupons and send off.
        $coupons = $DB->get_records_list('block_coupon', 'submission_code', $generatedcodes);
        list($status, $batchid, $ts) = block_coupon\helper::mail_coupons($coupons, $email, $generatesinglepdfs,
                false, false, $generatoroptions->batchid);

        return $status;
    }

    /**
     * Is generate_coupons_for_course() allowed from AJAX?
     * @return bool
     */
    public static function generate_coupons_for_course_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function generate_coupons_for_course_parameters() {
        return new external_function_parameters(array(
            'email' => new external_value(PARAM_EMAIL, 'email address the coupons will be sent to'),
            'amount' => new external_value(PARAM_INT, 'amount of coupons to be generated'),
            'courses' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'array of IDs of the courses the coupons will be generated for')
                ),
            'groups' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'array of IDs of all groups the users will be added to after using a Coupon'),
                    '', VALUE_DEFAULT, array()
                    // We MUST default to an array. The webservice validation implementation is LACKING nullability.
                ),
            'generatesinglepdfs' => new external_value(PARAM_BOOL,
                    'will generate one PDF file for each coupon if true', VALUE_DEFAULT, false, NULL_NOT_ALLOWED),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function generate_coupons_for_course_returns() {
        return new external_value(PARAM_BOOL, 'true on success, false on failure');
    }

    /**
     * Builds the coupons for the given cohorts and returns the coupon codes.
     *
     * @param int $amount Amount of coupons to be generated.
     * @param array $cohorts Array of IDs of the cohorts the coupons will be generated for.
     * @return array $coupon_codes Array of coupon codes.
     */
    public static function request_coupon_codes_for_cohorts($amount, $cohorts) {
        // Get to work and have generator and options.
        list($generator, $unused) = static::_request_coupon_codes_for_cohorts($amount, $cohorts);
        // We made it, so return the generated IDs.
        return $generator->get_generated_couponcodes();
    }

    /**
     * Is request_coupon_codes_for_cohorts() allowed from AJAX?
     * @return bool
     */
    public static function request_coupon_codes_for_cohorts_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function request_coupon_codes_for_cohorts_parameters() {
        return new external_function_parameters(array(
            'amount' => new external_value(PARAM_INT, 'amount of coupons to be generated'),
            'cohorts' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'array of IDs of the cohorts the coupons will be generated for')
                ),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function request_coupon_codes_for_cohorts_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_TEXT, 'coupon code')
        );
    }

    /**
     * Generate coupons for one or multiple cohorts.
     *
     * @param string $email Email address the coupons will be sent to.
     * @param int $amount Amount of coupons to be generated.
     * @param array $cohorts Array of IDs of the cohorts the coupons will be generated for.
     * @param bool $generatesinglepdfs Will generate one PDF file for each coupon if true.
     * @return boolean $result
     */
    public static function generate_coupons_for_cohorts($email, $amount, $cohorts, $generatesinglepdfs = false) {
        global $DB;

        // Let our other method do the magic of generating.
        list($generator, $generatoroptions) = static::_request_coupon_codes_for_cohorts($amount, $cohorts);
        $generatedcodes = $generator->get_generated_couponcodes();
        // Get coupons and send off.
        $coupons = $DB->get_records_list('block_coupon', 'submission_code', $generatedcodes);
        list($status, $batchid, $ts) = block_coupon\helper::mail_coupons($coupons, $email, $generatesinglepdfs,
                false, false, $generatoroptions->batchid);

        return $status;
    }

    /**
     * Is generate_coupons_for_cohorts() allowed from AJAX?
     * @return bool
     */
    public static function generate_coupons_for_cohorts_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function generate_coupons_for_cohorts_parameters() {
        return new external_function_parameters(array(
            'email' => new external_value(PARAM_EMAIL, 'email address the coupons will be sent to'),
            'amount' => new external_value(PARAM_INT, 'amount of coupons to be generated'),
            'cohorts' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'array of IDs of the cohorts the coupons will be generated for')
                ),
            'generatesinglepdfs' => new external_value(PARAM_BOOL,
                    'will generate one PDF file for each coupon if true', VALUE_DEFAULT, false, NULL_NOT_ALLOWED),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function generate_coupons_for_cohorts_returns() {
        return new external_value(PARAM_BOOL, 'true on success, false on failure');
    }

    /**
     * Returns the reports of the coupons that have been created. You can add extra parameters to force some filters on the data.
     *
     * @param string $type Type of coupons to get reports for ('course', 'cohort', 'enrolext' or 'all' (default))
     * @param int $ownerid ID of the creator of the coupons.
     * @param date $fromdate Request coupon reports created from this date.
     *          If given this should be passed in American format (yyyy-mm-dd)
     * @param date $todate Request coupon reports created until this date.
     *          If given this should be passed in American format (yyyy-mm-dd)
     * @return array $reports
     */
    public static function get_coupon_reports($type = 'all', $ownerid = null, $fromdate = null, $todate = null) {
        global $DB;
        $reports = array();

        $coupons = block_coupon\helper::get_all_coupons($type, $ownerid, $fromdate, $todate);
        // Cache-load courses/cohorts.
        $couponids = array_keys($coupons);
        list($cinsql, $cparams) = $DB->get_in_or_equal($couponids);
        $courseids = $DB->get_fieldset_select('block_coupon_courses', 'courseid', "couponid $cinsql", $cparams);
        $cohortids = $DB->get_fieldset_select('block_coupon_cohorts', 'cohortid', "couponid $cinsql", $cparams);
        $courses = $DB->get_records_list('course', 'id', $courseids, '', 'id, fullname as name, idnumber');
        $cohorts = $DB->get_records_list('cohort', 'id', $cohortids, '', 'id, name, idnumber');
        // Now generate reports.
        foreach ($coupons as $coupon) {
            $report = clone($coupon);
            unset($report->id); // Unset pkey.
            $report->timecreated = date('Y:m:d H:i:s', $report->timecreated);

            switch ($coupon->typ) {
                case \block_coupon\coupon\generatoroptions::COURSE:
                case \block_coupon\coupon\generatoroptions::ENROLEXTENSION:
                    // Load courses.
                    $cids = $DB->get_fieldset_select('block_coupon_courses', 'courseid', 'couponid = ?', array($coupon->id));
                    $report->courses = array();
                    foreach ($cids as $courseid) {
                        if (!isset($courses[$courseid])) {
                            continue;
                        }
                        $reportcourse = clone $courses[$courseid];
                        if (!empty($report->userid)) {
                            $user = (object)array('id' => $report->userid);
                            $completioninfo = block_coupon\helper::load_course_completioninfo($user, $courses[$courseid]);

                            $params = array('course' => $courseid, 'criteriatype' => COMPLETION_CRITERIA_TYPE_GRADE);
                            $completioncriteria = $DB->get_record('course_completion_criteria', $params);

                            $reportcourse->datestarted = $completioninfo->date_started;
                            $reportcourse->datecompleted = $completioninfo->date_complete;
                            $reportcourse->finalgrade = $completioninfo->str_grade;

                            if ($completioncriteria !== false && !is_null($completioncriteria->gradepass)) {
                                $reportcourse->requiredgrade = $completioncriteria->gradepass;
                            } else {
                                $reportcourse->requiredgrade = '-';
                            }
                        }
                        // Unset primary keys.
                        unset($reportcourse->id);
                        $report->courses[] = $reportcourse;
                    }
                    break;

                case \block_coupon\coupon\generatoroptions::COHORT:
                    // Load cohorts.
                    $cids = $DB->get_fieldset_select('block_coupon_cohorts', 'cohortid', 'couponid = ?', array($coupon->id));
                    $report->cohorts = array();
                    foreach ($cids as $cohortid) {
                        if (!isset($cohortid[$cohortid])) {
                            continue;
                        }
                        $reportcohort = clone $cohortid[$cohortid];
                        // Unset cohort pkey.
                        unset($reportcohort->id);
                        $report->cohorts[] = $reportcohort;
                    }
                    break;
            }
            // Unset userid.
            unset($report->userid);
            // Add.
            $reports[] = $report;
        }

        return $reports;
    }

    /**
     * Is get_coupon_reports() allowed from AJAX?
     * @return bool
     */
    public static function get_coupon_reports_is_allowed_from_ajax() {
        return false;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_coupon_reports_parameters() {
        return new external_function_parameters(array(
            'type' => new external_value(PARAM_ALPHA, 'Report type (all, enrolext, course or cohort)', VALUE_DEFAULT, 'all'),
            'ownerid' => new external_value(PARAM_INT, 'Userid of the owner', VALUE_DEFAULT, null),
            'fromdate' => new external_value(PARAM_INT, 'From data (given as a timestamp)', VALUE_DEFAULT, null),
            'todate' => new external_value(PARAM_INT, 'To data (given as a timestamp)', VALUE_DEFAULT, null)
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function get_coupon_reports_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'submission_code' => new external_value(PARAM_ALPHANUMEXT, 'coupon submission code'),
                    'timecreated' => new external_value(PARAM_TEXT, 'creation time of coupon'),
                    'claimed' => new external_value(PARAM_INT, '1 if coupon is claimed, 0 otherwise'),
                    'typ' => new external_value(PARAM_ALPHA, 'coupon type'),
                    'userfullname' => new external_value(PARAM_TEXT, 'fullname of user who claimed coupon'),
                    'useremail' => new external_value(PARAM_EMAIL, 'email address of user who claimed coupon'),
                    'useridnumber' => new external_value(PARAM_TEXT, 'idnumber of user who claimed coupon'),
                    'courses' => new external_multiple_structure(new external_single_structure(
                                array(
                                    'name' => new external_value(PARAM_TEXT, 'course full name'),
                                    'idnumber' => new external_value(PARAM_TEXT, 'course idnumber'),
                                    'datestarted' => new external_value(PARAM_TEXT,
                                            'if claimed, represent date the user started the course', VALUE_OPTIONAL),
                                    'datecompleted' => new external_value(PARAM_TEXT,
                                            'if claimed, represent date of course completion', VALUE_OPTIONAL),
                                    'finalgrade' => new external_value(PARAM_TEXT,
                                            'if completed, represent course final grade', VALUE_OPTIONAL),
                                    'requiredgrade' => new external_value(PARAM_TEXT,
                                            'if completed, represents required course grade', VALUE_OPTIONAL),
                                )),
                            'courses related to this coupon', VALUE_OPTIONAL),
                    'cohorts' => new external_multiple_structure(new external_single_structure(
                                array(
                                    'name' => new external_value(PARAM_TEXT, 'cohort name'),
                                    'idnumber' => new external_value(PARAM_TEXT, 'cohort idnumber')
                                )),
                            'cohorts related to this coupon', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Returns users based on search query.
     *
     * @param string $query search string
     * @return array $users
     */
    public static function find_users($query) {
        global $CFG, $DB;

        $where = array();
        $qparams = array();
        // Never include site gusts.
        $where[] = 'u.id <> '.$CFG->siteguest;
        // Do not include admins.
        $where[] = 'u.id NOT IN ('.$CFG->siteadmins.')';
        $where[] = 'u.id NOT IN (SELECT userid FROM {block_coupon_rusers})';

        $query = "%{$query}%";
        $qwhere = [];
        $qwhere[] = $DB->sql_like($DB->sql_fullname('u.firstname', 'u.lastname'), '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like($DB->sql_fullname('u.lastname', 'u.firstname'), '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like('u.username', '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like('u.email', '?', false, false);
        $qparams[] = $query;

        $where[] = '('.implode(' OR ', $qwhere).')';

        $sql = "SELECT id, username, ". get_all_user_name_fields(true, 'u')." FROM {user} u
             WHERE ".implode(" AND ", $where).
             " ORDER BY firstname ASC";
        $rs = $DB->get_recordset_sql($sql, $qparams);
        $users = [];
        foreach ($rs as $user) {
            $users[] = (object)[
                'id' => $user->id,
                'name' => fullname($user) . ' ('.$user->username.')'
                ];
        }
        $rs->close();

        return $users;
    }

    /**
     * Is find_users() allowed from AJAX?
     * @return bool
     */
    public static function find_users_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_users_parameters() {
        return new external_function_parameters(array(
            'query' => new external_value(PARAM_TEXT,
                    'search string', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'user id'),
                    'name' => new external_value(PARAM_TEXT, 'name'),
                )
            )
        );
    }

    /**
     * Returns courses based on search query.
     *
     * @param string $query search string
     * @return array $courses
     */
    public static function find_courses($query) {
        global $DB;

        $where = array();
        $qparams = array();
        // Dont include the SITE.
        $where[] = 'c.id <> ' . SITEID;
        $where[] = 'c.visible = 1';

        $query = "%{$query}%";
        $qwhere = [];
        $qwhere[] = $DB->sql_like('c.shortname', '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like('c.fullname', '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like('c.idnumber', '?', false, false);
        $qparams[] = $query;

        $where[] = '('.implode(' OR ', $qwhere).')';

        $sql = "SELECT id, shortname, fullname, idnumber FROM {course} c
             WHERE ".implode(" AND ", $where).
             " ORDER BY shortname ASC";
        $rs = $DB->get_recordset_sql($sql, $qparams);
        $courses = [];
        foreach ($rs as $course) {
            $courses[] = (object)[
                'id' => $course->id,
                'name' => $course->shortname . (empty($course->idnumber) ? '' : ' ('.$course->idnumber.')')
                ];
        }
        $rs->close();

        return $courses;
    }

    /**
     * Is find_courses() allowed from AJAX?
     * @return bool
     */
    public static function find_courses_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_courses_parameters() {
        return new external_function_parameters(array(
            'query' => new external_value(PARAM_TEXT,
                    'search string', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'course id'),
                    'name' => new external_value(PARAM_TEXT, 'name'),
                )
            )
        );
    }

    /**
     * Returns potential cohort courses based on search query.
     *
     * @param int $cohortid cohort id we're locating unconnected courses for
     * @param string $query search string
     * @return array $courses
     */
    public static function find_potential_cohort_courses($cohortid, $query) {
        global $DB;

        // Exclusions.
        $excludeids = helper::get_unconnected_cohort_courses($cohortid, true);
        $excludeids[] = SITEID;
        list($idnotinsql, $qparams) = $DB->get_in_or_equal($excludeids, SQL_PARAMS_QM, 'unused', true, 0);

        $where = array();
        $where[] = "c.id {$idnotinsql}";

        $query = "%{$query}%";
        $qwhere = [];
        $qwhere[] = $DB->sql_like('c.shortname', '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like('c.fullname', '?', false, false);
        $qparams[] = $query;

        $qwhere[] = $DB->sql_like('c.idnumber', '?', false, false);
        $qparams[] = $query;

        $where[] = '('.implode(' OR ', $qwhere).')';

        $sql = "SELECT id, shortname, fullname, idnumber FROM {course} c
             WHERE ".implode(" AND ", $where).
             " ORDER BY fullname ASC";
        $rs = $DB->get_recordset_sql($sql, $qparams);
        $courses = [];
        foreach ($rs as $course) {
            $courses[] = (object)[
                'id' => $course->id,
                'name' => $course->fullname . (empty($course->idnumber) ? '' : ' ('.$course->idnumber.')')
                ];
        }
        $rs->close();

        return $courses;
    }

    /**
     * Is find_courses() allowed from AJAX?
     * @return bool
     */
    public static function find_potential_cohort_courses_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_potential_cohort_courses_parameters() {
        return new external_function_parameters(array(
            'cohortid' => new external_value(PARAM_INT,
                    'cohort id to search for', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'query' => new external_value(PARAM_TEXT,
                    'search string', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_potential_cohort_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'course id'),
                    'name' => new external_value(PARAM_TEXT, 'name'),
                )
            )
        );
    }

    /**
     * Returns cohorts based on search query.
     *
     * @param string $query search string
     * @return array $cohorts
     */
    public static function find_cohorts($query) {
        global $CFG;
        require_once($CFG->dirroot . '/cohort/lib.php');
        $rs = cohort_get_all_cohorts(0, 0, $query);
        $cohorts = [];
        foreach ($rs['cohorts'] as $cohort) {
            $cohorts[] = (object)[
                'id' => $cohort->id,
                'name' => $cohort->name . (empty($cohort->idnumber) ? '' : ' ('.$cohort->idnumber.')')
            ];
        }

        return $cohorts;
    }

    /**
     * Is find_cohorts() allowed from AJAX?
     * @return bool
     */
    public static function find_cohorts_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_cohorts_parameters() {
        return new external_function_parameters(array(
            'query' => new external_value(PARAM_TEXT,
                    'search string', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ));
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_cohorts_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'cohort id'),
                    'name' => new external_value(PARAM_TEXT, 'name'),
                )
            )
        );
    }

    /**
     * Builds the coupons for the given course and returns the coupon codes.
     *
     * @param int $amount Amount of coupons to be generated.
     * @param int $courses Array of IDs of the courses the coupons will be generated for.
     * @param array $groups Array of IDs of all groups the users will be added to after using a Coupon.
     * @return array array containing generator instance and generator options.
     */
    private static function _request_coupon_codes_for_course($amount, $courses, $groups = null) {
        global $USER;

        // Get max length for the coupon code.
        if (!$couponcodelength = get_config('coupon', 'coupon_code_length')) {
            $couponcodelength = 16;
        }

        // Initialize generator options.
        $generatoroptions = new \block_coupon\coupon\generatoroptions();
        $generatoroptions->type = \block_coupon\coupon\generatoroptions::COURSE;
        $generatoroptions->amount = $amount;
        $generatoroptions->codesize = $couponcodelength;
        $generatoroptions->cohorts = [];
        $generatoroptions->courses = $courses;
        $generatoroptions->csvrecipients = [];
        $generatoroptions->emailbody = '';
        $generatoroptions->emailto = '';
        $generatoroptions->enrolperiod = 0;// TODO: add enrolperiod?
        $generatoroptions->extendusers = [];
        $generatoroptions->generatesinglepdfs = true;
        $generatoroptions->groups = (empty($groups) ? array() : $groups);
        $generatoroptions->logoid = 0; // Use default.
        $generatoroptions->ownerid = $USER->id;
        $generatoroptions->recipients = [];
        $generatoroptions->redirecturl = null; // Leave empty; default is my dashboard page.
        $generatoroptions->senddate = 0;

        // Generate.
        $generator = new \block_coupon\coupon\generator();
        $rs = $generator->generate_coupons($generatoroptions);

        // Check if we succeeded.
        if ($rs !== true) {
            $errors = $generator->get_errors();
            throw new block_coupon\exception('err:generating:coupons', implode("\n", $errors));
        }

        // We made it, so return the generator and options.
        return [$generator, $generatoroptions];
    }

    /**
     * Builds the coupons for the given cohorts and returns the coupon codes.
     *
     * @param int $amount Amount of coupons to be generated.
     * @param array $cohorts Array of IDs of the cohorts the coupons will be generated for.
     * @return array array containing generator instance and generator options.
     */
    private static function _request_coupon_codes_for_cohorts($amount, $cohorts) {
        global $USER;

        // Get max length for the coupon code.
        if (!$couponcodelength = get_config('coupon', 'coupon_code_length')) {
            $couponcodelength = 16;
        }
        $generatoroptions = new \block_coupon\coupon\generatoroptions();
        $generatoroptions->type = \block_coupon\coupon\generatoroptions::COHORT;
        $generatoroptions->amount = $amount;
        $generatoroptions->codesize = $couponcodelength;
        $generatoroptions->cohorts = $cohorts;
        $generatoroptions->courses = [];
        $generatoroptions->csvrecipients = [];
        $generatoroptions->emailbody = '';
        $generatoroptions->emailto = '';
        $generatoroptions->enrolperiod = 0;
        $generatoroptions->extendusers = [];
        $generatoroptions->generatesinglepdfs = true;
        $generatoroptions->groups = [];
        $generatoroptions->logoid = 0; // Use default.
        $generatoroptions->ownerid = $USER->id;
        $generatoroptions->recipients = [];
        $generatoroptions->redirecturl = null; // Leave empty; default is my dashboard page.
        $generatoroptions->senddate = 0;

        // Generate.
        $generator = new \block_coupon\coupon\generator();
        $rs = $generator->generate_coupons($generatoroptions);

        // Check if we succeeded.
        if ($rs !== true) {
            $errors = $generator->get_errors();
            throw new block_coupon\exception('err:generating:coupons', implode("\n", $errors));
        }

        // We made it, so return the generator and options.
        return [$generator, $generatoroptions];
    }


}
