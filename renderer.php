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
use block_coupon\coupon\generatoroptions;
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
            $out .= html_writer::start_div('block-coupon-container');
            $out .= html_writer::start_div();
            $out .= $this->get_tabs($this->page->context, 'wzcouponimage', array('id' => $id));
            $out .= html_writer::end_div();
            $out .= $mform->render();
            $out .= html_writer::end_div();
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
        $filter = \block_coupon\tables\coupons::UNUSED;
        return $this->page_coupons($id, $filter, $ownerid);
    }

    /**
     * Render used coupon page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    public function page_used_coupons($id, $ownerid = null) {
        $filter = \block_coupon\tables\coupons::USED;
        return $this->page_coupons($id, $filter, $ownerid);
    }

    /**
     * Render coupon page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $filter table filter
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    protected function page_coupons($id, $filter, $ownerid = null) {
        // Actions anyone?
        $action = optional_param('action', null, PARAM_ALPHA);
        if ($action === 'delete' && ($filter === \block_coupon\tables\coupons::UNUSED)) {
            global $DB;
            require_sesskey();
            $id = required_param('itemid', PARAM_INT);
            $DB->delete_records('block_coupon', array('id' => $id));
            $DB->delete_records('block_coupon_cohorts', array('couponid' => $id));
            $DB->delete_records('block_coupon_groups', array('couponid' => $id));
            $DB->delete_records('block_coupon_courses', array('couponid' => $id));
            redirect($this->page->url, get_string('coupon:deleted', 'block_coupon'));
        }
        // Table instance.
        $table = new \block_coupon\tables\coupons($ownerid, $filter);
        $table->baseurl = $this->page->url;
        $table->is_downloadable(true);
        $table->show_download_buttons_at(array(TABLE_P_BOTTOM, TABLE_P_TOP));
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'coupons', 'coupons');
            $table->render(25);
            exit;
        }

        $selectedtab = '';
        switch ($filter) {
            case \block_coupon\tables\coupons::UNUSED:
                $selectedtab = 'cpunused';
                break;
            case \block_coupon\tables\coupons::USED:
                $selectedtab = 'cpused';
                break;
        }

        $out = '';
        $out .= $this->header();
        $out .= html_writer::start_div('block-coupon-container');
        $out .= html_writer::start_div();
        $out .= $this->get_tabs($this->page->context, $selectedtab, array('id' => $id));
        $out .= html_writer::end_div();
        ob_start();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= html_writer::end_div();
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
        $out .= html_writer::start_div('block-coupon-container');
        $out .= html_writer::start_div();
        $out .= $this->get_tabs($this->page->context, 'cpreport', array('id' => $id));
        $out .= html_writer::end_div();
        ob_start();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= html_writer::end_div();
        $out .= $this->footer();
        return $out;
    }

    /**
     * Render error report page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    public function page_error_report($id, $ownerid = null) {
        // Table instance.
        $table = new \block_coupon\tables\errorreport($ownerid);
        $table->baseurl = $this->page->url;

        $out = '';
        $out .= $this->header();
        $out .= html_writer::start_div('block-coupon-container');
        $out .= html_writer::start_div();
        $out .= $this->get_tabs($this->page->context, 'cperrorreport', array('id' => $id));
        $out .= html_writer::end_div();
        ob_start();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= html_writer::end_div();
        $out .= $this->footer();
        return $out;
    }

    /**
     * Render coupon generator page 1 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator() {
        global $USER;
        // Create form.
        $mform = new block_coupon\forms\coupon\generator($this->page->url);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Load generator options.
            $generatoroptions = generatoroptions::from_session();
            $generatoroptions->ownerid = $USER->id;
            $generatoroptions->type = ($data->coupon_type['type'] == 0) ?
                    generatoroptions::COURSE : generatoroptions::COHORT;
            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $params = array('id' => $this->page->url->param('id'));
            $redirect = new moodle_url('/blocks/coupon/view/generate_coupon_step_two.php', $params);
            redirect($redirect);
        }

        generatoroptions::clean_session();
        $out = '';
        $out .= $this->get_coupon_form_page($mform);
        return $out;
    }

    /**
     * Render coupon generator page 2 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step2() {
        global $DB;
        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load options.
        $generatoroptions = generatoroptions::from_session();

        // Depending on our data we'll get the right form.
        if ($generatoroptions->type == generatoroptions::COURSE) {
            $mform = new \block_coupon\forms\coupon\generator\selectcourse($this->page->url);
        } else {
            $mform = new \block_coupon\forms\coupon\generator\selectcohorts($this->page->url);
        }

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            if ($generatoroptions->type == generatoroptions::COURSE) {
                $generatoroptions->courses = $data->coupon_courses;

                $hasgroups = false;
                foreach ($data->coupon_courses as $courseid) {
                    $groups = $DB->get_records("groups", array('courseid' => $courseid));
                    if (count($groups) > 0) {
                        $hasgroups = true;
                    }
                }

                $nextpage = ($hasgroups) ? 'generate_coupon_step_three' : $nextpage = 'generate_coupon_step_four';
            } else {
                $generatoroptions->cohorts = $data->coupon_cohorts;
                $nextpage = 'generate_coupon_step_three';
            }

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();

            $params = array('id' => $this->page->url->param('id'));
            $url = new moodle_url('/blocks/coupon/view/' . $nextpage . '.php', $params);
            redirect($url);
        }

        $out = '';
        $out .= $this->get_coupon_form_page($mform);
        return $out;
    }

    /**
     * Render coupon generator page 3 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step3() {
        global $DB;
        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load options.
        $generatoroptions = generatoroptions::from_session();

        // Depending on our data we'll get the right form.
        if ($generatoroptions->type == generatoroptions::COURSE) {
            $mform = new \block_coupon\forms\coupon\generator\selectgroups($this->page->url);
        } else {
            $mform = new block_coupon\forms\coupon\generator\selectcohortcourses($this->page->url);
        }

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Save param, its only about course or cohorts.
            if ($generatoroptions->type == generatoroptions::COURSE) {
                // Add selected groups to session.
                if (isset($data->coupon_groups)) {
                    $generatoroptions->groups = $data->coupon_groups;
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

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();

            $params = array('id' => $this->page->url->param('id'));
            $url = new moodle_url('/blocks/coupon/view/generate_coupon_step_four.php', $params);
            redirect($url);
        }

        $out = '';
        $out .= $this->get_coupon_form_page($mform);
        return $out;
    }

    /**
     * Render coupon generator page 4 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step4() {
        global $DB, $USER;
        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load options.
        $generatoroptions = generatoroptions::from_session();

        // Depending on our data we'll get the right form.
        if ($generatoroptions->type == generatoroptions::COURSE) {
            $mform = new \block_coupon\forms\coupon\generator\confirmcourse($this->page->url);
        } else {
            $mform = new \block_coupon\forms\coupon\generator\confirmcohorts($this->page->url);
        }

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // These settings are always the same.
            $generatoroptions->redirecturl = (empty($data->redirect_url)) ? null : $data->redirect_url;
            $generatoroptions->enrolperiod = (empty($data->enrolment_period)) ? null : $data->enrolment_period;

            // If we're generating based on csv we'll redirect first to confirm the csv input.
            if ($data->showform == 'csv') {

                $generatoroptions->senddate = $data->date_send_coupons;
                $generatoroptions->csvrecipients = $mform->get_file_content('coupon_recipients');
                $generatoroptions->emailbody = $data->email_body['text'];

                // Serialize generatoroptions to session.
                $generatoroptions->to_session();

                // To the extra step.
                $params = array('id' => $this->page->url->param('id'));
                $url = new moodle_url('/blocks/coupon/view/generate_coupon_step_five.php', $params);
                redirect($url);
            }

            // If we're generating based on manual csv input.
            if ($data->showform == 'manual') {
                $generatoroptions->senddate = $data->date_send_coupons_manual;
                $generatoroptions->emailbody = $data->email_body_manual['text'];
                // We'll get users right away.
                $generatoroptions->recipients = helper::get_recipients_from_csv($data->coupon_recipients_manual);
            }

            // If we're generating based on 'amount' of coupons.
            if ($data->showform == 'amount') {
                // Save last settings in sessions.
                $generatoroptions->amount = $data->coupon_amount;
                $generatoroptions->emailto = (!empty($data->use_alternative_email)) ? $data->alternative_email : $USER->email;
                $generatoroptions->generatesinglepdfs = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;
            }

            // Now that we've got all the coupons.
            $generator = new generator();
            $result = $generator->generate_coupons($generatoroptions);
            if ($result !== true) {
                // Means we've got an error.
                // Don't know yet what we're gonne do in this situation. Maybe mail to supportuser?
                echo "<p>An error occured while trying to generate the coupons. Please contact support.</p>";
                echo "<pre>" . implode("\n", $result) . "</pre>";
                die();
            }

            if ($data->showform == 'amount') {
                // Generate and send off.
                $coupons = $DB->get_records_list('block_coupon', 'id', $generator->get_generated_couponids());
                helper::mail_coupons($coupons, $generatoroptions->emailto, $generatoroptions->generatesinglepdfs);
                generatoroptions::clean_session();
                redirect(new moodle_url('/my'), get_string('coupons_sent', 'block_coupon'));
            } else {
                redirect(new moodle_url('/my'), get_string('coupons_ready_to_send', 'block_coupon'));
            }
        }

        $out = '';
        $out .= $this->get_coupon_form_page($mform);
        return $out;
    }

    /**
     * Render coupon generator page 5 (including header / footer).
     *
     * @return string
     */
    public function page_coupon_generator_step5() {
        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load options.
        $generatoroptions = generatoroptions::from_session();

        // Create form.
        $mform = new \block_coupon\forms\coupon\generator\extra($this->page->url);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url('/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Get recipients.
            $generatoroptions->recipients = helper::get_recipients_from_csv($data->coupon_recipients);

            // Now that we've got all information we'll create the coupon objects.
            $generator = new generator();
            $result = $generator->generate_coupons($generatoroptions);

            if ($result !== true) {
                // Means we've got an error.
                // Don't know yet what we're gonne do in this situation. Maybe mail to supportuser?
                echo "<p>An error occured while trying to generate the coupons. Please contact support.</p>";
                echo "<pre>" . implode("\n", $result) . "</pre>";
                die();
            }

            // Finish.
            generatoroptions::clean_session();
            redirect(new moodle_url('/my'), get_string('coupons_ready_to_send', 'block_coupon'));
        }

        $out = '';
        $out .= $this->get_coupon_form_page($mform);
        return $out;
    }

    /**
     * Get form page output (includes header/footer).
     *
     * @param \moodleform $mform
     */
    protected function get_coupon_form_page($mform) {
        $out = '';
        $out .= $this->header();
        $out .= html_writer::start_div('block-coupon-container');
        $out .= html_writer::start_div();
        $out .= $this->get_tabs($this->page->context, 'wzcoupons', array('id' => $this->page->url->param('id')));
        $out .= html_writer::end_div();
        $out .= $mform->render();
        $out .= html_writer::end_div();
        $out .= $this->footer();
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
                new \moodle_url('/blocks/coupon/view/coupon_view.php',
                array_merge($params, array('tab' => 'unused'))),
                get_string('tab:unused', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpused', 'used', 'block_coupon',
                new \moodle_url('/blocks/coupon/view/coupon_view.php',
                array_merge($params, array('tab' => 'used'))),
                get_string('tab:used', 'block_coupon'));
        $tabs[] = $this->create_pictab('cperrorreport', 'error', 'block_coupon',
                new \moodle_url('/blocks/coupon/view/errorreport.php',
                array_merge($params, array('tab' => 'cperrorreport'))),
                get_string('tab:errors', 'block_coupon'));
        return $this->tabtree($tabs, $selected);
    }

}