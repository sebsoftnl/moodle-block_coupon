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
 * Task class implementation for cleaning up coupons
 *
 * File         cleanup.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon\task;

defined('MOODLE_INTERNAL') || die();

use block_coupon\helper;

/**
 * block_coupon\task\cleanup
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup extends \core\task\scheduled_task {

    /**
     * Return the localised name for this task
     *
     * @return string task name
     */
    public function get_name() {
        return get_string('task:cleanup', 'block_coupon');
    }

    /**
     * Executes the task
     *
     * @return void
     */
    public function execute() {
        global $DB;
        $config = get_config('block_coupon');
        $couponids = array();
        if ((bool)$config->enablecleanup) {
            $timecheck = time() - $config->cleanupage;
            // Remove unused coupons older than xxx.
            $couponids = array_merge($couponids, $DB->get_fieldset_select('block_coupon', 'id',
                    'userid IS NULL AND timecreated < ? AND (timeexpired IS NULL OR timeexpired = 0)', array($timecheck)));
        }
        // Now clean up expired coupons.
        $couponids = array_merge($couponids, $DB->get_fieldset_select('block_coupon', 'id',
                'userid IS NULL AND timeexpired IS NOT NULL AND timeexpired < ?', array(time())));
        if (!empty($couponids)) {
            // Delegated transaction to ensure everything is removed.
            $transaction = $DB->start_delegated_transaction();
            $DB->delete_records_list('block_coupon', 'id', $couponids);
            $DB->delete_records_list('block_coupon_cohorts', 'couponid', $couponids);
            $DB->delete_records_list('block_coupon_groups', 'couponid', $couponids);
            $DB->delete_records_list('block_coupon_courses', 'couponid', $couponids);
            $DB->delete_records_list('block_coupon_errors', 'couponid', $couponids);
            $DB->commit_delegated_transaction($transaction);
        }
        // Standard cleaning, just to be sure.
        helper::cleanup_invalid_links();
    }

}
