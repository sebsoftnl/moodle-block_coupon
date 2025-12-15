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
 * this file contains the tle to display coupon errorreports
 *
 * File         errorreport.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tables;

use block_coupon\helper;

/**
 * block_coupon\tables\errorreport
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class errorreport extends base {
    /**
     * Do we render the history or the current status?
     *
     * @var int
     */
    protected $ownerid;

    /**
     * deletion string
     * @var string
     */
    protected $strdelete;

    /**
     * Create a new instance of the logtable
     *
     * @param int $ownerid if set, display only errorreport from given owner
     */
    public function __construct($ownerid = null) {
        global $USER;
        parent::__construct(__CLASS__ . '-' . $USER->id . '-' . ((int)$ownerid));
        $this->ownerid = (int)$ownerid;
        $this->strdelete = get_string('action:error:delete', 'block_coupon');
    }

    /**
     * Display the general status log table.
     *
     * @param int $pagesize
     * @param boolean $useinitialsbar
     */
    public function render($pagesize, $useinitialsbar = true) {
        $this->define_table_columns(['coupon', 'batchid', 'errortype', 'errormessage', 'timecreated', 'action']);
        // We won't be able to sort by action columns.
        $this->no_sorting('action');

        $this->out($pagesize, $useinitialsbar);
    }

    /**
     * get a useful record count.
     */
    protected function get_count() {
        global $DB;
        $queries = $this->get_query(true);
        $total = 0;
        foreach ($queries as $parts) {
            [$sql, $params] = $parts;
            $total += $DB->count_records_sql($sql, $params);
        }
        return $total;
    }

    /**
     * Get the complete query to generate the table.
     *
     * @param boolean $forcount if true, generates query for counting.
     * @return array array consisting of query and parameters
     */
    protected function get_query($forcount = false) {
        global $DB;
        $where = ['iserror = 1'];
        $params = [];
        if ($this->ownerid > 0) {
            $params['ownerid'] = $this->ownerid;
            $where[] = 'c.ownerid = :ownerid';
        }
        $fields = $DB->sql_concat('c.id', '\'-\'', 'e.id') . ' as idx,
               c.submission_code as coupon, c.batchid, e.*, null as action';
        $sql = 'SELECT ' . $fields . ' FROM {block_coupon} c ' .
               'JOIN {block_coupon_errors} e ON e.couponid=c.id';

        // Add filtering rules.
        if (!empty($this->filtering)) {
            [$fsql, $fparams] = $this->filtering->get_sql_filter();
            if (!empty($fsql)) {
                $where[] = $fsql;
                $params += $fparams;
            }
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        return [$sql, $params];
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param boolean $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        // Get count / data (modified version to parent).
        [$sql, $params] = $this->get_query(false);

        if (!$this->is_downloading()) {
            $total = $DB->count_records_sql('SELECT COUNT(*) FROM (' . $sql . ') t', $params);
            $this->pagesize($pagesize, $total);
        }

        // Fetch the attempts.
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }
        $sql = 'SELECT * FROM (' . $sql . ') t ' . $sort;

        if (!$this->is_downloading()) {
            $reportdata = $DB->get_records_sql($sql, $params, $this->get_page_start(), $this->get_page_size());
        } else {
            $reportdata = $DB->get_records_sql($sql, $params);
        }

        $this->rawdata = $reportdata;
    }

    /**
     * Convenience method to call a number of methods for you to display the table.
     *
     * @param int $pagesize
     * @param boolean $useinitialsbar
     * @param mixed $downloadhelpbutton unused
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        $this->setup();
        $this->query_db($pagesize, $useinitialsbar);
        $this->build_table();
        $this->finish_output();
    }

    /**
     * Render visual representation of the 'timecreated' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_timecreated($row) {
        return (is_numeric($row->timecreated) ? helper::render_date($row->timecreated, false) : $row->timecreated);
    }

    /**
     * Render visual representation of the 'action' column for use in the table
     *
     * @param \stdClass $row
     * @return string actions
     */
    public function col_action($row) {
        $actions = [];
        $actions[] = $this->get_action($row, 'delete');
        return implode('', $actions);
    }

    /**
     * Define columns for output table and define the headers through automated
     * lookup of the language strings.
     *
     * @param array $columns list of column names
     */
    protected function define_table_columns($columns) {
        $this->define_columns($columns);
        $headers = [];
        foreach ($columns as $name) {
            if ($name == 'batchid') {
                $headers[] = get_string('label:' . $name, 'block_coupon');
            } else {
                $headers[] = get_string('report:heading:' . $name, 'block_coupon');
            }
        }
        $this->define_headers($headers);
    }
}
