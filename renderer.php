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
 * Renderer for the coupon block.
 *
 * File         renderer.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_coupon\helper;
use block_coupon\coupon\generator;
use block_coupon\exception;

/**
 * Renderer for the coupon block.
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_coupon_renderer extends plugin_renderer_base {

    /**
     * Render image upload page (including header / footer).
     *
     * @param int $id block instance id
     * @return string
     */
    public function page_uploadimage($id) {
        global $CFG;
        $out = '';
        // Make sure the moodle editmode is off.
        helper::force_no_editing_mode();

        $mform = new block_coupon\forms\imageupload($this->page->url);

        if ($mform->is_cancelled()) {
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            $fn = $mform->get_new_filename('userfile');
            $file = BLOCK_COUPON_LOGOFILE;
            $saved = $mform->save_file('userfile', $file, true);
            $sizeinfo = getimagesize($file);
            if ($sizeinfo) {
                list($w, $h, $itype, $tagwh) = $sizeinfo;
                $errormargin = 5 / 100; // Have 5% margin.
                $desiredratio = 210 / 297;
                $ratio = $w / $h;
                if ($ratio < ($desiredratio - ($errormargin * $desiredratio)) ||
                        $ratio > ($desiredratio + ($errormargin * $desiredratio))) {
                    @unlink($file);
                    print_error('error:wrong_image_size', 'block_coupon');
                }
            }

            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)),
                    get_string('success:uploadimage', 'block_coupon'));
        } else {
            $out .= $this->header();
            $out .= '<div class="block-coupon-container">';
            $out .= '<div>';
            $out .= $this->get_tabs($this->page->context, 'wzcouponimage', array('id' => $id));
            $out .= '</div>';
            $out .= $mform->render();
            $out .= '</div>';
            $out .= $this->footer();
        }
        return $out;
    }

    /**
     * Render unused coupon page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    public function page_unused_coupons($id, $ownerid = null) {
        // Table instance.
        $table = new \block_coupon\tables\coupons($ownerid);
        $table->baseurl = $this->page->url;
        $table->is_downloadable(true);
        $table->show_download_buttons_at(array(TABLE_P_BOTTOM, TABLE_P_TOP));
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'coupons', 'coupons');
            $table->render(25);
            exit;
        }

        $out = '';
        $out .= $this->header();
        $out .= '<div class="block-coupon-container">';
        $out .= '<div>';
        $out .= $this->get_tabs($this->page->context, 'cpunused', array('id' => $id));
        $out .= '</div>';
        ob_start();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= '</div>';
        $out .= $this->footer();
        return $out;
    }

    /**
     * Render report page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    public function page_report($id, $ownerid = null) {
        // Table instance.
        $table = new \block_coupon\tables\report($ownerid);
        $table->baseurl = $this->page->url;
        $table->is_downloadable(true);
        $table->show_download_buttons_at(array(TABLE_P_BOTTOM, TABLE_P_TOP));
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'couponreport', 'couponreport');
            $table->render(25, true);
            exit;
        }

        $out = '';
        $out .= $this->header();
        $out .= '<div class="block-coupon-container">';
        $out .= '<div>';
        $out .= $this->get_tabs($this->page->context, 'cpreport', array('id' => $id));
        $out .= '</div>';
        ob_start();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= '</div>';
        $out .= $this->footer();
        return $out;
    }

    /**
     * Render coupon generator page 1 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator() {
        global $SESSION;
        // Create form.
        $mform = new block_coupon\forms\coupon\generator($this->page->url);
        $out = '';

        if ($mform->is_cancelled()) {
            unset($SESSION->coupon);
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Cache form input.
            $SESSION->coupon = new stdClass();
            $SESSION->coupon->type = ($data->coupon_type['type'] == 0) ? 'course' : 'cohorts';

            // And redirect user to next page.
            $params = array('id' => $this->page->url->param('id'));
            $redirect = new moodle_url('/blocks/coupon/view/generate_coupon_step_two.php', $params);
            redirect($redirect);
        } else {
            if (isset($SESSION->coupon)) {
                unset($SESSION->coupon);
            }

            $out .= $this->header();
            $out .= '<div class="block-coupon-container">';
            $out .= '<div>';
            $out .= $this->get_tabs($this->page->context, 'wzcoupons', array('id' => $this->page->url->param('id')));
            $out .= '</div>';
            $out .= $mform->render();
            $out .= '</div>';
            $out .= $this->footer();
        }
        return $out;
    }

    /**
     * Render coupon generator page 2 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step2() {
        global $SESSION, $DB;
        // Make sure sessions are still alive.
        if (!isset($SESSION->coupon)) {
            print_error("error:sessions-expired", 'block_coupon');
        }

        // Depending on our data we'll get the right form.
        if ($SESSION->coupon->type == 'course') {
            $mform = new \block_coupon\forms\coupon\generator\selectcourse($this->page->url);
        } else {
            $mform = new \block_coupon\forms\coupon\generator\selectcohorts($this->page->url);
        }
        $out = '';

        if ($mform->is_cancelled()) {
            unset($SESSION->coupon);
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            if ($SESSION->coupon->type == 'course') {
                $SESSION->coupon->courses = $data->coupon_courses;

                $hasgroups = false;
                foreach ($data->coupon_courses as $courseid) {
                    $groups = $DB->get_records("groups", array('courseid' => $courseid));
                    if (count($groups) > 0) {
                        $hasgroups = true;
                    }
                }

                $nextpage = ($hasgroups) ? 'generate_coupon_step_three' : $nextpage = 'generate_coupon_step_four';
            } else {
                $SESSION->coupon->cohorts = $data->coupon_cohorts;
                $nextpage = 'generate_coupon_step_three';
            }

            $params = array('id' => $this->page->url->param('id'));
            $url = new moodle_url('/blocks/coupon/view/' . $nextpage . '.php', $params);
            redirect($url);
        } else {
            $out .= $this->header();
            $out .= '<div class="block-coupon-container">';
            $out .= '<div>';
            $out .= $this->get_tabs($this->page->context, 'wzcoupons', array('id' => $this->page->url->param('id')));
            $out .= '</div>';
            $out .= $mform->render();
            $out .= '</div>';
            $out .= $this->footer();
        }
        return $out;
    }

    /**
     * Render coupon generator page 3 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step3() {
        global $SESSION, $DB;
        // Make sure sessions are still alive.
        if (!isset($SESSION->coupon)) {
            print_error("error:sessions-expired", 'block_coupon');
        }

        // Depending on our data we'll get the right form.
        if ($SESSION->coupon->type == 'course') {
            $mform = new \block_coupon\forms\coupon\generator\selectgroups($this->page->url);
        } else {
            $mform = new block_coupon\forms\coupon\generator\selectcohortcourses($this->page->url);
        }
        $out = '';

        if ($mform->is_cancelled()) {
            unset($SESSION->coupon);
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Save param, its only about course or cohorts.
            if ($SESSION->coupon->type == 'course') {
                // Add selected groups to session.
                if (isset($data->coupon_groups)) {
                    $SESSION->coupon->groups = $data->coupon_groups;
                }
            } else {
                // Check if a course is selected.
                if (isset($data->connect_courses)) {
                    // Get required records.
                    $enrol = enrol_get_plugin('cohort');
                    $role = $DB->get_record('role', array('shortname' => 'student'));
                    // Loop over all cohorts.
                    foreach ($data->connect_courses as $cohortid => $courses) {
                        // Loop over all courses selected for this cohort.
                        foreach ($courses as $courseid) {
                            // And enroll the shizzle.
                            $course = $DB->get_record('course', array('id' => $courseid));
                            $enrol->add_instance($course, array('customint1' => $cohortid, 'roleid' => $role->id));
                        }
                    }
                }
            }
            $params = array('id' => $this->page->url->param('id'));
            $url = new moodle_url('/blocks/coupon/view/generate_coupon_step_four.php', $params);
            redirect($url);
        } else {
            $out .= $this->header();
            $out .= '<div class="block-coupon-container">';
            $out .= '<div>';
            $out .= $this->get_tabs($this->page->context, 'wzcoupons', array('id' => $this->page->url->param('id')));
            $out .= '</div>';
            $out .= $mform->render();
            $out .= '</div>';
            $out .= $this->footer();
        }
        return $out;
    }

    /**
     * Render coupon generator page 4 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step4() {
        global $SESSION, $DB, $CFG, $USER;
        // Make sure sessions are still alive.
        if (!isset($SESSION->coupon)) {
            print_error("error:sessions-expired", 'block_coupon');
        }

        // Depending on our data we'll get the right form.
        if ($SESSION->coupon->type == 'course') {
            $mform = new \block_coupon\forms\coupon\generator\confirmcourse($this->page->url);
        } else {
            $mform = new \block_coupon\forms\coupon\generator\confirmcohorts($this->page->url);
        }
        $out = '';

        if ($mform->is_cancelled()) {
            unset($SESSION->coupon);
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // These settings are always the same.
            $SESSION->coupon->showform = $data->showform;
            $SESSION->coupon->redirect_url = (empty($data->redirect_url)) ? null : $data->redirect_url;
            $SESSION->coupon->enrolperiod = (empty($data->enrolment_period)) ? null : $data->enrolment_period;

            // If we're generating based on csv we'll redirect first to confirm the csv input.
            if ($data->showform == 'csv') {

                $SESSION->coupon->date_send_coupons = $data->date_send_coupons;
                $SESSION->coupon->csv_content = $mform->get_file_content('coupon_recipients');
                $SESSION->coupon->email_body = $data->email_body['text'];
                // To the extra step.
                $params = array('id' => $this->page->url->param('id'));
                $url = new moodle_url('/blocks/coupon/view/generate_coupon_step_five.php', $params);
                redirect($url);
            }

            // Otherwise we'll be generating coupons right away.
            $couponcodelength = get_config('block_coupon', 'coupon_code_length');
            if (!$couponcodelength) {
                $couponcodelength = 16;
            }

            // If we're generating based on manual csv input.
            if ($data->showform == 'manual') {
                $SESSION->coupon->date_send_coupons = $data->date_send_coupons_manual;
                $SESSION->coupon->email_body = $data->email_body_manual['text'];
                // We'll get users right away.
                $recipients = helper::get_recipients_from_csv($data->coupon_recipients_manual);

                $amountofcoupons = count($recipients);
            }

            // If we're generating based on 'amount' of coupons.
            if ($data->showform == 'amount') {
                // Save last settings in sessions.
                $amountofcoupons = $data->coupon_amount;
                $SESSION->coupon->email_to = (!empty($data->use_alternative_email)) ? $data->alternative_email : $USER->email;
                $SESSION->coupon->generate_single_pdfs = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;
            }

            // Now that we've got all information we'll create the coupon objects.
            $coupons = array();
            for ($i = 0; $i < $amountofcoupons; $i++) {

                $coupon = new stdClass();
                $coupon->ownerid = $USER->id;
                $coupon->courses = ($SESSION->coupon->type == 'course') ? $SESSION->coupon->courses : null;
                $coupon->redirect_url = $SESSION->coupon->redirect_url;
                $coupon->enrolperiod = $SESSION->coupon->enrolperiod;
                $coupon->issend = ($data->showform == 'amount') ? 1 : 0;
                $coupon->single_pdf = ($data->showform == 'amount') ? $SESSION->coupon->generate_single_pdfs : null;
                $coupon->submission_code = generator::generate_unique_code($couponcodelength);

                if ($data->showform == 'manual') {

                    $recipient = $recipients[$i];

                    $coupon->senddate = $SESSION->coupon->date_send_coupons;
                    $coupon->for_user_email = $recipient->email;
                    $coupon->for_user_name = $recipient->name;
                    $coupon->for_user_gender = $recipient->gender;
                    $coupon->redirect_url = $SESSION->coupon->redirect_url;
                    $coupon->enrolperiod = $SESSION->coupon->enrolperiod;
                    $coupon->email_body = $SESSION->coupon->email_body;
                }

                if ($SESSION->coupon->type == 'cohorts') {
                    $coupon->cohorts = array();
                    foreach ($SESSION->coupon->cohorts as $cohortid) {
                        // Build cohort object.
                        $couponcohort = new stdClass();
                        $couponcohort->cohortid = $cohortid;
                        $coupon->cohorts[] = $couponcohort;
                    }
                    // Otherwise we'll add groups if they are selected.
                } else if (isset($SESSION->coupon->groups)) {
                    $coupon->groups = array();
                    foreach ($SESSION->coupon->groups as $groupid) {
                        // Build groups object.
                        $coupongroup = new stdClass();
                        $coupongroup->groupid = $groupid;
                        $coupon->groups[] = $coupongroup;
                    }
                }
                $coupons[] = $coupon;
            }

            // Now that we've got all the coupons.
            $result = helper::generate_coupons($coupons);
            if ($result !== true) {
                // Means we've got an error.
                // Don't know yet what we're gonne do in this situation. Maybe mail to supportuser?
                echo "<p>An error occured while trying to generate the coupons. Please contact support.</p>";
                echo "<pre>" . implode("\n", $result) . "</pre>";
                die();
            }

            if ($data->showform == 'amount') {
                // Stuur maar gewoon gelijk...
                helper::mail_coupons($coupons, $SESSION->coupon->email_to, $SESSION->coupon->generate_single_pdfs);
                unset($SESSION->coupon);
                redirect($CFG->wwwroot . '/my', get_string('coupons_sent', 'block_coupon'));
            } else {
                redirect($CFG->wwwroot . '/my', get_string('coupons_ready_to_send', 'block_coupon'));
            }
        } else {
            $out .= $this->header();
            $out .= '<div class="block-coupon-container">';
            $out .= '<div>';
            $out .= $this->get_tabs($this->page->context, 'wzcoupons', array('id' => $this->page->url->param('id')));
            $out .= '</div>';
            $out .= $mform->render();
            $out .= '</div>';
            $out .= $this->footer();
        }
        return $out;
    }

    /**
     * Render coupon generator page 5 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step5() {
        global $SESSION, $CFG, $USER;
        // Make sure sessions are still alive.
        if (!isset($SESSION->coupon)) {
            print_error("error:sessions-expired", 'block_coupon');
        }

        // Create form.
        $mform = new \block_coupon\forms\coupon\generator\extra($this->page->url);
        $out = '';

        if ($mform->is_cancelled()) {
            unset($SESSION->coupon);
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Get recipients.
            $recipients = helper::get_recipients_from_csv($data->coupon_recipients);

            // Get max length for the coupon code.
            if (!$couponcodelength = get_config('block_coupon', 'coupon_code_length')) {
                $couponcodelength = 16;
            }

            // Now that we've got all information we'll create the coupon objects.
            $coupons = array();
            foreach ($recipients as $recipient) {
                $coupon = new stdClass();
                $coupon->ownerid = $USER->id;
                $coupon->courses = ($SESSION->coupon->type == 'course') ? $SESSION->coupon->courses : null;
                $coupon->submission_code = generator::generate_unique_code($couponcodelength);

                // Extra fields.
                $coupon->senddate = $SESSION->coupon->date_send_coupons;
                $coupon->for_user_email = $recipient->email;
                $coupon->for_user_name = $recipient->name;
                $coupon->for_user_gender = $recipient->gender;
                $coupon->redirect_url = $SESSION->coupon->redirect_url;
                $coupon->enrolperiod = $SESSION->coupon->enrolperiod;
                $coupon->email_body = $SESSION->coupon->email_body;

                if ($SESSION->coupon->type == 'cohorts') {
                    $coupon->cohorts = array();
                    foreach ($SESSION->coupon->cohorts as $cohortid) {
                        // Build cohort object.
                        $couponcohort = new stdClass();
                        $couponcohort->cohortid = $cohortid;
                        $coupon->cohorts[] = $couponcohort;
                    }
                    // Otherwise we'll add groups if they are selected.
                } else if (isset($SESSION->coupon->groups)) {
                    $coupon->groups = array();
                    foreach ($SESSION->coupon->groups as $groupid) {
                        // Build groups object.
                        $coupongroup = new stdClass();
                        $coupongroup->groupid = $groupid;
                        $coupon->groups[] = $coupongroup;
                    }
                }
                $coupons[] = $coupon;
            }

            // Now that we've got all the coupons.
            $result = helper::generate_coupons($coupons);

            if ($result !== true) {
                // Means we've got an error.
                // Don't know yet what we're gonne do in this situation. Maybe mail to supportuser?
                echo "<p>An error occured while trying to generate the coupons. Please contact support.</p>";
                echo "<pre>" . implode("\n", $result) . "</pre>";
                die();
            }

            // Finish.
            unset($SESSION->coupon);
            redirect($CFG->wwwroot . '/my', get_string('coupons_ready_to_send', 'block_coupon'));
        } else {
            $out .= $this->header();
            $out .= '<div class="block-coupon-container">';
            $out .= '<div>';
            $out .= $this->get_tabs($this->page->context, 'wzcoupons', array('id' => $this->page->url->param('id')));
            $out .= '</div>';
            $out .= $mform->render();
            $out .= '</div>';
            $out .= $this->footer();
        }
        return $out;
    }

    /**
     * Create a tab object with a nice image view, instead of just a regular tabobject
     *
     * @param string $id unique id of the tab in this tree, it is used to find selected and/or inactive tabs
     * @param string $pix image name
     * @param string $component component where the image will be looked for
     * @param string|moodle_url $link
     * @param string $text text on the tab
     * @param string $title title under the link, by defaul equals to text
     * @param bool $linkedwhenselected whether to display a link under the tab name when it's selected
     * @return \tabobject
     */
    protected function create_pictab($id, $pix = null, $component = null, $link = null,
            $text = '', $title = '', $linkedwhenselected = false) {
        $img = '';
        if ($pix !== null) {
            $img = $this->pix_url($pix, $component) . ' ';
            $img = '<img src="' . $img . '"';
            if (!empty($title)) {
                $img .= ' alt="' . $title . '"';
            }
            $img .= '/> ';
        }
        return new \tabobject($id, $link, $img . $text, empty($title) ? $text : $title, $linkedwhenselected);
    }

    /**
     * Generate navigation tabs
     *
     * @param \context $context current context to work in (needed to determine capabilities).
     * @param string $selected selected tab
     * @param array $params any paramaters needed for the base url
     */
    protected function get_tabs($context, $selected, $params = array()) {
        $tabs = array();
        // Add exclusions.
        $tabs[] = $this->create_pictab('wzcoupons', 'coupons', 'block_coupon',
                new \moodle_url('/blocks/coupon/view/generate_coupon.php', $params),
                get_string('tab:wzcoupons', 'block_coupon'));
        $tabs[] = $this->create_pictab('wzcouponimage', 'image', 'block_coupon',
                new \moodle_url('/blocks/coupon/view/uploadimage.php', $params),
                get_string('tab:wzcouponimage', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpreport', 'report', 'block_coupon',
                new \moodle_url('/blocks/coupon/view/reports.php', $params),
                get_string('tab:report', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpunused', 'unused', 'block_coupon',
                new \moodle_url('/blocks/coupon/view/unused_coupons.php', $params),
                get_string('tab:unused', 'block_coupon'));
        return $this->tabtree($tabs, $selected);
    }

}