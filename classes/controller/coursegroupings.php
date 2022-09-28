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
 * File         coursegroupings.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller;

/**
 * block_coupon\manager\coursegroupings
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursegroupings {

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
                $this->process_coursegrouping_edit();
                break;
            case 'details':
                $this->process_coursegrouping_details();
                break;
            case 'delete':
                $this->process_coursegrouping_delete();
                break;
            case 'list':
            default:
                $this->process_coursegrouping_overview();
                break;
        }
    }

    /**
     * Display user overview table
     */
    protected function process_coursegrouping_overview() {
        $table = new \block_coupon\tables\coursegroupings();
        $table->baseurl = new \moodle_url($this->page->url->out());

        $newurl = $this->get_url(['action' => 'add']);

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cpcoursegroupings', $this->page->url->params());
        echo \html_writer::link($newurl, get_string('str:coursegroupings:add', 'block_coupon'));
        echo '<br/>';
        echo $table->render(25);
        echo $this->output->footer();
    }

    /**
     * Process delete coursegroupingsuser instance
     */
    protected function process_coursegrouping_delete() {
        global $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'list']);
        }

        $params = array('action' => 'delete', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_coursegroupings', ['id' => $itemid]);

        $options = [
            get_string('delete:coursegrouping:header', 'block_coupon', $instance),
            $this->get_coursegroupingsdetails($instance, true),
            get_string('delete:coursegrouping:confirmmessage', 'block_coupon', $instance)
        ];
        $mform = new \block_coupon\forms\confirmation($url, $options);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            if ((bool) $data->confirm) {
                $DB->delete_records('block_coupon_coursegroupings', ['id' => $itemid]);
                $DB->delete_records('block_coupon_groupings', ['coursegroupingid' => $itemid]);
                $DB->delete_records('block_coupon_cgcourses', ['coursegroupingid' => $itemid]);
            }

            \core\notification::success(get_string('delete:coursegrouping:successmsg', 'block_coupon', $instance));

            redirect($redirect);
        }
        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cpcoursegroupings', $this->page->url->params());
        $mform->display();
        echo $this->output->footer();
    }

    /**
     * Process edit coursegroupingsuser instance
     */
    protected function process_coursegrouping_edit() {
        global $CFG, $DB, $USER;
        $itemid = optional_param('itemid', null, PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url();
        }

        if (empty($itemid)) {
            $params = array('action' => 'add');
        } else {
            $params = array('action' => 'edit', 'itemid' => $itemid);
        }
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_coursegroupings', ['id' => $itemid]);
        if (empty($instance)) {
            $instance = new \stdClass;
            $instance->id = 0;
        }
        $gcourseids = $DB->get_fieldset_select('block_coupon_cgcourses', 'courseid', 'coursegroupingid = ?', [$itemid]);

        $mform = new \block_coupon\forms\coursegrouping\edit($url, [$instance]);

        // We MUST do this here, IN form definition() causes a rest of set values...
        // See https://tracker.moodle.org/browse/MDL-53889.
        $instance->course = $gcourseids;
        $mform->set_data($instance);

        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            $dbt = $DB->start_delegated_transaction();

            $instance->name = $data->name;
            $instance->idnumber = $data->idnumber;
            $instance->maxamount = $data->maxamount;
            if (empty($instance->id)) {
                $instance->timecreated = time();
                $instance->timemodified = $instance->timecreated;
                $instance->id = $DB->insert_record('block_coupon_coursegroupings', $instance);
            } else {
                $instance->timemodified = $instance->timecreated;
                $DB->update_record('block_coupon_coursegroupings', $instance);
            }

            // Link courses.
            $deletecourseids = array_diff($gcourseids, $data->course);
            if (!empty($deletecourseids)) {
                list($insql, $params) = $DB->get_in_or_equal($deletecourseids, SQL_PARAMS_NAMED, 'cid', true, 0);
                $params['coursegroupingid'] = $instance->id;
                $select = "courseid {$insql} AND coursegroupingid = :coursegroupingid";
                $DB->delete_records_select('block_coupon_cgcourses', $select, $params);
            }
            $addcourseids = array_diff($data->course, $gcourseids);
            foreach ($addcourseids as $cid) {
                $record = new \stdClass;
                $record->coursegroupingid = $instance->id;
                $record->courseid = $cid;
                $record->timecreated = time();
                $record->timemodified = $instance->timecreated;
                $DB->insert_record('block_coupon_cgcourses', $record);
            }

            $dbt->allow_commit();

            // SUCCESS!
            redirect($redirect);
        }

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cpcoursegroupings', $this->page->url->params());
        echo $mform->render();
        echo $this->output->footer();
    }

    /**
     * Process details view
     */
    protected function process_coursegrouping_details() {
        global $CFG, $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'list']);
        }

        $params = array('action' => 'details', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_coursegroupings', ['id' => $itemid]);

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cpcoursegroupings', $this->page->url->params());

        echo $this->get_coursegroupingsdetails($instance);

        echo $this->output->footer();
    }

    /**
     * Get the detailview
     *
     * @param stdClass $instance
     * @param bool $noactions
     * @return string rendered content
     */
    protected function get_coursegroupingsdetails($instance, $noactions = false) {
        global $DB;
        $instance->courses = [];
        $gcourseids = $DB->get_fieldset_select('block_coupon_cgcourses', 'courseid', 'coursegroupingid = ?', [$instance->id]);
        $courses = $DB->get_records_list('course', 'id', $gcourseids, '', 'id, shortname, fullname');
        foreach ($courses as $course) {
            $course->deleteaction = false;
            $instance->courses[] = $course;
        }
        $instance->actions = [];
        if (!$noactions) {
            $delete = \html_writer::link($this->get_url(['action' => 'delete', 'itemid' => $instance->id]),
                    \html_writer::img($this->output->image_url('i/delete'),
                            get_string('action:coursegrouping:delete', 'block_coupon'),
                            ['class' => 'icon action-icon']));
            $instance->actions[] = $delete;

            $edit = \html_writer::link($this->get_url(['action' => 'edit', 'itemid' => $instance->id]),
                    \html_writer::img($this->output->image_url('i/edit'),
                            get_string('action:coursegrouping:edit', 'block_coupon'),
                            ['class' => 'icon action-icon']));
            $instance->actions[] = $edit;
        }

        return $this->renderer->render_from_template('block_coupon/coursegroupingdetails', $instance);
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
