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
 * Course type coupon processor
 *
 * File         enrolext.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon\types;

defined('MOODLE_INTERNAL') || die();

use block_coupon\coupon\icoupontype;
use block_coupon\coupon\typebase;
use block_coupon\exception;

/**
 * block_coupon\coupon\types\enrolext
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolext extends typebase implements icoupontype {

    /**
     * Claim coupon.
     * @param int $foruserid user that claims coupon. Current userid if not given.
     * @param mixed $options any options required by the instance
     */
    public function claim($foruserid = null, $options = null) {
        global $DB, $USER;

        // Validate.
        if ($this->coupon->typ !== \block_coupon\coupon\generatoroptions::ENROLEXTENSION) {
            throw new exception('invalid-coupon-type');
        }
        // Sanity checks (stolen from enrol_try_internal_enrol()).
        if (!enrol_is_enabled('manual')) {
            return false;
        }
        if (!$enrol = enrol_get_plugin('manual')) {
            return false;
        }

        // Claim.
        if (empty($foruserid)) {
            $foruserid = $USER->id;
        }

        // Validate correct user, if applicable.
        if (!empty($this->coupon->userid) && $this->coupon->userid != $foruserid) {
            throw new exception('coupon:claim:wronguser', 'block_coupon');
        }

        $couponcourses = $DB->get_records('block_coupon_courses', array('couponid' => $this->coupon->id));
        foreach ($couponcourses as $couponcourse) {
            // Make sure we only enrol if its not enrolled yet.
            $context = \context_course::instance($couponcourse->courseid);
            if (is_null($context) || $context === false) {
                throw new exception('error:course-not-found');
            }
            if (!is_enrolled($context, $foruserid)) {
                continue;
            }
            // Now we can update enrolment.
            if (!$instances = $DB->get_records('enrol', array('enrol' => 'manual',
                'courseid' => $couponcourse->courseid, 'status' => ENROL_INSTANCE_ENABLED), 'sortorder,id ASC')) {
                return false;
            }
            $instance = reset($instances);
            // Check if we HAVE an enrolment here, otherwise we will not do anything.
            $existing = $DB->get_record('user_enrolments', array('enrolid' => $instance->id, 'userid' => $foruserid));
            if (empty($existing)) {
                throw new exception('user-not-enrolled');
            }

            $endenrolment = 0;
            if (!is_null($this->coupon->enrolperiod) && $this->coupon->enrolperiod > 0) {
                $endenrolment = $existing->timeend + $this->coupon->enrolperiod;
            }
            // This takes care of updates as well.
            $enrol->enrol_user($instance, $foruserid, null, $existing->timestart, $endenrolment, ENROL_USER_ACTIVE);

            // Mark the context for cache refresh.
            $context->mark_dirty();
            remove_temp_course_roles($context);
        }

        // And finally update the coupon record.
        $this->coupon->claimed = 1;
        $this->coupon->userid = $foruserid;
        $time = time();
        $this->coupon->timemodified = $time;
        $this->coupon->timeclaimed = $time;
        $DB->update_record('block_coupon', $this->coupon);
    }

    /**
     * Return whether this coupon type has extended claim options.
     * @return bool false.
     */
    public function has_extended_claim_options() {
        return false;
    }

    /**
     * Assert other. This can be anything really.
     *
     * @param int $userid user claiming.
     * @throws exception
     */
    public function assert_internal_checks($userid) {
        global $DB;
        // Assert we have at least ONE course we can sign up to..
        $couponcourses = $DB->get_records('block_coupon_courses', array('couponid' => $this->coupon->id));
        $cansignup = false;
        foreach ($couponcourses as $couponcourse) {
            $ee = enrol_get_enrolment_end($couponcourse->courseid, $userid);
            if ($ee === false) {
                $cansignup = true;
            }
        }
        if (!$cansignup) {
            throw new exception('error:already-enrolled-in-courses');
        }
    }

}
