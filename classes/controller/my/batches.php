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
 * My batches controller
 *
 * File         batches.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller\my;

use html_writer;

/**
 * block_coupon\manager\my\batches
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batches {

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
            case 'list':
            default:
                $this->process_batchlist_overview();
                break;
        }
    }

    /**
     * Display user overview table
     */
    protected function process_batchlist_overview() {
        global $USER;
        // Table instance.
        $table = new \block_coupon\tables\downloadbatchlist($this->page->context, $USER->id);
        $table->baseurl = $this->page->url;

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_my_tabs($this->page->context, 'cpmybatches', $this->page->url->params());
        echo html_writer::end_div();
        echo $table->render(999999);
        echo html_writer::end_div();
        echo $this->output->footer();
    }

}
