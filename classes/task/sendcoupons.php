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
        global $CFG, $DB;
        require_once($CFG->dirroot . '/blocks/coupon/classes/settings.php');
        // Call coupons.
        $coupons = helper::get_coupons_to_send();

        if (!$coupons || empty($coupons)) {
            return;
        }

        // Omdat we geen koppeltabel hebben...
        $sentcoupons = array();
        $couponsend = time(); // Dit moet even om ervoor te zorgen dat dingen per owner gegroepeerd worden.
        // Let op: dit verkloot meerdere batches per owner - Sebastian dd 2014-03-19.
        foreach ($coupons as $coupon) {
            // Check if we have an owner.
            if (!is_null($coupon->ownerid)) {
                // And add to sentCoupons so we can check if all of them have been sent.
                if (!isset($sentcoupons[$coupon->ownerid])) {
                    $sentcoupons[$coupon->ownerid] = array();
                }
                if (!in_array($coupon->timecreated, $sentcoupons[$coupon->ownerid])) {
                    $sentcoupons[$coupon->ownerid][] = $couponsend;
                }
            }

            $result = helper::mail_coupons(array($coupon), $coupon->for_user_email, null, $coupon->email_body, true);

            if ($result !== false) {
                $coupon->issend = true;
                $coupon->timemodified = time();
                $DB->update_record('block_coupon', $coupon);
            }
        }

        // Check if all coupons have been send.
        if (!empty($sentcoupons)) {
            foreach ($sentcoupons as $ownerid => $coupons) {
                foreach ($coupons as $coupontimecreated) {
                    if (helper::has_sent_all_coupons($ownerid, $coupontimecreated)) {
                        // Mail confirmation.
                        helper::confirm_coupons_sent($ownerid, $coupontimecreated);
                    }
                }
            }
        }
    }

}