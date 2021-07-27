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
 * this file contains the tle to display coupon maillogs
 *
 * File         maillog.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tables;

defined('MOODLE_INTERNAL') || die();

use block_coupon\helper;
require_once($CFG->libdir . '/tablelib.php');

/**
 * block_coupon\tables\maillog
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class maillog extends \table_sql {

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
     *
     * @var \block_coupon\filtering\filtering
     */
    protected $filtering;

    /**
     * Get filtering instance
     * @return \block_coupon\filtering\filtering
     */
    public function get_filtering() {
        return $this->filtering;
    }

    /**
     * Set filtering instance
     * @param \block_coupon\filtering\filtering $filtering
     * @return \block_coupon\tables\coupons
     */
    public function set_filtering(\block_coupon\filtering\filtering $filtering) {
        $this->filtering = $filtering;
        return $this;
    }

    /**
     * Create a new instance of the logtable
     *
     * @param int $ownerid if set, display only maillog from given owner
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
        $this->define_table_columns(array('timecreated', 'errortype', 'errormessage'));
        $this->no_sorting('errortype');
        $this->no_sorting('errormessage');
        $this->sortable(true, 'timecreated', SORT_DESC);
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
        $where = array('errortype = :type');
        $params = array('type' => 'debugemail');
        $fields = 'e.*, null as action';
        $sql = 'SELECT ' . $fields . '
               FROM {block_coupon_errors} e';

        // Add filtering rules.
        if (!empty($this->filtering)) {
            list($fsql, $fparams) = $this->filtering->get_sql_filter();
            if (!empty($fsql)) {
                $where[] = $fsql;
                $params += $fparams;
            }
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        return array($sql, $params);
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
            $total = $DB->count_records_sql('SELECT COUNT(*) FROM ('.$sql.') t', $params);
            $this->pagesize($pagesize, $total);
        }

        // Fetch the attempts.
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }
        $sql = 'SELECT * FROM ('.$sql.') t ' . $sort;

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
     * Render visual representation of the 'timecreated' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_timecreated($row) {
        return userdate($row->timecreated);
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
