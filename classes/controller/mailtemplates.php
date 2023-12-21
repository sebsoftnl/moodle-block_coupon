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
 * Coupon overview(s) implementation for use with block_coupon
 *
 * File         mailtemplates.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller;

use html_writer;

/**
 * block_coupon\manager\mailtemplates
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mailtemplates {

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
            case 'add':
            case 'edit':
                $this->process_edit();
                break;
            case 'list':
            default:
                $this->process_index();
                break;
        }
    }

    /**
     * Display progress report
     */
    protected function process_index() {
        global $USER;
        $ownerid = (has_capability('block/coupon:administration', $this->page->context) ? 0 : $USER->id);

        $this->page->requires->js_call_amd('block_coupon/mailtemplates/mailtemplates', 'init', ['.block-coupon-container']);

        // Table instance.
        $filterset = new \block_coupon\table\mailtemplates_filterset($this->page->url);
        $table = new \block_coupon\table\mailtemplates($ownerid);
        $table->set_filterset($filterset);

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_tabs($this->page->context, 'cpmailtemplates');
        echo html_writer::end_div();
        echo $table->render(25, false);
        echo html_writer::end_div();
        $url = $this->get_url(['action' => 'add']);
        echo $this->output->single_button($url, get_string('createtemplate', 'block_coupon'), 'get');
        echo $this->output->footer();
    }

    /**
     * Process add/edit.
     */
    protected function process_edit() {
        global $DB, $USER;
        $id = optional_param('tid', 0, PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url();
        }

        if (empty($id)) {
            $params = array('action' => 'add');
        } else {
            $params = array('action' => 'edit', 'tid' => $id);
        }
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_mailtemplates', ['id' => $id]);
        if (empty($instance)) {
            $instance = new \stdClass;
            $instance->id = 0;
            $instance->bodyformat = FORMAT_HTML;
            $instance->usercreated = $USER->id;
        }

        $mform = new \block_coupon\forms\mailtemplates\edit($url, [$instance]);

        // We MUST do this here, IN form definition() causes a rest of set values...
        // See https://tracker.moodle.org/browse/MDL-53889.
        $instancedata = clone $instance;
        $instancedata->body = [
            'text' => $instancedata->body,
            'format' => $instancedata->bodyformat
        ];
        $mform->set_data($instancedata);

        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            // Create or update.
            $instance->name = $data->name;
            $instance->subject = $data->subject;
            $instance->body = $data->body['text'];
            $instance->bodyformat = $data->body['format'];
            $instance->timemodified = time();
            if ($instance->id == 0) {
                $instance->timecreated = time();
                $DB->insert_record('block_coupon_mailtemplates', $instance);
            } else {
                $DB->update_record('block_coupon_mailtemplates', $instance);
            }

            // SUCCESS!
            redirect($redirect);
        }

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cpmailtemplates');
        echo $mform->render();
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
