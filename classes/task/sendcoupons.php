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
 * Task class implementation for sending coupons
 *
 * File         sendcoupons.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon\task;

defined('MOODLE_INTERNAL') || die();

use block_coupon\helper;

/**
 * block_coupon\task\sendcoupons
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sendcoupons extends \core\task\scheduled_task {

    /**
     * Return the localised name for this task
     *
     * @return string task name
     */
    public function get_name() {
        return get_string('task:sendcoupons', 'block_coupon');
    }

    /**
     * Executes the task
     *
     * @return void
     */
    public function execute() {
        global $DB;
        $time = $this->get_next_run_time();
        if (empty($time)) {
            $time = time();
        }
        // Find batches.
        switch ($DB->get_dbfamily()) {
            case 'oracle':
                // Thanks goes out to: Wade Colclough from Zuken Limited and their team.
                $sql = "SELECT batchid FROM(
                        SELECT batchid FROM {block_coupon}
                        WHERE senddate < ? AND issend = 0 AND for_user_email IS NOT NULL
                        ORDER BY timecreated ASC
                    ) where ROWNUM = 1";
                break;
            default:
                $sql = "SELECT batchid FROM {block_coupon}
                    WHERE senddate < ? AND issend = 0 AND for_user_email IS NOT NULL
                    ORDER BY timecreated ASC
                    LIMIT 1";
                break;
        }
        $batchid = $DB->get_field_sql($sql, [$time]);
        if (empty($batchid)) {
            mtrace("No batches found");
            return;
        }

        // Load coupons for batch.
        $sql = "SELECT * FROM {block_coupon}
            WHERE batchid = ? AND issend = 0 AND for_user_email IS NOT NULL";
        $coupons = $DB->get_records_sql($sql, [$batchid], 0, 500);

        if (!$coupons || empty($coupons)) {
            mtrace("No coupons found for batch {$batchid}");
            return;
        }

        // Find owner for batch.
        $ownerid = $DB->get_field('block_coupon', 'ownerid', ['batchid' => $batchid], IGNORE_MULTIPLE);

        mtrace("SENDING COUPON BATCH (max 500 items | have = ".count($coupons).") {$batchid} WITH OWNER {$ownerid}");

        $this->send_batch($coupons, $batchid, $time);
    }

    /**
     * Send coupon batch.
     *
     * @param array $coupons
     * @param string $batchid
     * @param int $timeexecuted
     */
    protected function send_batch($coupons, $batchid, $timeexecuted) {
        global $DB;
        $ownerid = null;
        foreach ($coupons as $coupon) {
            if (empty($ownerid)) {
                $ownerid = $coupon->ownerid;
            }

            // Send off. All handling is done by the helper method.
            helper::mail_personalized_coupon($coupon);
        }

        // Check batch completed.
        $conditions = array(
            'issend' => 0,
            'ownerid' => $ownerid,
            'batchid' => $batchid
        );
        $batchcomplete = ($DB->count_records('block_coupon', $conditions) === 0);
        if ($batchcomplete) {
            // Mail confirmation.
            mtrace("Send batch completed notification");
            \block_coupon\couponnotification::send_task_notification($ownerid, $batchid, $timeexecuted);
        }
    }

}
