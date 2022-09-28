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
 * File         coupons.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tables;

/**
 * block_coupon\tables\coupons
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mycoupons extends coupons {

    /**
     * Define headers and columns.
     */
    protected function define_headers_and_columns() {
        $columns = array(
            'enrolperiod',
            'submission_code',
            'roleid',
            'batchid'
        );
        $headers = array(
            get_string('th:enrolperiod', 'block_coupon'),
            get_string('th:submission_code', 'block_coupon'),
            get_string('th:roleid', 'block_coupon'),
            get_string('th:batchid', 'block_coupon')
        );
        if ($this->is_downloading() == '' &&!$this->noactions) {
            $columns[] = 'action';
            $headers[] = get_string('th:action', 'block_coupon');
        }
        switch ($this->filter) {
            case self::USED:
            case self::PERSONAL:
                array_splice($columns, 0, 0, ['usedby', 'timeclaimed']);
                array_splice($headers, 0, 0, [get_string('th:usedby', 'block_coupon'),
                    get_string('th:claimedon', 'block_coupon')]);
                break;
            default:
                // Has no extra columns.
                break;
        }
        $this->define_columns($columns);
        $this->define_headers($headers);
    }

}
