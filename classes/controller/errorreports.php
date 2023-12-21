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
 * File         errorreports.php
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

use html_writer;

/**
 * block_coupon\manager\errorreports
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class errorreports {

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
        $action = optional_param('action', 'list', PARAM_ALPHANUM);

        switch ($action) {
            case 'delete':
                $this->process_delete();
                break;

            case 'list':
            default:
                $this->process_overview();
                break;
        }
    }

    /**
     * Display process progress report
     */
    protected function process_overview() {
        global $USER;
        $ownerid = (has_capability('block/coupon:viewallreports', $this->page->context) ? 0 : $USER->id);

        // Table instance.
        $table = new \block_coupon\tables\errorreport($ownerid);
        $table->baseurl = $this->page->url;

        $filtering = new \block_coupon\tablefilters\errorreport($this->page->url);
        $table->set_filtering($filtering);

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_tabs($this->page->context, 'cperrorreport');
        echo html_writer::end_div();
        $filtering->display_add();
        $filtering->display_active();
        echo $table->render(25);
        echo html_writer::end_div();
        echo $this->output->footer();
    }

    /**
     * Display process delete
     */
    protected function process_delete() {
        global $DB;
        require_capability('block/coupon:viewallreports', $this->page->context);
        $itemid = required_param('itemid', PARAM_INT);
        $DB->delete_records('block_coupon_errors', array('id' => $itemid));
        redirect($this->get_url());
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
