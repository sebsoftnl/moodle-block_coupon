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
 * this file contains the table to display coursegroupings
 *
 * File         coursegroupings.php
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

require_once($CFG->libdir . '/tablelib.php');

/**
 * block_coupon\tables\coursegroupings
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursegroupings extends \table_sql {

    /**
     * Filter for coupon display
     *
     * @var int
     */
    protected $filter;
    /**
     * Localised delete string
     * @var string
     */
    protected $strdelete;
    /**
     * Localised edit
     * @var string
     */
    protected $stredit;
    /**
     * Localised details string
     * @var string
     */
    protected $strdetails;

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
     * @return \block_coupon\tables\coursegroupings
     */
    public function set_filtering(\block_coupon\filtering\filtering $filtering) {
        $this->filtering = $filtering;
        return $this;
    }

    /**
     * Create a new instance of the table
     */
    public function __construct() {
        global $USER;
        parent::__construct(__CLASS__. '-' . $USER->id);
        $this->no_sorting('numcourses');
        $this->no_sorting('action');
        $this->sortable(true, 'name', SORT_DESC);
        $this->strdelete = get_string('action:coursegrouping:delete', 'block_coupon');
        $this->stredit = get_string('action:coursegrouping:edit', 'block_coupon');
        $this->strdetails = get_string('action:coursegrouping:details', 'block_coupon');
    }

    /**
     * Set the sql to query the db.
     * This method is disabled for this class, since we use internal queries
     *
     * @param string $fields
     * @param string $from
     * @param string $where
     * @param array $params
     * @throws exception
     */
    public function set_sql($fields, $from, $where, array $params = null) {
        // We'll disable this method.
        throw new exception('err:statustable:set_sql');
    }

    /**
     * Display the general status log table.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     */
    public function render($pagesize, $useinitialsbar = true) {
        $columns = array('name', 'idnumber', 'numcourses');
        if ($this->is_downloading() == '') {
            $columns[] = 'action';
        }
        $this->define_columns($columns);

        $headers = array(
            get_string('name'),
            get_string('idnumber'),
            get_string('numcourses', 'block_coupon'),
        );
        if ($this->is_downloading() == '') {
            $headers[] = get_string('th:action', 'block_coupon');
        }
        $this->define_headers($headers);

        // Generate SQL.
        $fields = 'cg.*, COUNT(cgc.courseid) AS numcourses, NULL as action';
        $from = '{block_coupon_coursegroupings} cg ';
        $from .= 'LEFT JOIN {block_coupon_cgcourses} cgc ON cg.id=cgc.coursegroupingid ';
        $where = array('cg.id IS NOT NULL');
        $params = array();
        // Add filtering rules.
        if (!empty($this->filtering)) {
            list($fsql, $fparams) = $this->filtering->get_sql_filter();
            if (!empty($fsql)) {
                $where[] = $fsql;
                $params += $fparams;
            }
        }

        if (empty($where)) {
            // Prevent bugs.
            $where[] = '1 = 1';
        }

        parent::set_sql($fields, $from, implode(' AND ', $where) . ' GROUP BY cg.id', $params);
        // Override countsql.
        $this->countsql = "SELECT COUNT(id) FROM {block_coupon_coursegroupings}";
        $this->out($pagesize, $useinitialsbar);
    }

    /**
     * Render visual representation of the 'fullname' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_name($row) {
        return \html_writer::link(new \moodle_url($this->baseurl,
                ['action' => 'details', 'itemid' => $row->id, 'sesskey' => sesskey()]),
                $row->name);
    }

    /**
     * Render visual representation of the 'action' column for use in the table
     *
     * @param \stdClass $row
     * @return string actions
     */
    public function col_action($row) {
        global $OUTPUT;
        $actions = array();

        $details = \html_writer::link(new \moodle_url($this->baseurl,
                ['action' => 'details', 'itemid' => $row->id, 'sesskey' => sesskey()]),
                \html_writer::img($OUTPUT->image_url('i/info'), $this->strdetails,
                        ['class' => 'icon action-icon']));
        $actions[] = $details;

        $delete = \html_writer::link(new \moodle_url($this->baseurl,
                ['action' => 'delete', 'itemid' => $row->id, 'sesskey' => sesskey()]),
                \html_writer::img($OUTPUT->image_url('i/delete'), $this->strdelete,
                        ['class' => 'icon action-icon']));
        $actions[] = $delete;

        $edit = \html_writer::link(new \moodle_url($this->baseurl,
                ['action' => 'edit', 'itemid' => $row->id, 'sesskey' => sesskey()]),
                \html_writer::img($OUTPUT->image_url('i/edit'), $this->stredit,
                        ['class' => 'icon action-icon']));
        $actions[] = $edit;

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
        $headers = array();
        foreach ($columns as $name) {
            $headers[] = get_string('th:' . $name, 'block_coupon');
        }
        $this->define_headers($headers);
    }

}
