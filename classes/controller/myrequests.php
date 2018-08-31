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
 * Request admin manager implementation for use with block_coupon
 *
 * File         myrequests.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\manager\myrequests
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class myrequests {

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
        $action = optional_param('action', null, PARAM_ALPHA);
        switch ($action) {
            case 'newrequest':
                $this->process_new_request();
                break;
            case 'delete':
                $this->process_delete_request();
                break;
            case 'details':
                $this->process_request_details();
                break;
            case 'list':
            default:
                $this->process_request_overview();
                break;
        }
    }

    /**
     * Display user overview table
     */
    protected function process_request_overview() {
        $table = new \block_coupon\tables\myrequests();
        $table->baseurl = new \moodle_url($this->page->url->out());

        $newurl = $this->get_url(['action' => 'newrequest']);

        echo $this->output->header();
        echo \html_writer::link($newurl, get_string('str:request:add', 'block_coupon'));
        echo '<br/>';
        echo $table->render(25);
        echo $this->output->footer();
    }

    /**
     * Process delete requestuser instance
     */
    protected function process_delete_request() {
        global $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_URL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'list']);
        }

        $params = array('action' => 'delete', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_requests', ['id' => $itemid]);
        $user = \core_user::get_user($instance->userid);

        $options = [
            get_string('delete:request:header', 'block_coupon', $user),
            $this->renderer->requestdetails($instance),
            get_string('delete:request:confirmmessage', 'block_coupon', $user)
        ];
        $mform = new \block_coupon\forms\confirmation($url, $options);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            if ((bool) $data->confirm) {
                $DB->delete_records('block_coupon_request', ['id' => $itemid]);
            }
            redirect($redirect);
        }
        echo $this->output->header();
        $mform->display();
        echo $this->output->footer();
    }

    /**
     * Process edit requestuser instance
     */
    protected function process_new_request() {
        global $CFG, $DB, $USER;
        $redirect = optional_param('redirect', null, PARAM_URL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'list']);
        }

        $params = array('action' => 'newrequest');
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_rusers', ['userid' => $USER->id]);

        $mform = new \block_coupon\forms\coupon\request\course($url, [$instance, $USER]);

        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            $generatoroptions = new \block_coupon\coupon\generatoroptions();
            $generatoroptions->type = \block_coupon\coupon\generatoroptions::COURSE;
            $generatoroptions->amount = $data->coupon_amount;
            $generatoroptions->ownerid = $instance->userid;
            $generatoroptions->enrolperiod = $data->enrolment_period;
            $generatoroptions->courses = $data->coupon_courses;
            if ($data->use_alternative_email) {
                $generatoroptions->emailto = $data->alternative_email;
            } else {
                $generatoroptions->emailto = $USER->email;
            }
            $generatoroptions->generatesinglepdfs = (bool) $data->generate_pdf;
            $generatoroptions->logoid = $data->logo;
            $generatoroptions->renderqrcode = (bool) $data->renderqrcode;
            $generatoroptions->roleid = $data->coupon_role;

            $record = new \stdClass();
            $record->id = 0;
            $record->userid = $instance->userid;
            $record->configuration = serialize($generatoroptions);
            $record->timecreated = time();
            $record->timemodified = $record->timecreated;
            $record->id = $DB->insert_record('block_coupon_requests', $record);

            // SUCCESS!
            redirect($redirect);
        }

        echo $this->output->header();
        echo $mform->render();
        echo $this->output->footer();
    }

    /**
     * Process details view
     */
    protected function process_request_details() {
        global $CFG, $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_URL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'list']);
        }

        $params = array('action' => 'details', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_requests', ['id' => $itemid]);
        $user = \core_user::get_user($instance->userid);

        echo $this->output->header();
        echo $this->renderer->requestdetails($instance);
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
