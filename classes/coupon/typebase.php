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
 * Coupon type base
 *
 * File         typebase.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\coupon;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\coupon\typebase
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class typebase {

    /**
     * @var \stdClass
     */
    protected $coupon;

    /**
     * Create a new instance
     * @param \stdClass $coupon record from database
     */
    public function __construct($coupon) {
        $this->coupon = $coupon;
    }

    /**
     * Claim coupon.
     * @param int $foruserid user that claims coupon. Current userid if not given.
     */
    abstract public function claim($foruserid = null);

    /**
     * Trigger event that this coupon is claimed.
     */
    protected function trigger_coupon_claimed() {
        // Trigger event.
        $event = \block_coupon\event\coupon_used::create(
                        array(
                            'objectid' => $this->coupon->id,
                            'relateduserid' => $this->coupon->userid,
                            'context' => \context_user::instance($this->coupon->userid),
                            'other' => [
                                'code' => $this->coupon->submission_code,
                                'type' => $this->coupon->typ
                            ]
                        )
        );
        $event->add_record_snapshot('block_coupon', $this->coupon);
        $event->trigger();
    }

}
