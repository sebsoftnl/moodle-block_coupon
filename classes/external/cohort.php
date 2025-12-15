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

/**
 * Webservices implementation for block_coupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort extends external_api {
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
            $cohorts[] = (object) [
                'id' => $cohort->id,
                'name' => $cohort->name . (empty($cohort->idnumber) ? '' : ' (' . $cohort->idnumber . ')'),
            ];
        }

        return (object)[
            'maxresults' => 0,
            'data' => $cohorts,
        ];
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_cohorts_parameters() {
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
     * @return external_multiple_structure
     */
    public static function find_cohorts_returns() {
        $cstruct = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'cohort id'),
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
     * Returns cohorts based on search query.
     *
     * @param string $query search string
     * @return array $cohorts
     */
    public static function find_coupon_cohorts($query) {
        global $DB;

        $where = [];
        $qparams = [];

        if (!empty($query)) {
            $query = "%{$query}%";
            $qwhere = [];
            $qwhere[] = $DB->sql_like('c.name', '?', false, false);
            $qparams[] = $query;

            $qwhere[] = $DB->sql_like('c.idnumber', '?', false, false);
            $qparams[] = $query;

            $where[] = '(' . implode(' OR ', $qwhere) . ')';
        }

        if (empty($where)) {
            $where[] = '1=1';
        }

        $sql = " FROM {block_coupon_cohorts} cc
            JOIN {cohort} c ON c.id=cc.courseid
            WHERE " . implode(" AND ", $where) .
                " GROUP BY c.id ORDER BY name ASC";
        $countsql = "SELECT COUNT(c.id)
            FROM {block_coupon_cohorts} cc
            JOIN {cohort} c ON c.id=cc.courseid
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

        $rs = $DB->get_recordset_sql("SELECT c.id, c.name, c.idnumber " . $sql, $qparams);
        $cohorts = [];

        $appendidnumber = true;
        foreach ($rs as $cohort) {
            $name = $cohort->name;
            if ($appendidnumber) {
                $name .= (empty($cohort->idnumber) ? '' : ' (' . $cohort->idnumber . ')');
            }
            $cohorts[] = (object) [
                'id' => $cohort->id,
                'name' => $name,
            ];
        }
        $rs->close();

        return (object)[
            'maxresults' => $maxitems,
            'data' => $cohorts,
        ];
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function find_coupon_cohorts_parameters() {
        return static::find_cohorts_parameters();
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_value
     */
    public static function find_coupon_cohorts_returns() {
        return static::find_cohorts_returns();
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
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_cohorts_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function get_cohorts_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'cohort record id'),
                'name' => new external_value(PARAM_TEXT, 'cohort name'),
                'idnumber' => new external_value(PARAM_RAW, 'cohort id number'),
            ])
        );
    }
}
