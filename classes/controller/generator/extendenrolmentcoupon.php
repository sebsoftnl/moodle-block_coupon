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

defined('MOODLE_INTERNAL') || die();

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

        switch ($page) {
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
            $redirect = $this->get_url(['page' => '2']);
            redirect($redirect);
        }

        $url = $this->get_url(['page' => '1']);

        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\extendenrolment\page1($url,
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
            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => '2']);
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
        global $CFG, $DB;
        $url = $this->get_url(['page' => '2']);

        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\extendenrolment\page2($url, ['generatoroptions' => $generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
        } else if ($data = $mform->get_data()) {
            if ((bool)$data->abort) {
                generatoroptions::clean_session();
                redirect(new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id)));
                exit; // Never reached.
            }
            // Set logo.
            $generatoroptions->logoid = $data->logo;
            // Set user(s).
            $generatoroptions->amount = count($data->extendusers);
            $generatoroptions->extendusers = $data->extendusers;
            $generatoroptions->enrolperiod = $data->enrolperiod;
            $generatoroptions->generatesinglepdfs = $data->generate_pdf;
            $generatoroptions->renderqrcode = (isset($data->renderqrcode) && $data->renderqrcode) ? true : false;
            $generatoroptions->redirecturl = $data->redirect_url;
            $generatoroptions->emailto = (!empty($data->use_alternative_email)) ? $data->alternative_email : null;
            if (empty($data->use_alternative_email)) {
                $generatoroptions->emailto = null;
                // Load recipients!
                $generatoroptions->recipients = [];
                $fields = 'id, email, ' . get_all_user_name_fields(true);
                $users = $DB->get_records_list('user', 'id', $data->extendusers, '', $fields);
                foreach ($users as $user) {
                    $generatoroptions->recipients[] = (object) array(
                        'email' => $user->email,
                        'name' => fullname($user),
                        'gender' => '',
                    );
                }
                // Force seperate coupons!
                $generatoroptions->generatesinglepdfs = true;
            } else {
                $generatoroptions->emailto = $data->alternative_email;
            }
            $generatoroptions->emailbody = $data->email_body_manual['text'];

            // Serialize generatoroptions to session.
            $generatoroptions->to_session();
            // And redirect user to next page.
            $redirect = $this->get_url(['page' => '3']);
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
        global $CFG, $DB;
        $url = $this->get_url(['page' => '3']);

        // Make sure sessions are still alive.
        generatoroptions::validate_session();
        // Load generator options.
        $generatoroptions = generatoroptions::from_session();
        // Create form.
        $mform = new \block_coupon\forms\coupon\extendenrolment\page3($url, ['generatoroptions' => $generatoroptions]);

        if ($mform->is_cancelled()) {
            generatoroptions::clean_session();
            $conditions = array('id' => $this->page->course->id);
            redirect(new moodle_url($CFG->wwwroot . '/course/view.php', $conditions));
        } else if ($data = $mform->get_data()) {
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
            // We will only use direct sending if we're sending off to an alternative email address.
            if (empty($generatoroptions->emailto)) {
                redirect(new moodle_url($CFG->wwwroot . '/my'), get_string('coupons_ready_to_send', 'block_coupon'));
            } else {
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
                generatoroptions::clean_session();
                // Force message ALSO as a notification.
                \core\notification::success($redirectmessage);
                redirect(new moodle_url($CFG->wwwroot . '/my'), $redirectmessage);
            }
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
