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
 * Filtering implementation
 *
 * File         filtering.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   1999 Martin Dougiamas  http://dougiamas.com
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon\filtering;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/filters/date.php');
require_once($CFG->dirroot . '/user/filters/cohort.php');
require_once($CFG->dirroot . '/user/filters/select.php');

/**
 * block_coupon\filtering\filtering
 *
 * @package     block_coupon
 *
 * @copyright   1999 Martin Dougiamas  http://dougiamas.com
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class filtering {
    // @codingStandardsIgnoreStart
    /** @var array */
    public $_fields;
    /** @var addfilterform */
    public $_addform;
    /** @var activefilterform */
    public $_activeform;
    // @codingStandardsIgnoreEnd

    /**
     * Contructor
     * @param string $baseurl base url used for submission/return, null if the same of current page
     * @param array $extraparams extra page parameters
     * @param array $fieldnames array of visible user fields
     * @param array $hidefields array of fields to HIDE.
     */
    public function __construct($baseurl = null, $extraparams = null, $fieldnames = null, $hidefields = null) {
        global $SESSION;

        if (!isset($SESSION->coupon_report_filtering)) {
            $SESSION->coupon_report_filtering = [];
        }

        if (empty($hidefields)) {
            $hidefields = []; // Prevent errors.
        }

        $fields = $this->get_fields();
        if (empty($fieldnames)) {
            $fieldnames = $fields;
        }

        $this->_fields  = [];

        foreach ($fieldnames as $fieldname => $advanced) {
            if (!empty($hidefields[$fieldname])) {
                continue;
            }
            if ($field = $this->get_field($fieldname, $advanced)) {
                $this->_fields[$fieldname] = $field;
            }
        }

        // Fist the new filter form.
        $addfilterparams = ['fields' => $this->_fields, 'extraparams' => $extraparams];
        $addfilterparams['alwayscollapsed'] = true; // Collapse by default.
        $this->_addform = new addfilterform($baseurl, $addfilterparams);
        if ($adddata = $this->_addform->get_data()) {
            foreach ($this->_fields as $fname => $field) {
                $data = $field->check_data($adddata);
                if ($data === false) {
                    continue; // Nothing new.
                }
                if (!array_key_exists($fname, $SESSION->coupon_report_filtering)) {
                    $SESSION->coupon_report_filtering[$fname] = [];
                }
                $SESSION->coupon_report_filtering[$fname][] = $data;
            }
            // Clear the form.
            $_POST = [];
            $this->_addform = new addfilterform($baseurl, ['fields' => $this->_fields, 'extraparams' => $extraparams]);
        }

        // Now the active filters.
        $this->_activeform = new activefilterform($baseurl, ['fields' => $this->_fields, 'extraparams' => $extraparams]);
        if ($adddata = $this->_activeform->get_data()) {
            if (!empty($adddata->removeall)) {
                $SESSION->coupon_report_filtering = [];
            } else if (!empty($adddata->removeselected) && !empty($adddata->filter)) {
                foreach ($adddata->filter as $fname => $instances) {
                    foreach ($instances as $i => $val) {
                        if (empty($val)) {
                            continue;
                        }
                        unset($SESSION->coupon_report_filtering[$fname][$i]);
                    }
                    if (empty($SESSION->coupon_report_filtering[$fname])) {
                        unset($SESSION->coupon_report_filtering[$fname]);
                    }
                }
            }
            // Clear+reload the form.
            $_POST = [];
            $this->_activeform = new activefilterform($baseurl, ['fields' => $this->_fields, 'extraparams' => $extraparams]);
        }
    }

    /**
     * Return all default filter names and advanced status
     * @return array
     */
    abstract public function get_fields();

    /**
     * Creates known user filter if present
     * @param string $fieldname
     * @param boolean $advanced
     * @return object filter
     */
    public function get_field($fieldname, $advanced) {
        switch ($fieldname) {
            default:
                return null;
        }
    }

    /**
     * Returns sql where statement based on active user filters
     * @param string $extra sql
     * @param array|null $params named params (recommended prefix ex)
     * @return array sql string and $params
     */
    public function get_sql_filter($extra = '', ?array $params = null) {
        global $SESSION;

        $sqls = [];
        if ($extra != '') {
            $sqls[] = $extra;
        }
        $params = (array)$params;

        if (!empty($SESSION->coupon_report_filtering)) {
            foreach ($SESSION->coupon_report_filtering as $fname => $datas) {
                if (!array_key_exists($fname, $this->_fields)) {
                    continue; // Filter not used.
                }
                $field = $this->_fields[$fname];
                foreach ($datas as $i => $data) {
                    [$s, $p] = $field->get_sql_filter($data);
                    $sqls[] = $s;
                    $params = $params + $p;
                }
            }
        }

        if (empty($sqls)) {
            return ['', []];
        } else {
            $sqls = implode(' AND ', $sqls);
            return [$sqls, $params];
        }
    }

    /**
     * Print the add filter form.
     */
    public function display_add() {
        $this->_addform->display();
    }

    /**
     * Print the active filter form.
     */
    public function display_active() {
        $this->_activeform->display();
    }
}
