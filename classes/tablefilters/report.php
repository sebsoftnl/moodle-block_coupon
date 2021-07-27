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
 * this file contains the table filter for the report table
 *
 * File         report.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tablefilters;

defined('MOODLE_INTERNAL') || die();

use \block_coupon\filtering\filtering;

/**
 * block_coupon\tables\report
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends filtering {

    /**
     * Return all default filter names and advanced status
     * @return array
     */
    public function get_fields() {
        return array(
            'timeexpired' => 0,
            'timemodified' => 1,
            'senddate' => 1,
            'sent' => 0,
            'couponcode' => 1,
            'context' => 1,
            'cohortid' => 1,
            'courseid' => 1,
            'course' => 1,
            'cohort' => 1,
        );
    }

    /**
     * Creates known user filter if present
     * @param string $fieldname
     * @param boolean $advanced
     * @return object filter
     */
    public function get_field($fieldname, $advanced) {
        switch ($fieldname) {
            case 'timemodified':
                return new \user_filter_date('timemodified',
                        get_string('lastmodified'), $advanced, 'c.timemodified');
            case 'timeexpired':
                return new \user_filter_date('timeexpired',
                        get_string('report:timeexpired', 'block_coupon'), $advanced, 'c.timeexpired');
            case 'senddate':
                return new \user_filter_date('senddate',
                        get_string('report:senddate', 'block_coupon'), $advanced, 'c.senddate');
            case 'sent':
                return new \user_filter_yesno('sent',
                        get_string('report:issend', 'block_coupon'), $advanced, 'c.issend');
            case 'for_user_email':
                return new \user_filter_text('for_user_email',
                        get_string('report:for_user_email', 'block_coupon'), $advanced, 'c.for_user_email');
            case 'for_user_name':
                return new \user_filter_text('for_user_name',
                        get_string('report:for_user_name', 'block_coupon'), $advanced, 'c.for_user_name');
            case 'couponcode':
                return new \user_filter_text('couponcode',
                        get_string('report:coupon_code', 'block_coupon'), $advanced, 'c.submission_code');
            case 'cohortid':
                return new \block_coupon\filters\couponcohortid($advanced, 'c.id');
            case 'courseid':
                return new \block_coupon\filters\couponcourseid($advanced, 'c.id');
            case 'course':
                return new \block_coupon\filters\couponcourseselect($advanced, 'c.id');
            case 'cohort':
                return new \block_coupon\filters\couponcohortselect($advanced, 'c.id');
            default:
                return null;
        }
    }

}
