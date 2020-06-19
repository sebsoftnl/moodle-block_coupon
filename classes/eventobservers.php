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
 * Event observers implementation
 *
 * File         eventobservers.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\eventobservers
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eventobservers
{

    /**
     * Handle course deleted event
     *
     * @param \core\event\course_deleted $event
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;
        $couponids = $DB->get_fieldset_select('block_coupon_courses', 'DISTINCT couponid', 'courseid = ?', array($event->objectid));
        if (empty($couponids)) {
            return;
        }
        $DB->delete_records('block_coupon_courses', array('courseid' => $event->objectid));

        list($insql, $params) = $DB->get_in_or_equal($couponids);
        $remainingcouponids = $DB->get_fieldset_select('block_coupon_courses', 'DISTINCT couponid', 'couponid '.$insql, $params);

        $deletecouponids = array_diff($couponids, $remainingcouponids);
        $DB->delete_records_list('block_coupon', 'id ', $deletecouponids);
        $DB->delete_records_list('block_coupon_groups', 'couponid', $deletecouponids);
    }

    /**
     * Handle cohort deleted event
     *
     * @param \core\event\cohort_deleted $event
     */
    public static function cohort_deleted(\core\event\cohort_deleted $event) {
        global $DB;
        $couponids = $DB->get_fieldset_select('block_coupon_cohorts', 'DISTINCT couponid', 'cohortid = ?', array($event->objectid));
        if (empty($couponids)) {
            return;
        }
        $DB->delete_records('block_coupon_cohorts', array('cohortid' => $event->objectid));

        list($insql, $params) = $DB->get_in_or_equal($couponids);
        $remainingcouponids = $DB->get_fieldset_select('block_coupon_cohorts', 'DISTINCT couponid', 'couponid '.$insql, $params);

        $deletecouponids = array_diff($couponids, $remainingcouponids);
        $DB->delete_records_list('block_coupon', 'id ', $deletecouponids);
        $DB->delete_records_list('block_coupon_groups', 'couponid', $deletecouponids);
    }

    /**
     * Handle user deleted event
     *
     * @param \core\event\user_deleted $event
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;
        $DB->delete_records('block_coupon_rusers', array('userid' => $event->objectid));
        $DB->delete_records('block_coupon_requests', array('userid' => $event->objectid));
    }

}
