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
 * Coupon code generator
 *
 * File         generator.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon;
defined('MOODLE_INTERNAL') || die();

use block_coupon\coupon\codegenerator;
use block_coupon\coupon\icoupongenerator;
use block_coupon\exception;

/**
 * block_coupon\coupon\generator
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator implements icoupongenerator {
    /**
     * Courses (each element has id, fullname)
     * @var array
     */
    protected $courses;
    /**
     * Groups (each element has id, name)
     * @var array
     */
    protected $groups;
    /**
     * Cohorts (each element has id, name)
     * @var array
     */
    protected $cohorts;

    /**
     * list of errors messages
     * @var array
     */
    protected $errors;
    /**
     * list of generated coupon ids
     * @var array
     */
    protected $generatorids;
    /**
     * list of generated coupon codes
     * @var array
     */
    protected $generatorcodes;

    /**
     * Get errors
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * get the generated coupon IDs
     * @return array
     */
    public function get_generated_couponids() {
        return $this->generatorids;
    }

    /**
     * get the generated coupon codes
     * @return array
     */
    public function get_generated_couponcodes() {
        return $this->generatorcodes;
    }

    /**
     * Generate a batch of coupons
     * @param \block_coupon\test\coupon\generatoroptions $options
     * @return bool
     */
    public function generate_coupons(generatoroptions $options) {
        $this->generatorids = array();
        $this->generatorcodes = array();
        // First, correct options.
        $this->fix_options($options);
        // Validate options.
        $this->validate_options($options);
        // And generate.
        return $this->generate($options);
    }

    /**
     * Fix generator options
     * @param \block_coupon\test\coupon\generatoroptions $options
     */
    protected function fix_options(generatoroptions $options) {
        global $USER;
        // If we have recipients, amount is the number of recipients.
        if (!empty($options->recipients)) {
            $options->amount = count($options->recipients);
        }
        // If the owner id hasn't been set, the user id will be the owner.
        if (empty($options->ownerid)) {
            $options->ownerid = $USER->id;
        }
        // If options have no batch id, create it.
        if (empty($options->batchid)) {
            $options->batchid = md5(uniqid((string)microtime(true), true));
        }
    }

    /**
     * Validate generator options
     * @param \block_coupon\test\coupon\generatoroptions $options
     * @throws exception
     */
    protected function validate_options(generatoroptions $options) {
        switch ($options->type) {
            case generatoroptions::COURSE:
            case generatoroptions::ENROLEXTENSION:
                if (empty($options->courses)) {
                    throw new exception('err:no-courses');
                }
                // Validate courses.
                $this->validate_courses($options->courses, $options->groups);
                break;

            case generatoroptions::COHORT:
                if (empty($options->cohorts)) {
                    throw new exception('err:no-cohorts');
                }
                // Validate cohorts.
                $this->validate_cohorts($options->cohorts);
                break;

            default:
                throw new exception('err:no-courses');
                break; // Never reached.
        }
        // If we have recipients, we should also have an emailbody.
        if (!empty($options->recipients) && empty($options->emailbody)) {
            throw new exception('error:no-emailbody');
        }
    }

    /**
     * Validate the configured courses (and optional coursegroups)
     *
     * @param array $courseids
     * @param array|null $groupids
     * @throws exception
     */
    protected function validate_courses($courseids, $groupids = null) {
        global $DB;
        // Load courses.
        $this->courses = $DB->get_records_list('course', 'id', $courseids, 'id ASC', 'id, fullname');
        $errors = array();
        foreach ($courseids as $courseid) {
            if (!isset($this->courses[$courseid])) {
                $errors[] = get_string('error:course-not-found', 'block_coupon') . ' (id = ' . $courseid . ')';
            }
        }
        // Groups.
        if (!empty($groupids)) {
            $this->groups = $DB->get_records_list('groups', 'id', $groupids, 'id ASC', 'id, name');
            $errors = array();
            foreach ($groupids as $groupid) {
                if (!isset($this->groups[$groupid])) {
                    $errors[] = get_string('error:group-not-found', 'block_coupon') . ' (id = ' . $groupid . ')';
                }
            }
        }
        // Do we have errors?
        if (!empty($errors)) {
            throw new exception('error:validate-courses', '', implode('<br/>', $errors));
        }
    }

    /**
     * Validate the configured cohorts
     *
     * @param array $cohortids
     * @throws exception
     */
    protected function validate_cohorts($cohortids) {
        global $DB;
        // Load courses.
        $this->cohorts = $DB->get_records_list('cohort', 'id', $cohortids, 'id ASC', 'id, name');
        $errors = array();
        foreach ($cohortids as $cohortid) {
            if (!isset($this->cohorts[$cohortid])) {
                $errors[] = get_string('error:cohort-not-found', 'block_coupon') . ' (id = ' . $cohortid . ')';
            }
        }
        // Do we have errors?
        if (!empty($errors)) {
            throw new exception('error:validate-cohorts', '', implode('<br/>', $errors));
        }
    }

    /**
     * Internally generate the coupons
     *
     * @param \block_coupon\test\coupon\generatoroptions $options
     * @return boolean
     * @throws exception
     */
    protected function generate(generatoroptions $options) {
        global $DB;
        raise_memory_limit(MEMORY_HUGE);
        $errors = array();
        $defaultrole = \block_coupon\helper::get_default_coupon_role();
        $defaultroleid = null;
        if (isset($defaultrole->id)) {
            $defaultroleid = $defaultrole->id;
        }
        $generatortime = time();

        for ($i = 0; $i < $options->amount; $i++) {
            // An object for the coupon itself.
            $objcoupon = new \stdClass();
            $objcoupon->ownerid = $options->ownerid;
            $objcoupon->submission_code = codegenerator::generate_unique_code($options->codesize);
            $objcoupon->timecreated = $generatortime;
            $objcoupon->timeexpired = null;
            $objcoupon->email_body = null;
            $objcoupon->userid = null;
            $objcoupon->issend = 0;
            $objcoupon->senddate = (!empty($options->senddate)) ? $options->senddate : null;
            $objcoupon->enrolperiod = (int)$options->enrolperiod;
            $objcoupon->redirect_url = (!empty($options->redirecturl)) ? $options->redirecturl : null;
            $objcoupon->logoid = (int)$options->logoid;
            $objcoupon->typ = $options->type;
            $objcoupon->claimed = 0;
            $objcoupon->renderqrcode = ($options->renderqrcode) ? 1 : 0;
            $objcoupon->roleid = (!empty($options->roleid)) ? $options->roleid : $defaultroleid;
            if (!empty($options->extendusers[$i])) {
                $objcoupon->userid = $options->extendusers[$i];
            }
            // Add batch id.
            $objcoupon->batchid = $options->batchid;

            // If coupons are personal, set recipient data.
            if (!empty($options->recipients)) {
                $recipient = $options->recipients[$i];
                $objcoupon->for_user_email = clean_param(trim($recipient->email), PARAM_EMAIL);
                $objcoupon->for_user_name = clean_param(trim($recipient->name), PARAM_TEXT);
                $objcoupon->for_user_gender = clean_param(trim($recipient->gender), PARAM_TEXT);
                // Set email body.
                $objcoupon->email_body = $this->generate_email($options->emailbody, $objcoupon);
            }

            // Insert coupon so we've got an id.
            if (!$objcoupon->id = $DB->insert_record('block_coupon', $objcoupon)) {
                $errors[] = 'Failed to create general coupon object in database.';
                continue;
            }
            // Add generated ID.
            $this->generatorids[] = $objcoupon->id;
            $this->generatorcodes[] = $objcoupon->submission_code;

            // Insert extra data depending on generator type.
            $inserterrors = array();
            $result = true;
            switch ($options->type) {
                case generatoroptions::COURSE:
                case generatoroptions::ENROLEXTENSION:
                    $result = $this->insert_coupon_courses($objcoupon, $inserterrors);
                    break;

                case generatoroptions::COHORT:
                    $result = $this->insert_coupon_cohorts($objcoupon, $inserterrors);
                    break;

                default:
                    // Should never happen due to earlier checks.
                    $errors[] = "Invalid generator type '{$options->type}'.";
                    break; // Never reached.
            }
            if (!$result) {
                $errors = array_merge($errors, $inserterrors);
            }
        }

        if (!empty($errors)) {
            throw new exception('error:coupon:generator', '', implode('<br/>', $errors));
        }
        return true;
    }

    /**
     * Generate the personalized email
     * @param string $template
     * @param \stdClass $coupon coupon record
     * @return string
     */
    protected function generate_email($template, $coupon) {
        global $SITE;
        $gendertxt = (!is_null($coupon->for_user_gender)) ? $coupon->for_user_gender : '';
        $extensionperiod = '';
        if ($coupon->typ == generatoroptions::ENROLEXTENSION) {
            if (empty($coupon->enrolperiod)) {
                $extensionperiod = get_string('enrolperiod:indefinite', 'block_coupon');
            } else {
                $extensionperiod = get_string('enrolperiod:extension', 'block_coupon', format_time($coupon->enrolperiod));
            }
        }

        // Replace some strings in the email body.
        $arrreplace = array(
            '##to_name##',
            '##site_name##',
            '##to_gender##',
            '##extensionperiod##',
            '##submission_code##',
        );
        $arrwith = array(
            $coupon->for_user_name,
            $SITE->fullname,
            $gendertxt,
            $extensionperiod,
            $coupon->submission_code
        );

        // Check if we're generating based on course, in which case we enter the course name too.
        if (isset($this->courses) && !empty($this->courses)) {

            $coursenames = array();
            foreach ($this->courses as $course) {
                $coursenames[] = $course->fullname;
            }

            $arrreplace[] = '##course_fullnames##';
            $arrwith[] = implode('<br/>', $coursenames);
        }

        return str_replace($arrreplace, $arrwith, $template);
    }

    /**
     * Insert coupon links for courses and optional coursegroups
     *
     * @param \stdClass $coupon coupon record
     * @param array $errors
     * @return bool true if valid, false if there's errors
     */
    protected function insert_coupon_courses($coupon, &$errors) {
        global $DB;
        $errors = array();
        foreach ($this->courses as $course) {
            // An object for each added course.
            $record = (object) array(
                'couponid' => $coupon->id,
                'courseid' => $course->id
            );
            // And insert in db.
            if (!$DB->insert_record('block_coupon_courses', $record)) {
                $errors[] = 'Failed to create course link ' . $course->id . ' record for coupon id ' . $coupon->id . '.';
            }
        }
        if (!empty($this->groups)) {
            foreach ($this->groups as $group) {
                // An object for each added cohort.
                $record = (object) array(
                    'couponid' => $coupon->id,
                    'groupid' => $group->id
                );
                // And insert in db.
                if (!$DB->insert_record('block_coupon_groups', $record)) {
                    $errors[] = 'Failed to create group link ' . $group->id . ' record for coupon id ' . $coupon->id . '.';
                }
            }
        }
        return !empty($errors);
    }

    /**
     * Insert coupon links for cohorts
     *
     * @param \stdClass $coupon coupon record
     * @param array $errors
     * @return bool true if valid, false if there's errors
     */
    protected function insert_coupon_cohorts($coupon, &$errors) {
        global $DB;
        $errors = array();
        foreach ($this->cohorts as $cohort) {
            // An object for each added cohort.
            $record = (object) array(
                'couponid' => $coupon->id,
                'cohortid' => $cohort->id
            );
            // And insert in db.
            if (!$DB->insert_record('block_coupon_cohorts', $record)) {
                $errors[] = 'Failed to create cohort link ' . $cohort->id . ' record for coupon id ' . $coupon->id . '.';
            }
        }
        return !empty($errors);
    }

}
