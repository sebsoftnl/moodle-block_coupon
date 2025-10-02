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
 * File         course.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon\types;

use block_coupon\coupon\icoupontype;
use block_coupon\coupon\typebase;
use block_coupon\exception;
use block_coupon\notificationexception;
use block_coupon\helper;

/**
 * block_coupon\coupon\types\course
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends typebase implements icoupontype {

    /**
     * CFW_STD
     */
    const CWF_STD = 1;
    /**
     * CFW_EXT
     */
    const CWF_EXT = 2;

    /**
     * @var stdClass
     */
    protected $config;

    /**
     * @var int
     */
    protected $workflow;

    /**
     * Constructor
     *
     * @param stdClass $coupon
     */
    public function __construct($coupon) {
        parent::__construct($coupon);
        $this->config = get_config('block_coupon');
        $this->workflow = intval($this->config->claimworkflow ?? static::CWF_STD);
    }

    /**
     * Claim coupon.
     * @param int $foruserid user that claims coupon. Current userid if not given.
     * @param mixed $options any options required by the instance
     */
    public function claim($foruserid = null, $options = null) {
        global $CFG, $DB, $USER;

        // Because we're outside course context we've got to include libraries manually.
        require_once($CFG->dirroot . '/group/lib.php');

        // Validate.
        if ($this->coupon->typ !== \block_coupon\coupon\generatoroptions::COURSE) {
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
        $couponcourses = $DB->get_records('block_coupon_courses', ['couponid' => $this->coupon->id]);
        // Set enrolment period.
        $endenrolment = 0;
        if (!is_null($this->coupon->enrolperiod) && $this->coupon->enrolperiod > 0) {
            $endenrolment = time() + $this->coupon->enrolperiod;
        }

        foreach ($couponcourses as $couponcourse) {
            // Make sure we only enrol if its not enrolled yet.
            $context = \context_course::instance($couponcourse->courseid);
            if (is_null($context) || $context === false) {
                throw new exception('error:course-not-found');
            }

            // Look up enrolment end date.
            $hasenrolment = is_enrolled($context, $foruserid, '', false);
            $hasactiveenrolment = is_enrolled($context, $foruserid, '', true);
            $reactivatesuspended = $this->config->ccactivatesuspended ?? true;
            $updateactiveenrolments = $this->config->ccupdateactiveenrolments ?? true;

            // Update conditions.
            $doupdate = ($hasenrolment && !$hasactiveenrolment && $reactivatesuspended) ||
                    ($hasactiveenrolment && $updateactiveenrolments) ||
                    !$hasenrolment;

            // Do settings dictate we re-activate suspended enrolments?
            if ($doupdate) {
                $newstatus = $reactivatesuspended ? ENROL_USER_ACTIVE : null; // Value "null" means keep "as is".
                if (!helper::enrol_try_internal_enrol($couponcourse->courseid, $foruserid,
                        $role->id, time(), $endenrolment, $newstatus)) {
                    throw new exception('error:unable_to_enrol');
                }
                // Mark the context for cache refresh.
                $context->mark_dirty();
                remove_temp_course_roles($context);
            }
        }

        // And add user to groups.
        $coupongroups = $DB->get_records('block_coupon_groups', ['couponid' => $this->coupon->id]);
        if (!empty($coupongroups)) {
            foreach ($coupongroups as $coupongroup) {
                // Check if the group exists.
                if (!$DB->get_record('groups', ['id' => $coupongroup->groupid])) {
                    throw new exception('error:missing_group');
                }
                // Add user if its not a member yet.
                if (!groups_is_member($coupongroup->groupid, $foruserid)) {
                    groups_add_member($coupongroup->groupid, $foruserid);
                }
            }
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
     * Assert claimable.
     * @throws exception
     */
    public function assert_not_claimed() {
        global $DB, $USER;
        switch ($this->workflow) {
            case 1:
                // Call parent.
                parent::assert_not_claimed();
                // Specialized.
                if (!is_null($this->coupon->userid)) {
                    throw new exception('error:coupon_already_used');
                }
                break;

            case 2:
                // Special workflow.
                if ((bool)$this->coupon->claimed) {
                    if ($USER->id != $this->coupon->userid) {
                        // Current claiming user !== referenced user.
                        throw new notificationexception('error:coupon_already_used_other', 'error');
                    } else {
                        throw new notificationexception('error:coupon_already_used_self', 'error');
                    }
                }
                break;
        }
    }

    /**
     * Assert other. This can be anything really.
     *
     * @param int $userid user claiming.
     * @throws exception
     */
    public function assert_internal_checks($userid) {
        global $DB;
        switch ($this->workflow) {
            case static::CWF_STD:
                // Assert we have at least ONE course we can sign up to..
                $couponcourses = $DB->get_records('block_coupon_courses', ['couponid' => $this->coupon->id]);
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
                break;

            case static::CWF_EXT:
                // We get here. Let's see if we're already enrolled to ALL courses in this coupon.
                $couponcourses = $DB->get_records('block_coupon_courses', ['couponid' => $this->coupon->id]);
                $allenrolled = true;
                $anyenrolled = false;
                $allactivelyenrolled = true;
                $anyactivelyenrolled = false;
                $courselist = [];
                $suspendedcourselist = [];
                foreach ($couponcourses as $couponcourse) {
                    // Make sure we only enrol if its not enrolled yet.
                    $context = \context_course::instance($couponcourse->courseid);
                    if (is_null($context) || $context === false) {
                        throw new exception('error:course-not-found');
                    }

                    $enrolled = is_enrolled($context, $userid, '', false);
                    $allenrolled = $allenrolled && $enrolled;
                    $anyenrolled = $anyenrolled || $enrolled;

                    $activelyenrolled = is_enrolled($context, $userid, '', true);
                    $allactivelyenrolled = $allactivelyenrolled && $activelyenrolled;
                    $anyactivelyenrolled = $anyactivelyenrolled || $activelyenrolled;

                    $course = get_course($couponcourse->courseid);
                    $coursedisplayname = $this->get_coursename_display($course, $context);

                    if ($enrolled && $activelyenrolled) {
                        $courselist[] = $this->get_coursename_display($course, $context);
                    } else if ($enrolled && !$activelyenrolled) {
                        $suspendedcourselist[] = $this->get_coursename_display($course, $context);

                    }
                }

                // Now perform checks.
                // If all actively enrolled, this is a no brainer.
                if ($allenrolled && $allactivelyenrolled) {
                    $a = '<ul><li>' . implode('</li><li>', $courselist) . '</li></ul>';
                    throw new notificationexception('notify:already_enrolled_in_course_list', 'danger', $a);
                }

                // NOT enrolled in all courses, are we configured to abort on _any_ enrolment?
                $abortifanyenrolled = (bool)($this->config->wf2abortifanyenrolled ?? false);
                if ($anyenrolled && $abortifanyenrolled) {
                    $a = '<ul><li>' . implode('</li><li>', $courselist) . '</li></ul>';
                    throw new notificationexception('notify:already_enrolled_in_course_list', 'danger', $a);
                }

                $reactivatesuspended = (bool)($this->config->ccactivatesuspended ?? true);
                // Enrolled in all courses, not all are active enrolments.
                if ($allenrolled && !$allactivelyenrolled) {
                    // If we do NOT reactivate suspended enrolments, throw a notification.
                    if (!$reactivatesuspended) {
                        $a = (object)[
                            'activecourses' => '<ul><li>' . implode('</li><li>', $courselist) . '</li></ul>',
                            'suspendedcourses' => '<ul><li>' . implode('</li><li>', $suspendedcourselist) . '</li></ul>',
                        ];
                        if (empty($courselist)) {
                            throw new notificationexception('notify:already_enrolled_in_course_list_all_suspended', 'danger', $a);
                        } else {
                            throw new notificationexception('notify:already_enrolled_in_course_list_with_suspended', 'danger', $a);
                        }
                    }
                }

                break;
        }
    }

    /**
     * Get course displayname according to settings
     *
     * @param stdClass $course
     * @param \context_course||null $context
     * @return string
     */
    protected function get_coursename_display($course, $context = null) {
        if ($context == null) {
            $context = \copntext_course::instance($course->id);
        }

        $dfield = $this->config->coursedisplay ?? 'fullname';
        $addidnum = $this->config->coursenameappendidnumber ?? false;

        $name = format_string($course->{$dfield}, true, $context);
        if ($addidnum && !empty($course->idnumber)) {
            $name .= " ($course->idnumber)";
        }

        return $name;
    }

}
