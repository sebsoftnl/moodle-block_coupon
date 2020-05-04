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
 * Course filter based on course id number
 *
 * File         couponcourseselect.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   1999 Martin Dougiamas  http://dougiamas.com
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon\filters;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * block_coupon\filters\couponcourseselect
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class couponcourseselect extends \user_filter_type {
    /** @var string */
    protected $fieldid;

    /**
     * Constructor
     * @param boolean $advanced advanced form element flag
     * @param string $fieldid identifier for the field in the query
     */
    public function __construct($advanced, $fieldid = 'id') {
        $this->fieldid = $fieldid;
        parent::__construct('couponcourseselect', get_string('course') . ' ID', $advanced);
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    public function get_operators() {
        return array(0 => get_string('isequalto', 'filters'),
                     1 => get_string('isnotequalto', 'filters')
            );
    }

    /**
     * Returns course menu
     * @return array of courses
     */
    public function get_coursemenu() {
        global $DB;
        return array(0 => '...') + $DB->get_records_menu('course', null, 'fullname ASC', 'id,fullname');
    }

    /**
     * Adds controls specific to this filter in the form.
     *
     * We modified this method to comply to Moodle standards.
     * This does not matter since our handler is also internal.
     *
     * @param object $mform a MoodleForm object to setup
     */
    public function setup_form(&$mform) {
        $courses = $this->get_coursemenu();
        if (count($courses) <= 1) {
            return;
        }
        $objs = array();
        $objs['select'] = $mform->createElement('select', $this->_name.'_op', null, $this->get_operators());
        $objs['value'] = $mform->createElement('select', $this->_name, null, $courses);
        $objs['select']->setLabel(get_string('limiterfor', 'filters', $this->_label));
        $objs['value']->setLabel(get_string('valuefor', 'filters', $this->_label));
        $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $mform->setType($this->_name, PARAM_INT);
        $mform->disabledIf($this->_name, $this->_name.'_op', 'eq', 5);
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }
        $mform->setDefault($this->_name.'_op', 2);
    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field    = $this->_name;
        $operator = $field.'_op';

        if (array_key_exists($operator, $formdata)) {
            if (empty($formdata->$field)) {
                // No data - no change except for empty filter.
                return false;
            }
            // If field value is set then use it, else it's null.
            $fieldvalue = null;
            if (isset($formdata->$field)) {
                $fieldvalue = $formdata->$field;
            }
            return array('operator' => (int)$formdata->$operator, 'value' => $fieldvalue);
        }

        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        static $counter = 0;
        $name = 'ex_couponcourseselect'.$counter++;

        $operator = $data['operator'];
        $value    = $data['value'];

        $params = array();

        if ($value === '') {
            return '';
        }

        switch($operator) {
            case 0: // Equals.
                $res = "cc.courseid = :$name";
                $params[$name] = "$value";
                break;
            case 1: // Not.
                $res = "cc.courseid <> :$name";
                $params[$name] = "$value";
                break;
            default:
                return '';
        }

        $sql = "{$this->fieldid} IN (SELECT couponid
                         FROM {block_coupon_courses} cc
                         WHERE $res)";
        return array($sql, $params);
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    public function get_label($data) {
        $operator  = $data['operator'];
        $value     = $data['value'];
        $operators = $this->get_operators();

        $a = new \stdClass();
        $a->label    = $this->_label;
        $a->value    = '"'.s($value).'"';
        $a->operator = $operators[$operator];

        switch ($operator) {
            case 0: // Contains.
            case 1: // Doesn't contain.
            case 2: // Equal to.
            case 3: // Starts with.
            case 4: // Ends with.
                return get_string('textlabel', 'filters', $a);
            case 5: // Empty.
                return get_string('textlabelnovalue', 'filters', $a);
        }

        return '';
    }
}
