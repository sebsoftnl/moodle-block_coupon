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
 * this file contains the table to display editlog
 *
 * File         editlog.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use moodle_url;

/**
 * block_coupon\tables\editlog
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editlog extends \table_sql implements \core_table\dynamic {
    /**
     * @var \context $context
     */
    protected $context;

    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * Sets up the table.
     */
    public function __construct() {
        global $USER;
        parent::__construct(str_replace('\\', '_', __CLASS__) . '-' . $USER->id);

        $this->collapsible(false);
        $this->sortable(true);
        $this->no_sorting('actions');
        $this->config = get_config('block_coupon');
    }

    /**
     * Set the filterseu.
     *
     * @param \core_table\local\filter\filterset $filterset
     * @return void
     */
    public function set_filterset(\core_table\local\filter\filterset $filterset): void {
        $this->context = \context_system::instance();
        parent::set_filterset($filterset);
    }

    /**
     * Guess the base url for the editlog table.
     */
    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/blocks/coupon/view/editlog.php');
    }

    /**
     * Get the context of the current table.
     *
     * Note: This function should not be called until after the filterset has been provided.
     *
     * @return context
     */
    public function get_context(): \context {
        return $this->context;
    }

    /**
     * Check capability for users accessing the dynamic table.
     *
     * @return bool
     */
    public function has_capability(): bool {
        return has_capability('block/coupon:administration', $this->get_context());
    }

    /**
     * Convenience method to call a number of methods for you to display the table.
     *
     * @param int $pagesize
     * @param boolean $useinitialsbar
     * @param string $downloadhelpbutton
     * @return string
     */
    public function render($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        ob_start();
        $this->out($pagesize, $useinitialsbar, $downloadhelpbutton);
        $table = ob_get_clean();
        return $table;
    }

    /**
     * Output table
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @param string $downloadhelpbutton
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        $columns = [
            'timemodified',
            'procid',
            'typ',
            'oldrefid',
            'newrefid',
        ];

        $headers = [
            get_string('time'),
            get_string('procid', 'block_coupon'),
            get_string('coupon:type', 'block_coupon'),
            get_string('sourceitem', 'block_coupon'),
            get_string('targetitem', 'block_coupon'),
        ];

        // Abuse filtering for group by aggregation.
        $aggregate = $this->filterset->get_filter('procaggregate')->current();
        if ((bool)$aggregate) {
            array_splice($columns, 3, 0, ['affecteditems']);
            array_splice($headers, 3, 0, [get_string('affecteditems', 'block_coupon')]);
        }

        if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = '';
        } else {
            array_splice($columns, 1, 0, ['user']);
            array_splice($headers, 1, 0, [get_string('user')]);
        }

        $this->define_columns($columns);
        $this->define_headers($headers);
        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_timemodified($row) {
        if (!$this->is_downloading()) {
            $user = fullname($row);
            $time = userdate($row->timemodified);
            return "<span>{$user} @ {$time}</span>";
        }
        return userdate($row->timemodified);
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_user($row) {
        return fullname($row);
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_procid($row) {
        return $row->procid;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_oldrefid($row) {
        $fortype = $this->filterset->get_filter('fortype')->current();
        switch ($fortype) {
            case 'course':
                $dfield = 'c1_' . ($this->config->coursedisplay ?? 'fullname');
                $addidnum = $this->config->coursenameappendidnumber ?? false;
                if (empty($row->c1_shortname)) {
                    // LIKELY deleted.
                    $v = $row->oldrefid;
                } else {
                    $l = new moodle_url('/course/view.php', ['id' => $row->oldrefid]);
                    $name = $row->{$dfield};
                    if ($addidnum && !empty($row->c1_idnumber)) {
                        $name .= " ($row->c1_idnumber)";
                    }
                    if (!$this->is_downloading()) {
                        $v = \html_writer::link($l, $name);
                    } else {
                        $v = $name;
                    }
                }
                break;
            case 'cohort':
                if (empty($row->c1_name)) {
                    // LIKELY deleted.
                    $name = $row->newrefid;
                } else {
                    $name = $row->c1_name;
                    if (!empty($row->c1_idnumber)) {
                        $name .= " ($row->c1_idnumber)";
                    }
                }
                $v = $name;
                break;
            default:
                $v = $row->oldrefid;
                break;
        }
        return $v;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_newrefid($row) {
        $fortype = $this->filterset->get_filter('fortype')->current();
        switch ($fortype) {
            case 'course':
                $dfield = 'c2_' . ($this->config->coursedisplay ?? 'fullname');
                $addidnum = $this->config->coursenameappendidnumber ?? false;
                if (empty($row->c2_shortname)) {
                    // LIKELY deleted.
                    $name = $row->newrefid;
                } else {
                    $l = new moodle_url('/course/view.php', ['id' => $row->newrefid]);
                    $name = $row->{$dfield};
                    if ($addidnum && !empty($row->c2_idnumber)) {
                        $name .= " ($row->c2_idnumber)";
                    }
                    if (!$this->is_downloading()) {
                        $v = \html_writer::link($l, $name);
                    } else {
                        $v = $name;
                    }
                }
                break;
            case 'cohort':
                if (empty($row->c2_name)) {
                    // LIKELY deleted.
                    $name = $row->newrefid;
                } else {
                    $name = $row->c2_name;
                    if (!empty($row->c2_idnumber)) {
                        $name .= " ($row->c2_idnumber)";
                    }
                }
                $v = $name;
                break;
            default:
                $v = $row->newrefid;
                break;
        }
        return $v;
    }

    /**
     * Generate the actions column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row) {
        global $OUTPUT;

        $actions = [];

        $aggregate = $this->filterset->get_filter('procaggregate')->current();
        if ((bool)$aggregate) {
            // Link to revert this change.
            $revertlink = new \moodle_url('#');
            $actions[] = $OUTPUT->action_icon(
                $revertlink,
                new \pix_icon('e/undo', get_string('revertproc', 'block_coupon')),
                null,
                ['class' => 'action-icon revert-icon', 'data-action' => 'revertproc',
                'data-procid' => $row->procid, 'data-type' => $this->filterset->get_filter('fortype')->current()]
            );
        } else {
            // Link to revert this change.
            $revertlink = new \moodle_url('#');
            $actions[] = $OUTPUT->action_icon(
                $revertlink,
                new \pix_icon('e/undo', get_string('revertmod', 'block_coupon')),
                null,
                ['class' => 'action-icon revert-icon', 'data-action' => 'revertmod',
                'data-modid' => $row->id, 'data-type' => $this->filterset->get_filter('fortype')->current()]
            );
        }

        return implode('', $actions);
    }

    /**
     * Initialise database related parts.
     */
    protected function init_sql() {
        // ABUSE filter to implement grouping :D ("group by m.procid").
        $aggregate = $this->filterset->get_filter('procaggregate')->current();
        // And this is an actual filter.
        $fortype = $this->filterset->get_filter('fortype')->current();

        $unf = \block_coupon\helper::get_all_user_name_fields(true, 'u');
        $fields = 'm.*, ' . $unf;
        $from = "{block_coupon_modifications} m";
        $from .= " JOIN {block_coupon} c ON c.id=m.couponid";
        $from .= ' JOIN {user} u ON u.id = m.usermodified';

        $wheres = ['c.typ = :typ'];
        $params = ['typ' => $fortype];

        if ((bool)$aggregate) {
            $fields .= ', COUNT(m.id) as affecteditems';
        }

        switch ($fortype) {
            case 'course':
                $fields .= ',
                    c1.shortname as c1_shortname,
                    c1.fullname as c1_fullname,
                    c1.idnumber as c1_idnumber,
                    c2.shortname as c2_shortname,
                    c2.fullname as c2_fullname,
                    c2.idnumber as c2_idnumber
                    ';
                $from .= ' LEFT JOIN {course} c1 ON c1.id = m.oldrefid';
                $from .= ' LEFT JOIN {course} c2 ON c2.id = m.newrefid';
                break;
            case 'cohort':
                $fields .= ',
                    c1.name as c1_name,
                    c1.idnumber as c1_idnumber,
                    c2.name as c2_name,
                    c2.idnumber as c2_idnumber
                    ';
                $from .= ' LEFT JOIN {cohort} c1 ON c1.id = m.oldrefid';
                $from .= ' LEFT JOIN {cohort} c2 ON c2.id = m.newrefid';
                break;
        }

        // Prepare final values.
        if ($wheres) {
            switch ($this->filterset->get_join_type()) {
                case $this->filterset::JOINTYPE_ALL:
                    $wherenot = '';
                    $wheresjoin = ' AND ';
                    break;
                case $this->filterset::JOINTYPE_NONE:
                    $wherenot = ' NOT ';
                    $wheresjoin = ' AND NOT ';

                    // Some of the $where conditions may begin with `NOT` which results in `AND NOT NOT ...`.
                    // To prevent this from breaking on Oracle the inner WHERE clause is wrapped in brackets, making it
                    // `AND NOT (NOT ...)` which is valid in all DBs.
                    $wheres = array_map(function ($where) {
                        return "({$where})";
                    }, $wheres);

                    break;
                default:
                    // Default to 'Any' jointype.
                    $wherenot = '';
                    $wheresjoin = ' OR ';
                    break;
            }

            $outerwhere = $wherenot . implode($wheresjoin, $wheres);
        } else {
            $outerwhere = '';
        }

        $this->set_sql($fields, $from, $outerwhere, $params);
    }

    /**
     * Query the database
     *
     * @param  int      $pagesize
     * @param  boolean  $useinitialsbar  -- unused
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        $this->init_sql();

        $countsql = "SELECT COUNT(DISTINCT m.procid) FROM {$this->sql->from}";
        $countsql .= !empty($this->sql->where) ? " WHERE {$this->sql->where}" : '';

        $sql = "SELECT {$this->sql->fields} FROM {$this->sql->from}";
        $sql .= !empty($this->sql->where) ? " WHERE {$this->sql->where}" : '';

        // Abuse filtering for group by aggregation.
        $aggregate = $this->filterset->get_filter('procaggregate')->current();
        if ((bool)$aggregate) {
            $sql .= ' GROUP BY m.procid';
        }

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sql .= " ORDER BY {$sort}";
        }

        if (!$this->is_downloading()) {
            $this->pagesize($pagesize, $DB->count_records_sql($countsql, $this->sql->params));
            $this->rawdata = $DB->get_recordset_sql(
                $sql,
                $this->sql->params,
                $this->get_page_start(),
                $this->get_page_size()
            );
        } else {
            $this->rawdata = $DB->get_recordset_sql($sql, $this->sql->params);
        }
    }
}
