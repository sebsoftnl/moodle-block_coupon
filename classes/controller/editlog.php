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
 * Maillog implementation for use with block_coupon
 *
 * File         editlog.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller;

use moodle_url;
use html_writer;
use core_table\local\filter\filter;
use core_table\local\filter\string_filter;
use core_table\local\filter\integer_filter;

/**
 * block_coupon\manager\editlog
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editlog {
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
        global $CFG;
        $tab = 'editlog';
        $action = optional_param('action', $tab, PARAM_ALPHA);
        $title = 'view:reports-' . $tab . ':title';
        $heading = 'view:reports-' . $tab . ':heading';

        $this->page->navbar->add(get_string($title, 'block_coupon'));

        $url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/editlog.php', ['tab' => $tab]);
        $this->page->set_url($url);
        $this->page->set_title(get_string($title, 'block_coupon'));
        $this->page->set_heading(get_string($heading, 'block_coupon'));

        switch ($action) {
            default:
                $this->process_overview();
                break;
        }
    }

    /**
     * Display overview table
     */
    protected function process_overview() {
        $fortype = optional_param('fortype', 'course', PARAM_ALPHANUMEXT);
        $aggregate = optional_param('aggregate', 1, PARAM_INTEGER); // Abusing aggregate for grouping :).

        $this->page->requires->js_call_amd('block_coupon/editlog/editlog', 'init', ['.block-coupon-editlog-container']);

        // Filterset.
        $filterset = new \block_coupon\table\editlog_filterset($this->page->url);
        $filterset->add_filter(new string_filter('fortype', filter::JOINTYPE_ALL, [$fortype]));
        $filterset->add_filter(new integer_filter('procaggregate', filter::JOINTYPE_ALL, [$aggregate]));
        $filterset->check_validity();

        // Table instance.
        $table = new \block_coupon\table\editlog();
        $table->set_filterset($filterset);

        $table->is_downloadable(true);
        $table->show_download_buttons_at([TABLE_P_BOTTOM, TABLE_P_TOP]);
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'editlog', 'editlog');
            $table->render(25, false);
        } else {
            echo $this->output->header();
            echo html_writer::start_div('block-coupon-editlog-container');
            echo html_writer::start_div();
            echo $this->renderer->get_tabs($this->page->context, 'cpeditlog' . $fortype . 's');
            echo html_writer::end_div();
            echo $table->render(25, false);
            echo html_writer::end_div();
            echo $this->output->footer();
        }
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
