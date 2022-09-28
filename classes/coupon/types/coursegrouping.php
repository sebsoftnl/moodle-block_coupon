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
 * File         coursegrouping.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon\types;

use block_coupon\coupon\icoupontype;
use block_coupon\coupon\typebase;
use block_coupon\exception;

/**
 * block_coupon\coupon\types\coursegrouping
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursegrouping extends typebase implements icoupontype {

    /**
     * Claim coupon.
     *
     * @param int $foruserid user that claims coupon. Current userid if not given.
     * @param array|stdClass $options any extra options
     */
    public function claim($foruserid = null, $options = null) {
        global $CFG, $DB, $USER;

        if (!is_object($options)) {
            $options = (object)$options;
        }

        // Validate.
        if ($this->coupon->typ !== \block_coupon\coupon\generatoroptions::COURSEGROUPING) {
            throw new exception('invalid-coupon-type');
        }
        // Claim.
        if (empty($foruserid)) {
            $foruserid = $USER->id;
        }

        // Validate correct user, if applicable.
        if (!empty($this->coupon->userid) && $this->coupon->userid != $foruserid) {
            throw new exception('coupon:claim:wronguser', 'block_coupon');
        }

        // Determine role.
        if (empty($this->coupon->roleid)) {
            $role = \block_coupon\helper::get_default_coupon_role();
        } else {
            $role = $DB->get_record('role', ['id' => $this->coupon->roleid]);
        }

        $coupongrouping = $DB->get_record('block_coupon_groupings', ['couponid' => $this->coupon->id]);
        $coursegrouping = $DB->get_record('block_coupon_coursegroupings', ['id' => $coupongrouping->coursegroupingid]);
        $groupingcourseids = $DB->get_fieldset_select('block_coupon_cgcourses', 'courseid',
                'coursegroupingid = ?', [$coursegrouping->id]);

        $selectedcourses = $options->courses;
        // Assert choice.
        if (count($selectedcourses) > $coursegrouping->maxamount) {
            throw new exception('err:coupon:coursegrouping:amount', $coursegrouping->maxamount);
        }

        // Assert valid courses!
        $invalidids = array_diff($selectedcourses, $groupingcourseids);
        if (count($invalidids)) {
            throw new exception('err:coupon:coursegrouping:invalid-selection<br/>' .
                    implode(',', $invalidids) . '<br/>' . implode(',', $groupingcourseids));
        }

        // Set enrolment period.
        $endenrolment = 0;
        if (!is_null($this->coupon->enrolperiod) && $this->coupon->enrolperiod > 0) {
            $endenrolment = time() + $this->coupon->enrolperiod;
        }

        foreach ($selectedcourses as $courseid) {
            // Make sure we only enrol if its not enrolled yet.
            $context = \context_course::instance($courseid);
            if (is_null($context) || $context === false) {
                throw new exception('error:course-not-found');
            }
            // Now we can enrol.
            if (!enrol_try_internal_enrol($courseid, $foruserid, $role->id, time(), $endenrolment)) {
                throw new exception('error:unable_to_enrol');
            }
            // Mark the context for cache refresh.
            $context->mark_dirty();
            remove_temp_course_roles($context);
        }

        $time = time();
        // Now connect courses in our user tracking table.
        foreach ($selectedcourses as $courseid) {
            $instance = new \stdClass;
            $instance->couponid = $this->coupon->id;
            $instance->courseid = $courseid;
            $instance->timecreated = $time;
            $instance->timemodified = $time;
            $DB->insert_record('block_coupon_cgucourses', $instance);
        }

        // And finally update the coupon record.
        $this->coupon->claimed = 1;
        $this->coupon->userid = $foruserid;
        $this->coupon->timemodified = $time;
        $this->coupon->timeclaimed = $time;
        $DB->update_record('block_coupon', $this->coupon);
    }

    /**
     * Return whether this coupon type has extended claim options.
     *
     * @return bool false.
     */
    public function has_extended_claim_options() {
        return true;
    }

    /**
     * Process the claim.
     *
     * @param int $foruserid user that claims coupon. Current userid if not given.
     */
    public function process_claim($foruserid = null) {
        global $CFG, $DB;
        $grouping = $DB->get_record('block_coupon_groupings', ['couponid' => $this->coupon->id], '*', MUST_EXIST);
        // User MUST select courses, then we can claim.
        $params = [
            'id' => \block_coupon\helper::find_block_instance_id(),
            'code' => $this->coupon->submission_code,
            'gpid' => $grouping->id // NOT the coursegroupingid! We'll use this to validate later.
        ];
        $redirect = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/selectcourses.php', $params);
        redirect($redirect);
    }

    /**
     * Assert claimable.
     * @throws \block_coupon\exception
     */
    public function assert_not_claimed() {
        if ((bool)$this->coupon->claimed) {
            get_string('error:coupon_already_used', 'block_coupon');
        }
    }

    /**
     * Assert other. This can be anything really.
     *
     * @param int $userid user claiming.
     * @throws exception
     */
    public function assert_internal_checks($userid) {
        return;
    }

}
