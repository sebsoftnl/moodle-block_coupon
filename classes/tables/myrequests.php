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
 * this file contains the table to display myrequests
 *
 * File         myrequests.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tables;

/**
 * block_coupon\tables\myrequests
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class myrequests extends base {
    /**
     * Create a new instance of the table
     */
    public function __construct() {
        global $USER;
        parent::__construct(__CLASS__ . '-' . $USER->id);
        $this->no_sorting('action');
    }

    /**
     * Set the sql to query the db.
     * This method is disabled for this class, since we use internal queries
     *
     * @param string $fields
     * @param string $from
     * @param string $where
     * @param array|null $params
     * @throws exception
     */
    public function set_sql($fields, $from, $where, ?array $params = null) {
        // We'll disable this method.
        throw new exception('err:statustable:set_sql');
    }

    /**
     * Display the general status log table.
     *
     * @param int $pagesize
     * @param boolean $useinitialsbar
     */
    public function render($pagesize, $useinitialsbar = true) {
        global $USER;
        $columns = ['timecreated'];
        if ($this->is_downloading() == '') {
            $columns[] = 'action';
        }
        $this->define_table_columns($columns);

        // Generate SQL.
        $fields = 'cr.*, NULL as action';
        $from = '{block_coupon_requests} cr ';

        $where = [
            'cr.userid = :userid',
            'finalized = 0',
        ];
        $params = ['userid' => $USER->id];
        // Add filtering rules.
        if (!empty($this->filtering)) {
            [$fsql, $fparams] = $this->filtering->get_sql_filter();
            if (!empty($fsql)) {
                $where[] = $fsql;
                $params += $fparams;
            }
        }

        parent::set_sql($fields, $from, implode(' AND ', $where), $params);
        $this->out($pagesize, $useinitialsbar);
    }

    /**
     * Render visual representation of the 'action' column for use in the table
     *
     * @param \stdClass $row
     * @return string actions
     */
    public function col_action($row) {
        global $OUTPUT;
        $actions = [];

        $details = \html_writer::link(
            new \moodle_url(
                $this->baseurl,
                ['action' => 'details', 'itemid' => $row->id, 'sesskey' => sesskey()]
            ),
            \html_writer::img($OUTPUT->image_url('i/search'), 'Details', ['class' => 'icon'])
        );
        $actions[] = $details;

        $delete = \html_writer::link(
            new \moodle_url(
                $this->baseurl,
                ['action' => 'delete', 'itemid' => $row->id, 'sesskey' => sesskey()]
            ),
            \html_writer::img($OUTPUT->image_url('i/delete'), 'Delete', ['class' => 'icon'])
        );
        $actions[] = $delete;

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
            $headers[] = get_string('th:' . $name, 'block_coupon');
        }
        $this->define_headers($headers);
    }
}
