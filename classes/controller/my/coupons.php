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
 * My coupons controller
 *
 * File         coupons.php
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
 * block_coupon\manager\my\coupons
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coupons {

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
        $config = get_config('block_coupon');
        if (empty($config->enablemycouponsforru)) {
            echo $this->output->header();
            echo "<div class=\"alert alert-danger\">" .
                    get_string('err:tab:enablemycouponsforru', 'block_coupon') .
                    "</div>";
            echo $this->output->footer();
        }

        $action = optional_param('action', null, PARAM_ALPHA);
        switch ($action) {
            case 'unused':
                $this->process_unused_overview();
                break;
            case 'used':
            default:
                $this->process_used_overview();
                break;
        }
    }

    /**
     * Display user overview table
     */
    protected function process_used_overview() {
        global $USER;
        $table = new \block_coupon\tables\mycoupons($USER->id, \block_coupon\tables\coupons::USED);
        $table->baseurl = $this->get_url(['action' => 'used']);
        $table->set_noactions(true);

        // We're limiting filter fields...
        $fields = [
            'mybatchselect' => 0,
            'batchid' => 1,
            'timemodified' => 1,
            'couponcode' => 1,
            'course' => 1,
            'coursegroup' => 1,
            'cohort' => 1,
        ];
        $filtering = new \block_coupon\tablefilters\coupons($table->baseurl, null, $fields);
        $table->set_filtering($filtering);

        $table->is_downloadable(true);
        $table->show_download_buttons_at(array(TABLE_P_BOTTOM, TABLE_P_TOP));
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'couponreport', 'couponreport');
            $table->render(25, true);
            exit;
        }

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_my_tabs($this->page->context, 'mycoupons-used', $this->page->url->params());
        echo html_writer::end_div();
        echo html_writer::start_div();
        $filtering->display_add();
        $filtering->display_active();
        echo html_writer::end_div();
        echo $table->render(25);
        echo html_writer::end_div();
        echo $this->output->footer();
    }

    /**
     * Display user overview table
     */
    protected function process_unused_overview() {
        global $USER;
        $table = new \block_coupon\tables\mycoupons($USER->id, \block_coupon\tables\coupons::UNUSED);
        $table->baseurl = $this->get_url(['action' => 'unused']);
        $table->set_noactions(true);

        // We're limiting filter fields...
        $fields = [
            'mybatchselect' => 0,
            'batchid' => 1,
            'timemodified' => 1,
            'couponcode' => 1,
            'course' => 1,
            'coursegroup' => 1,
            'cohort' => 1,
        ];
        $filtering = new \block_coupon\tablefilters\coupons($table->baseurl, null, $fields);
        $table->set_filtering($filtering);

        $table->is_downloadable(true);
        $table->show_download_buttons_at(array(TABLE_P_BOTTOM, TABLE_P_TOP));
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'couponreport', 'couponreport');
            $table->render(25, true);
            exit;
        }

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_my_tabs($this->page->context, 'mycoupons-unused', $this->page->url->params());
        echo html_writer::end_div();
        echo html_writer::start_div();
        $filtering->display_add();
        $filtering->display_active();
        echo html_writer::end_div();
        echo $table->render(25);
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
