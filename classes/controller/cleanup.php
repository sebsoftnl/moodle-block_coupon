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
 * File         cleanup.php
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

use moodle_url;
use html_writer;

/**
 * block_coupon\manager\cleanup
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup {

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
        global $CFG, $USER;
        $ownerid = (has_capability('block/coupon:viewallreports', $this->page->context) ? 0 : $USER->id);

        $title = 'view:cleanup:title';
        $heading = 'view:cleanup:heading';

        $this->page->navbar->add(get_string($title, 'block_coupon'));

        $url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/cleanup.php',
                array('id' => $this->page->url->param('id'), 'tab' => 'cpcleaner'));
        $this->page->set_url($url);

        $this->page->set_title(get_string($title, 'block_coupon'));
        $this->page->set_heading(get_string($heading, 'block_coupon'));

        $action = optional_param('action', null, PARAM_ALPHA);

        switch ($action) {
            case 'confirm':
                $this->process_confirm($ownerid);
                break;
            default:
                $this->process_cleanup($ownerid);
                break;
        }
    }

    /**
     * Display overview cleanup form
     *
     * @param int $owner
     */
    protected function process_cleanup($owner) {
        global $CFG;
        $redirect = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->page->course->id));
        // Create form.
        $mform = new \block_coupon\forms\coupon\cleanup($this->page->url, array('ownerid' => $owner));

        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            // Redirect to confirmation.
            $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/cleanup.php',
                    array('id' => $this->page->url->param('id'), 'data' => base64_encode(json_encode($data)),
                        'action' => 'confirm'));
            redirect($redirect);
        }

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_tabs($this->page->context, 'cpcleaner', array('id' => $this->page->url->param('id')));
        echo html_writer::end_div();
        echo $mform->render();
        echo html_writer::end_div();
        echo $this->output->footer();
    }

    /**
     * Process cleanup confirmation
     *
     * @param int $owner
     */
    protected function process_confirm($owner) {
        global $CFG;
        $data = required_param('data', PARAM_RAW);
        $redirect = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/cleanup.php',
                    array('id' => $this->page->url->param('id')));

        $options = json_decode(base64_decode($data));
        // FORCE ownerid in options, this prevents injection/security issues in ownership.
        $options->ownerid = (empty($owner) ? 0 : $owner);
        $confirmdata = new \block_coupon\output\component\cleanupconfirm($options);
        $rendered = $this->renderer->render_from_template('block_coupon/cleanupconfirm',
                $confirmdata->export_for_template($this->renderer));

        $formoptions = [
            get_string('cleanup:confirm:header', 'block_coupon'),
            $rendered,
            get_string('cleanup:confirm:confirmmessage', 'block_coupon')
        ];
        $url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/cleanup.php',
                    array('id' => $this->page->url->param('id'), 'data' => base64_encode(json_encode($options)),
                        'action' => 'confirm'));
        $mform = new \block_coupon\forms\confirmation($url, $formoptions);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($formdata = $mform->get_data()) {
            if ((bool) $formdata->confirm) {
                $count = \block_coupon\helper::cleanup_coupons($options);
                \core\notification::success(get_string('coupons:cleaned', 'block_coupon', $count));
            }
            redirect($redirect);
        }

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_tabs($this->page->context, 'cpcleaner', array('id' => $this->page->url->param('id')));
        echo html_writer::end_div();
        echo $mform->render();
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
