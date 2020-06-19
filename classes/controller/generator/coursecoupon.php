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
 * Controller taking care of generating course type coupons
 *
 * File         coursecoupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller\generator;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use html_writer;
use block_coupon\helper;
use block_coupon\coupon\generator;
use block_coupon\coupon\generatoroptions;

/**
 * block_coupon\controller\generator\coursecoupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecoupon {

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

        switch ($page) {
            case 5:
                $this->process_page_5();
                break;
            case 4:
                $this->process_page_4();
                break;
            case 3:
                $this->process_page_3();
                break;
            case 2:
                $this->process_page_2();
                break;
            case 1:
                $this->process_page_1();
            default:
                break;
        }
    }

    /**
     * Process page 1
     */
    protected function process_page_1() {
        global $CFG, $USER, $DB;
        $url = $this->get_url(['page' => '1']);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\course\page1($url, [$generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            $generatoroptions->ownerid = $USER->id;
            $generatoroptions->type = generatoroptions::COURSE;
            $generatoroptions->logoid = $data->logo;
            if (!empty($data->batchid)) {
                $generatoroptions->batchid = $data->batchid;
            }
            $generatoroptions->roleid = $data->coupon_role;
            $generatoroptions->enrolperiod = (empty($data->enrolment_period)) ? null : $data->enrolment_period;

            $generatoroptions->courses = $data->coupon_courses;

            $hasgroups = false;
            foreach ($data->coupon_courses as $courseid) {
                $groups = $DB->get_records("groups", array('courseid' => $courseid));
                if (count($groups) > 0) {
                    $hasgroups = true;
                }
            }
            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $nextpage = ($hasgroups) ? 2 : 3;
            $redirect = $this->get_url(['page' => $nextpage]);
            redirect($redirect);
        }

        generatoroptions::clean_session();
        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 2
     */
    protected function process_page_2() {
        global $CFG;
        $url = $this->get_url(['page' => '2']);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\course\page2($url, [$generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Add selected groups to session.
            if (isset($data->coupon_groups)) {
                $generatoroptions->groups = $data->coupon_groups;
            }

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
     * Process page 3
     */
    protected function process_page_3() {
        global $CFG;
        $url = $this->get_url(['page' => '3']);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\course\page3($url, [$generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            $generatoroptions->generatormethod = $data->showform;

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => 4]);
            redirect($redirect);
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 4
     */
    protected function process_page_4() {
        global $CFG, $USER, $DB;
        $url = $this->get_url(['page' => '4']);

        // Set up preview stuff.
        $bid = helper::find_block_instance_id();
        $previewurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/preview.php', ['id' => $bid]);
        $args = ['#block-coupon-preview-btn', $previewurl->out()];
        $this->page->requires->js_call_amd('block_coupon/preview', 'init', $args);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\course\page4($url, [$generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // These settings are always the same.
            $generatoroptions->redirecturl = (empty($data->redirect_url)) ? null : $data->redirect_url;
            $generatoroptions->renderqrcode = (isset($data->renderqrcode) && $data->renderqrcode) ? true : false;

            if ($generatoroptions->generatormethod == 'csv') {
                $generatoroptions->csvdelimitername = $data->csvdelimiter;
                $generatoroptions->senddate = $data->date_send_coupons;
                $generatoroptions->csvrecipients = $mform->get_file_content('coupon_recipients');
                $generatoroptions->emailbody = $data->email_body['text'];

                // Serialize generatoroptions to session.
                $generatoroptions->to_session();

                // To the extra step.
                $redirect = $this->get_url(['page' => 5]);
                redirect($redirect);
            }

            // If we're generating based on manual csv input.
            if ($generatoroptions->generatormethod == 'manual') {
                $generatoroptions->csvdelimitername = ','; // Forced!
                $generatoroptions->senddate = $data->date_send_coupons_manual;
                $generatoroptions->emailbody = $data->email_body_manual['text'];
                // We'll get users right away.
                $delimiter = helper::get_delimiter($generatoroptions->csvdelimitername);
                $generatoroptions->recipients = helper::get_recipients_from_csv($data->coupon_recipients_manual, $delimiter);
                // Set amount, otherwise the generator won't do anything.
                $generatoroptions->amount = count($generatoroptions->recipients);
            }

            // If we're generating based on 'amount' of coupons.
            if ($generatoroptions->generatormethod == 'amount') {
                // Save last settings in sessions.
                $generatoroptions->amount = $data->coupon_amount;
                $generatoroptions->codesize = $data->codesize;
                $generatoroptions->emailto = (!empty($data->use_alternative_email)) ? $data->alternative_email : $USER->email;
                $generatoroptions->generatesinglepdfs = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;
                $generatoroptions->generatecodesonly = (isset($data->generatecodesonly) && $data->generatecodesonly) ? true : false;
            }
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

            if ($generatoroptions->generatormethod == 'amount') {
                // Only send if not opted to only generate the codes!
                if (!$generatoroptions->generatecodesonly) {
                    // Generate and send off.
                    $coupons = $DB->get_records_list('block_coupon', 'id', $generator->get_generated_couponids());
                    list($rs, $batchid, $ts) = helper::mail_coupons($coupons, $generatoroptions->emailto,
                            $generatoroptions->generatesinglepdfs, false, false, $generatoroptions->batchid);

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
            } else {
                $redirectmessage = get_string('coupons_ready_to_send', 'block_coupon');
                \core\notification::success($redirectmessage);
                redirect(new moodle_url($CFG->wwwroot . '/my'));
            }
        }

        $this->start_page();
        echo $mform->render();
        $this->end_page();
    }

    /**
     * Process page 5
     */
    protected function process_page_5() {
        global $CFG;
        $url = $this->get_url(['page' => '5']);

        // Set up preview stuff.
        $bid = helper::find_block_instance_id();
        $previewurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/preview.php', ['id' => $bid]);
        $args = ['#block-coupon-preview-btn', $previewurl->out()];
        $this->page->requires->js_call_amd('block_coupon/preview', 'init', $args);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\course\page5($url, [$generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            // Get recipients.
            $delimiter = helper::get_delimiter($generatoroptions->csvdelimitername);
            $generatoroptions->recipients = helper::get_recipients_from_csv($data->coupon_recipients, $delimiter);

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
            $redirectmessage = get_string('coupons_ready_to_send', 'block_coupon');
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
        echo html_writer::start_div();
        echo $this->renderer->get_tabs($this->page->context, 'wzcoupons', $this->page->url->params());
        echo html_writer::end_div();
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
