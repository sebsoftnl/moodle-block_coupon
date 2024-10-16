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
 * Text field filter
 *
 * File         multitext.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   2024 RvD <helpdesk@sebsoft.nl>
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\filters;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * Generic filter for text fields.
 *
 * @package     block_coupon
 *
 * @copyright   2024 RvD <helpdesk@sebsoft.nl>
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class multitext extends \user_filter_type {
    /** @var array */
    public $fields;

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param array $fields field names
     */
    public function __construct($name, $label, $advanced, array $fields) {
        parent::__construct($name, $label, $advanced);
        $this->fields = $fields;
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    public function get_operators() {
        return [0 => get_string('contains', 'filters'),
                     1 => get_string('doesnotcontain', 'filters'),
                     2 => get_string('isequalto', 'filters'),
                     3 => get_string('startswith', 'filters'),
                     4 => get_string('endswith', 'filters'),
                     5 => get_string('isempty', 'filters')];
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) { // @codingStandardsIgnoreLine Can't change parent behaviour.
        $objs = [];
        $objs['select'] = $mform->createElement('select', $this->_name.'_op', null, $this->get_operators());
        $objs['text'] = $mform->createElement('text', $this->_name, null);
        $objs['select']->setLabel(get_string('limiterfor', 'filters', $this->_label));
        $objs['text']->setLabel(get_string('valuefor', 'filters', $this->_label));
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $mform->setType($this->_name, PARAM_RAW);
        $mform->disabledIf($this->_name, $this->_name.'_op', 'eq', 5);
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }
    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field    = $this->_name;
        $operator = $field.'_op';

        $func = is_array($formdata) ? 'array_key_exists' : 'property_exists';
        $args = is_array($formdata) ? [$field, $formdata] : [$formdata, $field];

        if (call_user_func_array($func, $args)) {
            if ($formdata->$operator != 5 && $formdata->$field == '') {
                // No data - no change except for empty filter.
                return false;
            }
            // If field value is set then use it, else it's null.
            $fieldvalue = null;
            if (isset($formdata->$field)) {
                $fieldvalue = $formdata->$field;
            }
            return ['operator' => (int)$formdata->$operator, 'value' => $fieldvalue];
        }

        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        if (!is_array($this->fields)) {
            return $this->_get_sql_filter($data, $this->fields);
        } else {
            $sql = [];
            $params = [];
            foreach ($this->fields as $field) {
                list($tmpsql, $tmpparams) = $this->internal_get_sql_filter($data, $field);
                $sql[] = $tmpsql;
                $params = array_merge($params, $tmpparams);
            }
            return ['('.implode(' OR ', $sql).')', $params];
        }
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @param string $field field name
     * @return array sql string and $params
     */
    public function internal_get_sql_filter($data, $field) {
        global $DB;
        static $counter = 0;
        $name = 'ex_multitext'.$counter++;

        $operator = $data['operator'];
        $value    = $data['value'];

        $params = [];

        if ($operator != 5 && $value === '') {
            return '';
        }

        switch($operator) {
            case 0: // Contains.
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 1: // Does not contain.
                $res = $DB->sql_like($field, ":$name", false, false, true);
                $params[$name] = "%$value%";
                break;
            case 2: // Equal to.
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "$value";
                break;
            case 3: // Starts with.
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "$value%";
                break;
            case 4: // Ends with.
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "%$value";
                break;
            case 5: // Empty.
                $res = "$field = :$name";
                $params[$name] = '';
                break;
            default:
                return '';
        }
        return [$res, $params];
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
        $a->label    = $this->label;
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
