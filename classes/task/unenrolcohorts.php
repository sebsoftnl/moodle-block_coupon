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
 * File         unenrolcohorts.php
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

/**
 * block_coupon\task\unenrolcohorts
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unenrolcohorts extends \core\task\scheduled_task {

    /**
     * Return the localised name for this task
     *
     * @return string task name
     */
    public function get_name() {
        return get_string('task:unenrolcohorts', 'block_coupon');
    }

    /**
     * Executes the task
     *
     * @return void
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/cohort/lib.php');

        $sql = 'SELECT *
                FROM {block_coupon} bc
                WHERE typ = :typ AND claimed = 1
                AND enrolperiod <> 0
                AND (timeclaimed + enrolperiod) < :now';
        $params = ['typ' => 'cohort', 'now' => $this->get_last_run_time()];

        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $coupon) {
            // Fetch cohorts.
            $cohortrefs = $DB->get_records_select('block_coupon_cohorts', 'couponid = ?', [$coupon->id]);
            // Try to remove member.
            foreach ($cohortrefs as $ref) {
                $userid = $coupon->userid;
                $cohortid = $ref->cohortid;
                try {
                    cohort_remove_member($cohortid, $userid);
                } catch (\Exception $ex) {
                    // This is a no-op, it could be that this cohort or member does not exist.
                    mtrace("Failed removing user with ID {$userid} from cohort with ID {$cohortid}: " . $ex->getMessage());
                }
            }
        }
    }
}
