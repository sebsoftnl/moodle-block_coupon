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
 * This file contains the form for handling editing a template element.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tool;

/**
 * The form for handling editing a template element.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class voucherimporter {

    /**
     * Processing errors, if any
     *
     * @var array
     */
    protected static $errors;

    /**
     * Process  full import.
     *
     * @return bool
     */
    public static function process() {
        if (!static::can_process()) {
            return false;
        }
        static::$errors = [];

        // Import vouchers.
        static::import_vouchers();

        // Import email templates.
        static::import_email_templates();

        // Import pdf templates.
        static::import_templates();

        return (count(static::$errors) === 0);
    }

    /**
     * Test if we can import (aka: if voucher block exists)
     *
     * @return bool
     */
    protected static function can_process() {
        $p = \core_plugin_manager::instance()->get_plugin_info('block_voucher');
        return ($p !== null);
    }

    /**
     * Import email templates
     */
    protected static function import_email_templates() {
        global $DB;

        $dataset = $DB->get_recordset('vouchers_mailtemplate');
        foreach ($dataset as $template) {
            $record = (object)[
                'name' => $template->name,
                'subject' => '',
                'body' => $template->body,
                'bodyformat' => 1,
                'usercreated' => $template->createdby,
                'timecreated' => $template->timecreated,
                'timemodified' => $template->timecreated,
            ];

            $DB->insert_record('block_coupon_mailtemplates', $record);
        }
        $dataset->close();
    }

    /**
     * Import email templates
     */
    protected static function import_templates() {
        global $DB;

        $dataset = $DB->get_recordset('vouchers_templates');
        foreach ($dataset as $template) {
            $record = clone $template;
            unset($record->id);

            $record->id = $DB->insert_record('block_coupon_templates', $record);

            // Import pages.
            static::import_template_pages($template->id, $record->id);
        }
        $dataset->close();
    }

    /**
     * Import template pages
     *
     * @param int $remoteid
     * @param int $localid
     */
    protected static function import_template_pages($remoteid, $localid) {
        global $DB;
        $records = $DB->get_records('vouchers_pages', ['templateid' => $remoteid]);
        foreach ($records as $record) {
            $page = clone $record;
            unset($page->id);
            $page->templateid = $localid;

            $page->id = $DB->insert_record('block_coupon_pages', $page);

            // And import the elements.
            static::import_page_elements($record->id, $page->id);
        }
    }

    /**
     * Import page elements
     *
     * @param int $remoteid
     * @param int $localid
     */
    protected static function import_page_elements($remoteid, $localid) {
        global $DB;
        $records = $DB->get_records('vouchers_elements', ['pageid' => $remoteid]);
        foreach ($records as $record) {
            $page = clone $record;
            unset($page->id);
            $page->pageid = $localid;

            $page->id = $DB->insert_record('block_coupon_elements', $page);
        }
    }

    /**
     * Import vouchers
     */
    protected static function import_vouchers() {
        global $DB;

        $cmvoucherids = static::load_activity_voucherids();
        $cohortvoucherids = static::load_cohort_voucherids();
        $coursevoucherids = static::load_course_voucherids();
        $groupsvoucherids = static::load_group_voucherids();

        $dataset = $DB->get_recordset('vouchers');
        foreach ($dataset as $voucher) {
            try {
                $coupon = (object)[
                    'id' => $voucher->id,
                    'userid' => $voucher->userid,
                    'idowner' => $voucher->ownerid,
                    'for_user_email' => $voucher->for_user_email,
                    'for_user_name' => $voucher->for_user_name,
                    'for_user_gender' => $voucher->for_user_gender,
                    'enrolperiod' => $voucher->enrolperiod,
                    'senddate' => $voucher->senddate,
                    'issend' => $voucher->issend,
                    'redirect_url' => $voucher->redirect_url,
                    'email_body' => $voucher->email_body,
                    'submission_code' => $voucher->submission_code,
                    'typ' => '',
                    'claimed' => 0,
                    'renderqrcode' => 0,
                    'roleid' => null,
                    'batchid' => md5("{$voucher->id}{$voucher->timecreated}"),
                    'timecreated' => $voucher->timecreated,
                    'timemodified' => $voucher->timemodified,
                    'timeexpired' => $voucher->timeexpired,
                    'timeclaimed' => null
                ];

                // Type.
                if (in_array($voucher->id, $coursevoucherids)) {
                    $coupon->typ = 'course';
                } else if (in_array($voucher->id, $cohortvoucherids)) {
                    $coupon->typ = 'cohort';
                } else if (in_array($voucher->id, $cmvoucherids)) {
                    $coupon->typ = 'activity';
                } else {
                    $coupon->typ = 'unknown';
                }

                // Claimed status (assumption).
                if (!empty($voucher->userid) && !empty($voucher->timemodified)) {
                    $coupon->claimed = 1;
                    $coupon->timeclaimed = $voucher->timemodified;
                }

                // Insert coupon.
                $coupon->id = $DB->insert_record('block_coupon', $coupon);
                // Link data.
                switch ($coupon->typ) {
                    case 'course':
                        // Inject course links.
                        static::import_course_links($voucher->id, $coupon->id);
                        break;

                    case 'cohort':
                        // Inject cohort links.
                        static::import_cohort_links($voucher->id, $coupon->id);
                        break;

                    case 'activity':
                        // Inject activity links.
                        static::import_activity_links($voucher->id, $coupon->id);
                        break;

                    case 'unknown':
                    default:
                        static::$errors[] = "Voucher type with ID {$voucher->id} translated to 'unknown': not linking records";
                }

            } catch (\Exception $e) {
                static::$errors[] = "Voucher error for ID {$voucher->id}: " . $e->getMessage();
            }
        }
        $dataset->close();
    }

    /**
     * Load voucherids for activity linked data
     *
     * @return array
     */
    protected static function load_activity_voucherids() {
        global $DB;
        return $DB->get_fieldset_sql('SELECT DISTINCT voucherid FROM {vouchers_activity}');
    }

    /**
     * Load voucherids for cohort linked data
     *
     * @return array
     */
    protected static function load_cohort_voucherids() {
        global $DB;
        return $DB->get_fieldset_sql('SELECT DISTINCT voucherid FROM {vouchers_cohorts}');
    }

    /**
     * Load voucherids for group linked data
     *
     * @return array
     */
    protected static function load_group_voucherids() {
        global $DB;
        return $DB->get_fieldset_sql('SELECT DISTINCT voucherid FROM {vouchers_groups}');
    }

    /**
     * Load voucherids for course linked data
     *
     * @return array
     */
    protected static function load_course_voucherids() {
        global $DB;
        return $DB->get_fieldset_sql('SELECT DISTINCT voucherid FROM {vouchers_courses}');
    }

    /**
     * Import course linked data
     *
     * @param int $voucherid
     * @param int $couponid
     */
    protected static function import_course_links($voucherid, $couponid) {
        global $DB;

        $records = $DB->get_records('voucher_courses', ['voucherid' => $voucherid]);
        foreach ($records as $record) {
            $link = (object)[
                'couponid' => $couponid,
                'courseid' => $record->courseid
            ];
            $DB->insert_record('block_coupon_courses', $link);
        }

        $records = $DB->get_records('voucher_groups', ['voucherid' => $voucherid]);
        foreach ($records as $record) {
            $link = (object)[
                'couponid' => $couponid,
                'groupid' => $record->groupid
            ];
            $DB->insert_record('block_coupon_groups', $link);
        }
    }

    /**
     * Import cohort linked data
     *
     * @param int $voucherid
     * @param int $couponid
     */
    protected static function import_cohort_links($voucherid, $couponid) {
        global $DB;
        $records = $DB->get_records('voucher_cohorts', ['voucherid' => $voucherid]);
        foreach ($records as $record) {
            $link = (object)[
                'couponid' => $couponid,
                'cohortid' => $record->cohortid
            ];
            $DB->insert_record('block_coupon_cohorts', $link);
        }
    }

    /**
     * Import activity linked data
     *
     * @param int $voucherid
     * @param int $couponid
     */
    protected static function import_activity_links($voucherid, $couponid) {
        global $DB;
        $records = $DB->get_records('vouchers_activity', ['voucherid' => $voucherid]);
        foreach ($records as $record) {
            $link = (object)[
                'couponid' => $couponid,
                'cmid' => $record->cmid
            ];
            $DB->insert_record('block_coupon_activities', $link);
        }
    }

}
