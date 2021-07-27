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
 * this file contains the table to display coupons
 *
 * File         downloadbatchlist.php
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
 * block_coupon\tables\downloadbatchlist
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class downloadbatchlist extends \table_sql {

    /**
     * @var string
     */
    protected $strdownload;

    /**
     * Do we render the history or the current status?
     *
     * @var int
     */
    protected $ownerid;

    /**
     * Context used to check capabilities
     *
     * @var \context
     */
    protected $context;

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
     * @param \context $context - used for capability checks
     * @param int $ownerid if set, display only coupons from given owner
     */
    public function __construct(\context $context, $ownerid = null) {
        global $USER;
        parent::__construct(__CLASS__. '-' . $USER->id . '-' . ((int)$ownerid));
        $this->context = $context;
        $this->ownerid = (int)$ownerid;
        $this->sortable(false);
        $this->strdownload = get_string('view:download:title', 'block_coupon');
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
        global $CFG, $DB;
        $columns = array('tid', 'batchid', 'owner');
        if ($this->is_downloading() == '') {
            $columns[] = 'action';
        }
        $this->define_table_columns($columns);

        // Determine data/rows.
        $path = $CFG->dataroot;
        $di = new \DirectoryIterator($path);
        $rows = [];
        $batchids = [];
        $tids = [];
        foreach ($di as $fileinfo) {
            if ($fileinfo->isDir()) {
                continue;
            }
            $fn = $fileinfo->getFilename();
            $match = null;
            if (preg_match('/coupons-(.+)-(\d+)/i', $fn, $match)) {
                $rows[] = (object)[
                    'batchid' => $match[1],
                    'tid' => $match[2],
                ];
                $batchids[] = $match[1];
                $tids[] = $match[2];
            }
        }
        // Loop through rows and find owners.
        list($insql, $params) = $DB->get_in_or_equal($batchids, SQL_PARAMS_NAMED, 'bid', true, 0);

        $sql = "SELECT c.batchid, c.ownerid, " . get_all_user_name_fields(true, 'u') . "
            FROM {block_coupon} c
            JOIN {user} u ON u.id=c.ownerid
            WHERE c.batchid {$insql}
            GROUP BY c.batchid, c.ownerid";
        $udata = $DB->get_records_sql($sql, $params);
        foreach ($rows as $row) {
            if (isset($udata[$row->batchid])) {
                $row->owner = fullname($udata[$row->batchid]);
                $row->ownerid = $udata[$row->batchid]->ownerid;
            } else {
                $row->owner = '-';
                $row->ownerid = '-';
            }
        }

        // Limiting owners (this SHOULD prevent request users to view more than they're allowed to).
        if (!has_capability('block/coupon:viewallreports', $this->context)) {
            $finalrows = [];
            $batchids = [];
            $tids = [];
            foreach ($rows as $row) {
                if ($row->ownerid != $this->ownerid) {
                    continue;
                }
                $finalrows[] = $row;
                $batchids[] = $row->batchid;
                $tids[] = $row->tid;
            }
            $rows = $finalrows;
        }

        // Sort.
        array_multisort($tids, SORT_DESC, SORT_NUMERIC, $rows);

        $this->setup();
        $this->rawdata = $rows;
        $this->build_table();
        $this->finish_output();
    }

    /**
     * Render visual representation of the 'action' column for use in the table
     *
     * @param \stdClass $row
     * @return string actions
     */
    public function col_action($row) {
        global $CFG;
        $actions = array();

        global $PAGE;
        $renderer = $PAGE->get_renderer('block_coupon');
        $actions[] = $renderer->action_icon(
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/download.php',
                array('bid' => $row->batchid, 't' => $row->tid)),
                new \image_icon('i/down', $this->strdownload, 'moodle', ['class' => 'icon']),
                null,
                ['alt' => $this->strdownload, 'target' => '_new'], $linktext = '');

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
