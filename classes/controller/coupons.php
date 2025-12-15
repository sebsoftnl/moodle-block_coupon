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
 * File         coupons.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller;

use moodle_url;
use html_writer;

/**
 * block_coupon\manager\coupons
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
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
        global $CFG;
        $tab = optional_param('tab', null, PARAM_ALPHA);
        $action = optional_param('action', $tab, PARAM_ALPHA);
        $title = 'view:reports-' . $tab . ':title';
        $heading = 'view:reports-' . $tab . ':heading';

        $this->page->navbar->add(get_string($title, 'block_coupon'));

        $url = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php', ['tab' => $tab]);
        $this->page->set_url($url);
        $this->page->set_title(get_string($title, 'block_coupon'));
        $this->page->set_heading(get_string($heading, 'block_coupon'));

        switch ($action) {
            case 'delete':
                $this->process_delete();
                break;
            case 'personal':
                $filter = \block_coupon\tables\coupons::PERSONAL;
                $this->process_overview($filter);
                break;
            case 'used':
                $filter = \block_coupon\tables\coupons::USED;
                $this->process_overview($filter);
                break;
            case 'unused':
            default:
                $filter = \block_coupon\tables\coupons::UNUSED;
                $this->process_overview($filter);
                break;
        }
    }

    /**
     * Display overview table
     *
     * @param int $filter
     */
    protected function process_overview($filter) {
        global $USER;
        $ownerid = (has_capability('block/coupon:viewallreports', $this->page->context) ? 0 : $USER->id);
        $config = get_config('block_coupon');

        // Table instance.
        $table = new \block_coupon\tables\coupons($ownerid, $filter);
        $table->baseurl = $this->page->url;

        $skipfields = ['mybatchselect' => 1, 'coursegroup' => 1];
        switch ($filter) {
            case \block_coupon\tables\coupons::PERSONAL:
                $skipfields['claimee'] = 1;
                break;
            case \block_coupon\tables\coupons::USED:
                break;
            case \block_coupon\tables\coupons::UNUSED:
                $skipfields['claimee'] = 1;
                break;
        }

        $filtering = new \block_coupon\tablefilters\coupons($this->page->url, [], [], $skipfields);
        $table->set_filtering($filtering);

        $table->is_downloadable(true);
        $table->show_download_buttons_at([TABLE_P_BOTTOM, TABLE_P_TOP]);
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'coupons', 'coupons');
            $table->render(25);
            exit;
        }

        $selectedtab = '';
        $extrahtml = '';
        $massactionshtml = '';
        switch ($filter) {
            case \block_coupon\tables\coupons::UNUSED:
                $selectedtab = 'cpunused';
                $this->page->requires->js_call_amd('block_coupon/coupons/bulkactions', 'init', ['.block-coupon-container']);

                if ($config->enableeditcourses ?? false) {
                    $massactionshtml .= '<li data-action="replacecourses" class="dropdown-item">' .
                            get_string('replacecourses', 'block_coupon') . '</li>';
                }
                if ($config->enableeditcohorts ?? false) {
                    $massactionshtml .= '<li data-action="replacecohorts" class="dropdown-item">' .
                            get_string('replacecohorts', 'block_coupon') . '</li>';
                }
                if (!empty($massactionshtml)) {
                    $massactionshtml = '<div class="d-flex flex-row justify-content-center">
                    <div data-region="massactions">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" data-type="massaction">' .
                        get_string('massaction', 'block_coupon') . '
                             <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">' . $massactionshtml . '</ul></div></div>';
                }

                $extrahtml = '<div class="d-flex flex-row justify-content-center">
                    <div data-region="bulkactions">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" data-type="bulkaction">' .
                        get_string('withselectedcoupons', 'block_coupon') . '
                             (<span id="bulk-counter">0</span>)
                             <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li data-action="bulkdelete" class="dropdown-item">' . get_string('delete') . '</li>';
                if ($config->enableeditcourses ?? false) {
                    $extrahtml .= '<li data-action="editcourses" class="dropdown-item">' .
                            get_string('editcourses', 'block_coupon') . '</li>';
                }
                if ($config->enableeditcohorts ?? false) {
                    $extrahtml .= '<li data-action="editcohorts" class="dropdown-item">' .
                            get_string('editcohorts', 'block_coupon') . '</li>';
                }
                $extrahtml .= '</ul></div></div>';

                break;
            case \block_coupon\tables\coupons::USED:
                $selectedtab = 'cpused';
                break;
            case \block_coupon\tables\coupons::PERSONAL:
                $selectedtab = 'cppersonal';
                break;
        }

        echo $this->output->header();
        echo html_writer::start_div('block-coupon-container');
        echo html_writer::start_div();
        echo $this->renderer->get_tabs($this->page->context, $selectedtab);
        echo html_writer::end_div();
        $filtering->display_add();
        $filtering->display_active();
        echo $massactionshtml;
        echo $table->render(25);
        echo $extrahtml;
        echo html_writer::end_div();
        echo $this->output->footer();
    }

    /**
     * Process delete
     */
    protected function process_delete() {
        global $DB;
        require_sesskey();
        // If we can generate, we're also allowed to delete.
        require_capability('block/coupon:generatecoupons', $this->page->context);
        $id = required_param('itemid', PARAM_INT);

        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('block_coupon', ['id' => $id]);
        $DB->delete_records('block_coupon_cohorts', ['couponid' => $id]);
        $DB->delete_records('block_coupon_groups', ['couponid' => $id]);
        $DB->delete_records('block_coupon_courses', ['couponid' => $id]);
        $DB->delete_records('block_coupon_groupings', ['couponid' => $id]);
        $DB->delete_records('block_coupon_cgucourses', ['couponid' => $id]);
        $DB->delete_records('block_coupon_activities', ['couponid' => $id]);
        $DB->delete_records('block_coupon_errors', ['couponid' => $id]);
        $DB->commit_delegated_transaction($transaction);

        redirect($this->page->url, get_string('coupon:deleted', 'block_coupon'));
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
