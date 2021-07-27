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
 * Renderer for the coupon block.
 *
 * File         renderer.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Renderer for the coupon block.
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_coupon_renderer extends plugin_renderer_base {

    /**
     * Return rendered request details
     * @param stdClass $request
     */
    public function requestdetails($request) {
        $widget = new \block_coupon\output\component\requestdetails($request);
        return $this->render_requestdetails($widget);
    }

    /**
     * Render request details
     * @param \block_coupon\output\component\requestdetails $widget
     */
    public function render_requestdetails(\block_coupon\output\component\requestdetails $widget) {
        $context = $widget->export_for_template($this);
        return $this->render_from_template('block_coupon/requestdetails', $context);
    }

    /**
     * Render report page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    public function page_report($id, $ownerid = null) {
        // Table instance.
        $table = new \block_coupon\tables\report($ownerid);
        $table->baseurl = $this->page->url;

        $filtering = new \block_coupon\tablefilters\report($this->page->url);
        $table->set_filtering($filtering);

        $table->is_downloadable(true);
        $table->show_download_buttons_at(array(TABLE_P_BOTTOM, TABLE_P_TOP));
        $download = optional_param('download', '', PARAM_ALPHA);
        if (!empty($download)) {
            $table->is_downloading($download, 'couponreport', 'couponreport');
            $table->render(25, true);
            exit;
        }

        $out = '';
        $out .= $this->header();
        $out .= html_writer::start_div('block-coupon-container');
        $out .= html_writer::start_div();
        $out .= $this->get_tabs($this->page->context, 'cpreport', array('id' => $id));
        $out .= html_writer::end_div();
        ob_start();
        $filtering->display_add();
        $filtering->display_active();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= html_writer::end_div();
        $out .= $this->footer();
        return $out;
    }

    /**
     * Render error report page (including header / footer).
     *
     * @param int $id block instance id
     * @param int $ownerid the owner id of the coupons. Set 0 or NULL to see all.
     * @return string
     */
    public function page_error_report($id, $ownerid = null) {
        // Table instance.
        $table = new \block_coupon\tables\errorreport($ownerid);
        $table->baseurl = $this->page->url;

        $filtering = new \block_coupon\tablefilters\errorreport($this->page->url);
        $table->set_filtering($filtering);

        $out = '';
        $out .= $this->header();
        $out .= html_writer::start_div('block-coupon-container');
        $out .= html_writer::start_div();
        $out .= $this->get_tabs($this->page->context, 'cperrorreport', array('id' => $id));
        $out .= html_writer::end_div();
        ob_start();
        $filtering->display_add();
        $filtering->display_active();
        $table->render(25);
        $out .= ob_get_clean();
        $out .= html_writer::end_div();
        $out .= $this->footer();
        return $out;
    }

    /**
     * Create a tab object with a nice image view, instead of just a regular tabobject
     *
     * @param string $id unique id of the tab in this tree, it is used to find selected and/or inactive tabs
     * @param string $pix image name
     * @param string $component component where the image will be looked for
     * @param string|moodle_url $link
     * @param string $text text on the tab
     * @param string $title title under the link, by defaul equals to text
     * @param bool $linkedwhenselected whether to display a link under the tab name when it's selected
     * @return \tabobject
     */
    protected function create_pictab($id, $pix = null, $component = null, $link = null,
            $text = '', $title = '', $linkedwhenselected = false) {
        $img = '';
        if ($pix !== null) {
            $img = $this->image_icon($pix, $title, empty($component) ? 'moodle' : $component, ['class' => 'icon']);
        }
        return new \tabobject($id, $link, $img . $text, empty($title) ? $text : $title, $linkedwhenselected);
    }

    /**
     * Generate navigation tabs
     *
     * @param \context $context current context to work in (needed to determine capabilities).
     * @param string $selected selected tab
     * @param array $params any paramaters needed for the base url
     */
    public function get_tabs($context, $selected, $params = array()) {
        global $CFG;
        $tabs = array();
        // Add exclusions.
        $tabs[] = $this->create_pictab('wzcoupons', 'e/print', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/index.php', $params),
                get_string('tab:wzcoupons', 'block_coupon'));
        $tabs[] = $this->create_pictab('wzcouponimage', 'e/insert_edit_image', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/managelogos.php', $params),
                get_string('tab:wzcouponimage', 'block_coupon'));

        $requesttab = $this->create_pictab('cprequestadmin', 'i/checkpermissions', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/admin.php', $params),
                get_string('tab:requests', 'block_coupon'));
        $requesttab->subtree[] = $this->create_pictab('cprequestusers', 'i/users', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/admin.php', $params + ['action' => 'users']),
                get_string('tab:requestusers', 'block_coupon'));
        $requesttab->subtree[] = $this->create_pictab('cprequests', 'e/help', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/admin.php', $params + ['action' => 'requests']),
                get_string('tab:requests', 'block_coupon'));
        $tabs[] = $requesttab;

        $tabs[] = $this->create_pictab('cpreport', 'i/report', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/reports.php', $params),
                get_string('tab:report', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpunused', 'i/completion-manual-n', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php',
                array_merge($params, array('tab' => 'unused'))),
                get_string('tab:unused', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpused', 'i/completion-manual-enabled', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php',
                array_merge($params, array('tab' => 'used'))),
                get_string('tab:used', 'block_coupon'));
        if (get_config('block_coupon', 'seperatepersonalcoupontab')) {
            $tabs[] = $this->create_pictab('cppersonal', 'i/permissionlock', '',
                    new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php',
                    array_merge($params, array('tab' => 'personal'))),
                    get_string('tab:personalcoupons', 'block_coupon'));
        }
        $tabs[] = $this->create_pictab('cperrorreport', 'i/warning', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/errorreport.php',
                array_merge($params, array('tab' => 'cperrorreport'))),
                get_string('tab:errors', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpcleaner', 'e/cleanup_messy_code', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/cleanup.php',
                array_merge($params, array('tab' => 'cpcleaner'))),
                get_string('tab:cleaner', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpbatchlist', 'i/down', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/downloadbatchlist.php',
                array_merge($params, array('tab' => 'cpbatchlist'))),
                get_string('tab:downloadbatchlist', 'block_coupon'));
        $tabs[] = $this->create_pictab('cpmaillog', 'i/warning', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/maillog.php',
                array_merge($params, array('tab' => 'maillog'))),
                get_string('tab:maillog', 'block_coupon'));
        return $this->tabtree($tabs, $selected);
    }

    /**
     * Generate navigation tabs
     *
     * @param \context $context current context to work in (needed to determine capabilities).
     * @param string $selected selected tab
     * @param array $params any paramaters needed for the base url
     */
    public function get_my_requests_tabs($context, $selected, $params = array()) {
        global $CFG;
        $tabs = array();

        $requesttab = $this->create_pictab('cpmyrequests', 'i/checkpermissions', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php', $params),
                get_string('tab:requests', 'block_coupon'));
        $requesttab->subtree[] = $this->create_pictab('myrequests', null, '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php',
                        $params + ['action' => 'list']),
                get_string('tab:listrequests', 'block_coupon'));
        switch ($selected) {
            case 'newrequest':
                $requesttab->subtree[] = $this->create_pictab('newrequest', null, '',
                        new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php',
                                $params + ['action' => 'newrequest']),
                        get_string('str:request:add', 'block_coupon'));
                break;
            case 'delete':
                $requesttab->subtree[] = $this->create_pictab('delete', null, '',
                        new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php',
                                $params + ['action' => 'delete']),
                        get_string('delete:request:header', 'block_coupon'));
                break;
            case 'details':
                $requesttab->subtree[] = $this->create_pictab('details', null, '',
                        new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php',
                                $params + ['action' => 'details']),
                        get_string('str:request:details', 'block_coupon'));
                break;
        }
        $tabs[] = $requesttab;

        $batchlisttab = $this->create_pictab('cpmybatches', 'i/down', '',
                new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php',
                        $params + ['action' => 'batchlist']),
                get_string('tab:downloadbatchlist', 'block_coupon'));
        $tabs[] = $batchlisttab;

        return $this->tabtree($tabs, $selected);
    }



    /**
     * Renders an action_icon.
     *
     * @param string|moodle_url $url A string URL or moodle_url
     * @param pix_icon $pixicon
     * @param component_action $action
     * @param array $attributes associative array of html link attributes + disabled
     * @param bool $linktext show title next to image in link
     * @param bool $iconbeforetext override default Moodle to place icon BEFORE text
     * @return string HTML fragment
     */
    public function action_icon($url, pix_icon $pixicon, component_action $action = null,
            array $attributes = null, $linktext = false, $iconbeforetext = false) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $attributes = (array) $attributes;

        if (empty($attributes['class'])) {
            // Let ppl override the class via $options.
            $attributes['class'] = 'action-icon';
        }

        $icon = $this->render($pixicon);

        if ($linktext) {
            $text = $pixicon->attributes['alt'];
        } else {
            $text = '';
        }

        if ($iconbeforetext) {
            return $this->action_link($url, $icon . $text, $action, $attributes);
        } else {
            return $this->action_link($url, $text . $icon, $action, $attributes);
        }
    }

}
