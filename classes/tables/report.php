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
 * this file contains the tle to display coupon reports
 *
 * File         report.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tables;
use block_coupon\helper;
require_once($CFG->libdir . '/tablelib.php');

/**
 * block_coupon\tables\report
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends \table_sql {

    /**
     * Do we render the history or the current status?
     *
     * @var int
     */
    protected $ownerid;

    /**
     * Create a new instance of the logtable
     *
     * @param int $ownerid if set, display only report from given owner
     */
    public function __construct($ownerid = null) {
        global $USER;
        parent::__construct(__CLASS__. '-' . $USER->id . '-' . ((int)$ownerid));
        $this->ownerid = (int)$ownerid;
    }

    /**
     * Display the general status log table.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     */
    public function render($pagesize, $useinitialsbar = true) {
        $this->define_table_columns(array('user', 'coursename', 'status', 'datestart', 'datecomplete', 'grade'));
        // We won't be able to sort by most columns.
        $this->no_sorting('status');
        $this->no_sorting('datestart');
        $this->no_sorting('datecomplete');
        $this->no_sorting('grade');

        $this->out($pagesize, $useinitialsbar);
    }

    /**
     * get a useful record count.
     */
    protected function get_count() {
        /* @var $DB \moodle_database */
        global $DB;
        $queries = $this->get_query(true);
        $total = 0;
        foreach ($queries as $parts) {
            list($sql, $params) = $parts;
            $total += $DB->count_records_sql($sql, $params);
        }
        return $total;
    }

    /**
     * Get the complete query to generate the table.
     *
     * @param bool $forcount if true, generates query for counting.
     * @return array array consisting of query and parameters
     */
    protected function get_query($forcount = false) {
        global $DB;
        $q1params = array();
        $q2params = array();
        $usersql = '';
        if ($this->ownerid > 0) {
            $q1params[] = $this->ownerid;
            $q2params[] = $this->ownerid;
            $usersql = ' AND bc.ownerid = ?';
        }
        $fields = $DB->sql_concat('c.id', '\'-\'', 'bc.id') . ' as idx,
               bc.*, c.id as courseid, c.fullname as coursename,
               ' . $DB->sql_fullname() . ' as user, u.firstname, u.lastname';
        $q1 = 'SELECT ' . $fields . '
               FROM {block_coupon} bc
               JOIN {block_coupon_courses} cc ON cc.couponid=bc.id
               JOIN {user} u ON bc.userid=u.id
               LEFT JOIN {course} c ON cc.courseid=c.id
               WHERE bc.userid IS NOT NULL
               ' . $usersql;

        $q2 = 'SELECT ' . $fields . '
               FROM {block_coupon} bc
               JOIN {block_coupon_cohorts} cc ON cc.couponid=bc.id
               JOIN {user} u ON bc.userid=u.id
               LEFT JOIN {enrol} e ON cc.cohortid=e.customint1
               LEFT JOIN {course} c ON e.courseid = c.id
               WHERE bc.userid IS NOT NULL
               AND e.enrol = \'cohort\'
               ' . $usersql;
        return array("$q1 UNION DISTINCT $q2", array_merge($q1params, $q2params));
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    public function query_db($pagesize, $useinitialsbar=true) {
        global $DB;

        // Get count / data (modified version to parent).
        list($sql, $params) = $this->get_query(false);

        if (!$this->is_downloading()) {
            $total = $DB->count_records_sql('SELECT COUNT(*) FROM ('.$sql.') AS t', $params);
            $this->pagesize($pagesize, $total);
        }

        // Fetch the attempts.
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }
        $sql = 'SELECT * FROM ('.$sql.') AS t ' . $sort;

        if (!$this->is_downloading()) {
            $reportdata = $DB->get_records_sql($sql, $params, $this->get_page_start(), $this->get_page_size());
        } else {
            $reportdata = $DB->get_records_sql($sql, $params);
        }

        // Now we've got fully the main data, we'll load the completion data. This is just too nasty :(.
        foreach ($reportdata as &$row) {
            $user = (object)array('id' => $row->userid);
            $course = (object)array('id' => $row->courseid);
            $row->completiondata = helper::load_course_completioninfo($user, $course);

            $row->status = $row->completiondata->str_status;
            $row->datestart = $row->completiondata->date_started;
            $row->datecomplete = $row->completiondata->date_complete;
            $row->grade = $row->completiondata->str_grade;
        }
        unset($row);

        $this->rawdata = $reportdata;
    }

    /**
     * Convenience method to call a number of methods for you to display the table.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @param mixed $downloadhelpbutton unused
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton='') {
        $this->setup();
        $this->query_db($pagesize, $useinitialsbar);
        $this->build_table();
        $this->finish_output();
    }

    /**
     * Render visual representation of the 'user' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_user($row) {
        global $CFG;
        return '<a href="' . $CFG->wwwroot . '/user/profile.php?id=' . $row->userid . '">' . $row->user . '</a>';
    }

    /**
     * Render visual representation of the 'datestart' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_datestart($row) {
        return (is_numeric($row->datestart) ? helper::render_date($row->datestart, false) : $row->datestart);
    }

    /**
     * Render visual representation of the 'datecomplete' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_datecomplete($row) {
        return (is_numeric($row->datecomplete) ? helper::render_date($row->datecomplete, false) : $row->datecomplete);
    }

    /**
     * Render visual representation of the 'action' column for use in the table
     *
     * @param \stdClass $row
     * @return string actions
     */
    public function col_action($row) {
        $actions = array();
        return implode('', $actions);
    }

    /**
     * Return the image tag representing an action image
     *
     * @param string $action
     * @return string HTML image tag
     */
    protected function get_action_image($action) {
        global $OUTPUT;
        return '<img src="' . $OUTPUT->pix_url($action, 'block_coupon') . '"/>';
    }

    /**
     * Return a string containing the link to an action
     *
     * @param \stdClass $row
     * @param string $action
     * @return string link representing the action with an image
     */
    protected function get_action($row, $action) {
        $actionstr = 'str' . $action;
        return '<a href="' . new \moodle_url($this->baseurl,
                array('action' => $action, 'id' => $row->id)) .
                '" alt="' . $this->{$actionstr} .
                '">' . $this->get_action_image($action) . '</a>';
    }

    /**
     * Define columns for output table and define the headers through automated
     * lookup of the language strings.
     *
     * @param array $columns list of column names
     */
    protected function define_table_columns($columns) {
        $this->define_columns($columns);
        $headers = array();
        foreach ($columns as $name) {
            $headers[] = get_string('report:heading:' . $name, 'block_coupon');
        }
        $this->define_headers($headers);
    }

}