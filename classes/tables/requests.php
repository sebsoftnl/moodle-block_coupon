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
 * this file contains the table to display requests
 *
 * File         requests.php
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
 * block_coupon\tables\requests
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class requests extends \table_sql {

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
     * Localised delete confirmation string
     * @var string
     */
    protected $strdeleteconfirm;

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
     * @return \block_coupon\tables\requests
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
        $this->no_sorting('action');
        $this->sortable(true, 'timecreated', SORT_DESC);
        $this->strdelete = get_string('action:coupon:delete', 'block_coupon');
        $this->strdeleteconfirm = get_string('action:coupon:delete:confirm', 'block_coupon');
        // Needed to generate correct link to user.
        $this->useridfield = 'userid';
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
        $columns = array('fullname', 'timecreated');
        if ($this->is_downloading() == '') {
            $columns[] = 'action';
        }
        $this->define_table_columns($columns);

        // Generate SQL.
        $fields = 'cu.id, cu.timecreated, cu.configuration, cu.userid, ' . get_all_user_name_fields(true, 'u') . ', NULL as action';
        $from = '{block_coupon_requests} cu ';
        $from .= 'JOIN {user} u ON cu.userid=u.id ';
        $where = array();
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

        parent::set_sql($fields, $from, implode(' AND ', $where), $params);
        $this->out($pagesize, $useinitialsbar);
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
     * Render visual representation of the 'fullname' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_fullname($row) {
        $str = parent::col_fullname($row);
        $config = unserialize($row->configuration);
        $str .= '<br/>';
        $str .= get_string('request:info', 'block_coupon', $config);
        return $str;
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

        $deny = \html_writer::link(new \moodle_url($this->baseurl,
                ['action' => 'denyrequest', 'itemid' => $row->id, 'sesskey' => sesskey()]),
                \html_writer::img($OUTPUT->image_url('i/invalid'), 'Deny', ['class' => 'icon']));
        $actions[] = $deny;

        $accept = \html_writer::link(new \moodle_url($this->baseurl,
                ['action' => 'acceptrequest', 'itemid' => $row->id, 'sesskey' => sesskey()]),
                \html_writer::img($OUTPUT->image_url('i/checked'), 'Accept', ['class' => 'icon']));
        $actions[] = $accept;

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
        return '<img src="' . $OUTPUT->image_url($action, 'block_coupon') . '"/>';
    }

    /**
     * Return a string containing the link to an action
     *
     * @param \stdClass $row
     * @param string $action
     * @param bool $confirm true to enable javascript confirmation of this action
     * @return string link representing the action with an image
     */
    protected function get_action($row, $action, $confirm = false) {
        $actionstr = 'str' . $action;
        $onclick = '';
        if ($confirm) {
            $actionconfirmstr = 'str' . $action . 'confirm';
            $onclick = ' onclick="return confirm(\'' . $this->{$actionconfirmstr} . '\');"';
        }
        return '<a ' . $onclick . 'href="' . new \moodle_url($this->baseurl,
                array('action' => $action, 'itemid' => $row->id, 'sesskey' => sesskey())) .
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
            $headers[] = get_string('th:' . $name, 'block_coupon');
        }
        $this->define_headers($headers);
    }

}
