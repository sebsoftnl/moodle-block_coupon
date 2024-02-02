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
 * COntroller taking care of generating cohort type coupons
 *
 * File         extendenrolmentcoupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller\generator;

use moodle_url;
use html_writer;
use block_coupon\helper;
use block_coupon\coupon\generator;
use block_coupon\coupon\generatoroptions;

/**
 * block_coupon\controller\generator\extendenrolmentcoupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extendenrolmentcoupon {

    /**
     * @var \moodle_page
     */
    protected $page;

    /**
     * @var \core_renderer
     */
    protected $output;

    /**
     * @var \block_coupon_renderer
     */
    protected $renderer;

    /**
     * Create new manager instance
     * @param \moodle_page $page
     * @param \core\output_renderer $output
     * @param \core_renderer|null $renderer
     */
    public function __construct($page, $output, $renderer = null) {
        $this->page = $page;
        $this->output = $output;
        $this->renderer = $renderer;
    }

    /**
     * Execute page request
     */
    public function execute_request() {
        $this->process_generator();
    }

    /**
     * Display user overview table
     */
    protected function process_generator() {
        $page = optional_param('page', 1, PARAM_INT);

        $func = "process_page_{$page}";
        if (!method_exists($this, $func)) {
            throw new \InvalidArgumentException("Page {$page} does not exist");
        }

        $this->{$func}();
    }

    /**
     * Process page 1
     */
    protected function process_page_1() {
        global $USER, $CFG, $DB;
        // If we have a valid course ID, continue to page 2.
        $courseid = optional_param('cid', null, PARAM_INT);
        if (!empty($courseid) && $courseid > 1) {
            // Validate course.
            if (!$DB->record_exists('course', array('id' => $courseid))) {
                redirect(new moodle_url($CFG->wwwroot . '/my'),
                        get_string('generator:extendenrolment:invalidcourse', 'block_coupon'));
            }
            // We have a valid course. Prepare generator options and continue to page 2.
            generatoroptions::clean_session();
            $generatoroptions = new generatoroptions();
            $generatoroptions->ownerid = $USER->id;
            $generatoroptions->type = generatoroptions::ENROLEXTENSION;
            $generatoroptions->courses = array($courseid);
            $generatoroptions->to_session();
            $redirect = $this->get_url(['page' => 1]);
            redirect($redirect);
        }

        $url = $this->get_url(['page' => '1']);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\extendenrolment\coursevars($url,
                ['generatoroptions' => $generatoroptions, 'coursemultiselect' => false]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Load generator options.
            $generatoroptions = generatoroptions::from_session();
            $generatoroptions->ownerid = $USER->id;
            $generatoroptions->type = generatoroptions::ENROLEXTENSION;
            $generatoroptions->courses = $data->coupon_courses;
            if (!is_array($generatoroptions->courses)) {
                $generatoroptions->courses = [$generatoroptions->courses];
            }
            $generatoroptions->enrolperiod = $data->enrolperiod;

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => '2']);
            redirect($redirect);
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 2
     */
    protected function process_page_2() {
        global $CFG, $DB;
        $url = $this->get_url(['page' => 2]);

        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\extendenrolment\courseusers($url, ['generatoroptions' => $generatoroptions]);

        if ($mform->is_previous()) {
            $redirecturl = $this->get_url(['page' => 1]);
            redirect($redirecturl);
        } else if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            if ((bool)$data->abort) {
                generatoroptions::clean_session();
                redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
                exit; // Never reached.
            }
            // Set user(s).
            $generatoroptions->amount = count($data->extendusers);
            $generatoroptions->extendusers = $data->extendusers;
            $generatoroptions->extendusersrecipient = $data->extendusersrecipient;

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => 3]);
            redirect($redirect);
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 4
     */
    protected function process_page_3() {
        global $CFG, $USER, $DB;
        $url = $this->get_url(['page' => 3]);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\generatorsettings($url, [$generatoroptions]);

        if ($mform->is_previous()) {
            $redirecturl = $this->get_url(['page' => 2]);
            redirect($redirecturl);
        } else if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // These settings are always the same.
            if (!empty($data->batchid)) {
                $generatoroptions->batchid = $data->batchid;
            }
            // This is pretty much where we MAY have campaign types supporting "single code fits N usages".
            $generatoroptions->generatorflags = helper::make_generator_flags($data->flags);
            $generatoroptions->codesize = $data->codesize;
            $generatoroptions->redirecturl = (empty($data->redirect_url)) ? null : $data->redirect_url;
            $generatoroptions->expirymethod = $data->expirationmethod;
            $generatoroptions->expiresin = $data->expiresin ?? 0;
            $generatoroptions->expiresat = helper::get_expiration_from_data($data);
            $generatoroptions->generatecodesonly = $data->generatecodesonly;

            // When generating codes only, skip the PDF settings page.
            $nextpage = 4;
            if ((bool)$generatoroptions->generatecodesonly) {
                // Forced generator options to prevent failure/wrong behaviour.
                $generatoroptions->templateid = null;
                $generatoroptions->renderqrcode = false;
                $generatoroptions->font = null;
                $generatoroptions->logoid = null;
                $nextpage = 5;
            }

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => $nextpage]);
            redirect($redirect);
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 5
     */
    protected function process_page_4() {
        global $CFG;
        $url = $this->get_url(['page' => '4']);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\pdfsettings($url, [$generatoroptions, ['']]);

        if ($mform->is_previous()) {
            $prevpage = 3;
            $redirecturl = $this->get_url(['page' => $prevpage]);
            redirect($redirecturl);
        } else if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            $generatoroptions->generatesinglepdfs = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;
            $generatoroptions->pdftype = $data->usetype;
            if ($data->usetype == 'template') {
                $generatoroptions->templateid = $data->templateid;
                $generatoroptions->renderqrcode = false;
                $generatoroptions->font = null;
                $generatoroptions->logoid = null;
            } else {
                $generatoroptions->templateid = null;
                $generatoroptions->renderqrcode = (isset($data->renderqrcode) && $data->renderqrcode) ? true : false;
                if (isset($data->font)) {
                    $generatoroptions->font = $data->font;
                }
                $generatoroptions->logoid = $data->logo;
            }

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => 5]);
            redirect($redirect);
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 5
     */
    protected function process_page_5() {
        global $CFG, $DB, $USER;
        $url = $this->get_url(['page' => 5]);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\extendenrolment\recips($url, [$generatoroptions]);

        if ($mform->is_previous()) {
            $prevpage = 4;
            if ($generatoroptions->generatecodesonly) {
                $prevpage = 3;
            }
            $redirecturl = $this->get_url(['page' => $prevpage]);
            redirect($redirecturl);
        } else if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($mform->no_submit_button_pressed()) {
            $tplid = $mform->optional_param('tplload', 0, PARAM_INT);
            if (!empty($tplid)) {
                $mailtemplate = $DB->get_record('block_coupon_mailtemplates', ['id' => $tplid]);
                $generatoroptions->emailbody = $mailtemplate->body;
            } else {
                $generatoroptions->emailbody = get_string('coupon_mail_extend_content', 'block_coupon');
            }
            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And reload page.
            redirect($url);
        } else if ($data = $mform->get_data()) {
            if ($generatoroptions->extendusersrecipient == 'me') {
                $generatoroptions->altemail = (bool)$data->use_alternative_email;
                $generatoroptions->emailto = (!empty($data->use_alternative_email)) ? $data->alternative_email : $USER->email;
            } else {
                $generatoroptions->emailto = null;
                // Load recipients.
                $generatoroptions->recipients = [];
                $fields = 'id, email, ' . \block_coupon\helper::get_all_user_name_fields(true);
                $users = $DB->get_records_list('user', 'id', $generatoroptions->extendusers, '', $fields);
                foreach ($users as $user) {
                    $generatoroptions->recipients[] = (object) array(
                        'email' => $user->email,
                        'name' => fullname($user),
                        'gender' => '',
                    );
                }
                // Force seperate coupons!
                $generatoroptions->generatesinglepdfs = true;
                $generatoroptions->emailbody = $data->email_body_manual['text'];
            }
            $generatoroptions->senddate = $data->date_send_coupons_manual;

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => 6]);
            redirect($redirect);
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 8
     */
    protected function process_page_6() {
        global $CFG, $DB;
        $url = $this->get_url(['page' => 6]);

        // Set up preview stuff.
        $previewurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/preview.php');
        $args = ['#block-coupon-preview-btn', $previewurl->out()];
        $this->page->requires->js_call_amd('block_coupon/preview', 'init', $args);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\confirm($url, [$generatoroptions]);

        if ($mform->is_previous()) {
            $redirecturl = $this->get_url(['page' => 5]);
            redirect($redirecturl);
        } else if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // There is no data, only processing.
            $generatoroptions->to_session();

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

            // Finish.
            generatoroptions::clean_session();
            // We will only use direct sending if we're sending off to an alternative email address.
            if ($generatoroptions->extendusersrecipient == 'users') {
                // Redirect; this is all handled by a task later on.
                redirect(new moodle_url($CFG->wwwroot . '/my'), get_string('coupons_ready_to_send', 'block_coupon'));
            }

            // Only send if not opted to only generate the codes!
            if (!$generatoroptions->generatecodesonly) {
                // Generate and send off.
                $coupons = $DB->get_records_list('block_coupon', 'id', $generator->get_generated_couponids());
                list($rs, $batchid, $ts) = helper::mail_coupons($coupons, $generatoroptions, false, false);

                $dlurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/download.php', ['bid' => $batchid, 't' => $ts]);
                $dllink = \html_writer::link($dlurl, get_string('here', 'block_coupon'));
                if ($rs) {
                    $redirectmessage = get_string('coupons_generated', 'block_coupon', $dllink);
                } else {
                    $redirectmessage = get_string('coupons_generated', 'block_coupon', $dllink);
                }
            } else {
                $redirectmessage = get_string('coupons_generated_codes_only', 'block_coupon');
            }
            generatoroptions::clean_session();
            // Force message as a notification.
            \core\notification::success($redirectmessage);
            redirect(new moodle_url($CFG->wwwroot . '/my'));
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Start page output.
     */
    protected function start_page() {
        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
    }

    /**
     * End page output.
     */
    protected function end_page() {
        echo html_writer::end_div();
        echo $this->output->footer();
    }

    /**
     * Return new url based on the current page-url
     *
     * @param array $mergeparams
     * @return \moodle_url
     */
    protected function get_url($mergeparams = []) {
        $url = $this->page->url;
        $url->params($mergeparams);
        return $url;
    }

}
