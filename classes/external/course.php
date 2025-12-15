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
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\external;

use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use block_coupon\helper;

/**
 * Webservices implementation for block_coupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends external_api {
    /**
     * Returns courses based on search query.
     *
     * @param string $query search string
     * @return array $courses
     */
    public static function find_courses($query) {
        global $DB;

        $where = [];
        $qparams = [];
        // Dont include the SITE.
        $where[] = 'c.id <> ' . SITEID;
        $where[] = 'c.visible = 1';

        if (!empty($query)) {
            $query = "%{$query}%";
            $qwhere = [];
            $qwhere[] = $DB->sql_like('c.shortname', '?', false, false);
            $qparams[] = $query;

            $qwhere[] = $DB->sql_like('c.fullname', '?', false, false);
            $qparams[] = $query;

            $qwhere[] = $DB->sql_like('c.idnumber', '?', false, false);
            $qparams[] = $query;

            $where[] = '(' . implode(' OR ', $qwhere) . ')';
        }

        $sql = " FROM {course} c
             WHERE " . implode(" AND ", $where) .
                " ORDER BY shortname ASC";
        $countsql = "SELECT COUNT(c.id) FROM {course} c
             WHERE " . implode(" AND ", $where);
        $counter = $DB->get_field_sql($countsql, $qparams);
        $maxitems = 100;
        if ($counter > $maxitems) {
            $ovstr = '<div class="alert alert-danger">' . get_string('err:overflow', 'block_coupon', $maxitems) . '</div>';
            return (object)[
                'overflow' => true,
                'overflowstr' => $ovstr,
                'maxresults' => $maxitems,
            ];
        }

        $rs = $DB->get_recordset_sql("SELECT id, shortname, fullname, idnumber " . $sql, $qparams);
        $courses = [];

        $config = get_config('block_coupon');
        $dfield = $config->coursedisplay ?? 'fullname';
        $appendidnumber = $config->coursenameappendidnumber ?? true;

        foreach ($rs as $course) {
            $name = $course->{$dfield};
            if ($appendidnumber) {
                $name .= (empty($course->idnumber) ? '' : ' (' . $course->idnumber . ')');
            }
            $courses[] = (object) [
                'id' => $course->id,
                'name' => $name,
            ];
        }
        $rs->close();

        return (object)[
            'maxresults' => $maxitems,
            'data' => $courses,
        ];
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_courses_parameters() {
        return new external_function_parameters([
            'query' => new external_value(
                PARAM_TEXT,
                'search string',
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
        ]);
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_courses_returns() {
        $cstruct = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'course id'),
            'name' => new external_value(PARAM_TEXT, 'name'),
        ]);
        return new external_single_structure([
            'maxresults' => new external_value(PARAM_INT),
            'overflow' => new external_value(PARAM_BOOL, 'Provided as true when too many results', VALUE_OPTIONAL),
            'overflowstr' => new external_value(PARAM_CLEANHTML, 'Provided when too many results', VALUE_OPTIONAL),
            'data' => new external_multiple_structure($cstruct, 'result data', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Returns courses based on search query.
     *
     * @param string $query search string
     * @return array $courses
     */
    public static function find_coupon_courses($query) {
        global $DB;

        $where = [];
        $qparams = [];

        if (!empty($query)) {
            $query = "%{$query}%";
            $qwhere = [];
            $qwhere[] = $DB->sql_like('c.shortname', '?', false, false);
            $qparams[] = $query;

            $qwhere[] = $DB->sql_like('c.fullname', '?', false, false);
            $qparams[] = $query;

            $qwhere[] = $DB->sql_like('c.idnumber', '?', false, false);
            $qparams[] = $query;

            $where[] = '(' . implode(' OR ', $qwhere) . ')';
        }

        if (empty($where)) {
            $where[] = '1=1';
        }

        $sql = " FROM {block_coupon_courses} cc
            JOIN {course} c ON c.id=cc.courseid
            WHERE " . implode(" AND ", $where) .
                " GROUP BY c.id ORDER BY shortname ASC";
        $countsql = "SELECT COUNT(c.id) FROM {block_coupon_courses} cc
            JOIN {course} c ON c.id=cc.courseid
            WHERE " . implode(" AND ", $where);
        $counter = $DB->get_field_sql($countsql, $qparams);
        $maxitems = 100;
        if ($counter > $maxitems) {
            $ovstr = '<div class="alert alert-danger">' . get_string('err:overflow', 'block_coupon', $maxitems) . '</div>';
            return (object)[
                'overflow' => true,
                'overflowstr' => $ovstr,
                'maxresults' => $counter,
            ];
        }

        $rs = $DB->get_recordset_sql("SELECT c.id, c.shortname, c.fullname, c.idnumber " . $sql, $qparams);
        $courses = [];

        $config = get_config('block_coupon');
        $dfield = $config->coursedisplay ?? 'fullname';
        $appendidnumber = $config->coursenameappendidnumber ?? true;

        foreach ($rs as $course) {
            $name = $course->{$dfield};
            if ($appendidnumber) {
                $name .= (empty($course->idnumber) ? '' : ' (' . $course->idnumber . ')');
            }
            $courses[] = (object) [
                'id' => $course->id,
                'name' => $name,
            ];
        }
        $rs->close();

        return (object)[
            'maxresults' => $maxitems,
            'data' => $courses,
        ];
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_coupon_courses_parameters() {
        return static::find_courses_parameters();
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_coupon_courses_returns() {
        return static::find_courses_returns();
    }

    /**
     * Get all non-sidewide and visible courses.
     *
     * @return array
     */
    public static function get_courses() {
        $rs = helper::get_visible_courses('id,shortname,fullname,idnumber');
        return array_values($rs);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_courses_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function get_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'course record id'),
                'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                'fullname' => new external_value(PARAM_TEXT, 'course full name'),
                'idnumber' => new external_value(PARAM_RAW, 'course id number'),
            ])
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
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_groups_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'course record id'),
        ]);
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function get_course_groups_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'group record id'),
                'name' => new external_value(PARAM_TEXT, 'group name'),
            ])
        );
    }
}
