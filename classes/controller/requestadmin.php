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
 * File         requestadmin.php
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
 * block_coupon\manager\requestadmin
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class requestadmin {

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
            case 'deleteuser':
                $this->process_delete_user();
                break;
            case 'adduser':
                $this->process_add_user();
                break;
            case 'edituser':
                $this->process_edit_user();
                break;
            case 'users':
                $this->process_user_overview();
                break;
            case 'acceptrequest':
                $this->process_accept_request();
                break;
            case 'denyrequest':
                $this->process_deny_request();
                break;
            case 'requests':
            default:
                $this->process_request_overview();
                break;
        }
    }

    /**
     * Display user overview table
     */
    protected function process_user_overview() {
        $table = new \block_coupon\tables\requestusers();
        $table->baseurl = new \moodle_url($this->get_url(['action' => 'users']));

        $addurl = $this->get_url(['action' => 'adduser']);

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequestusers', $this->page->url->params());
        echo \html_writer::link($addurl, get_string('str:request:adduser', 'block_coupon'));
        echo '<br/>';
        echo $table->render(25);
        echo $this->output->footer();
    }

    /**
     * Process edit requestuser instance
     */
    protected function process_add_user() {
        global $DB;
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'users']);
        }

        $params = array('action' => 'adduser');
        $url = $this->get_url($params);

        $mform = new \block_coupon\forms\request\adduser($url);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            // Create record and redirect to edit user.
            $options = null;
            $record = new \stdClass();
            $record->id = 0;
            $record->userid = $data->userid;
            $record->configuration = json_encode($options);
            $record->timecreated = time();
            $record->timemodified = $record->timecreated;
            $record->id = $DB->insert_record('block_coupon_rusers', $record);

            $next = $this->get_url(['action' => 'edituser', 'itemid' => $record->id]);
            redirect($next);
        }

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequestusers', $this->page->url->params());
        $mform->display();
        echo $this->output->footer();
    }

    /**
     * Process edit requestuser instance
     */
    protected function process_edit_user() {
        global $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'users']);
        }

        $params = array('action' => 'edituser', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_rusers', ['id' => $itemid]);
        $user = \core_user::get_user($instance->userid);

        $mform = new \block_coupon\forms\request\user($url, [$instance, $user]);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            if (empty($instance->options)) {
                $options = new \stdClass;
            } else {
                $options = json_decode($instance->options);
            }
            // Merge allowed courses.
            if (!empty($data->course)) {
                $this->merge_options($options, 'courses', $data->course, true, true);
            }
            // Merge other options.
            $this->merge_options($options, 'allowselectlogo', $data->allowselectlogo, false, true);
            if (!empty($data->logo)) {
                $this->merge_options($options, 'logo', $data->logo, false, true);
            }

            $this->merge_options($options, 'allowselectrole', $data->allowselectrole, false, true);
            if (!empty($data->role)) {
                $this->merge_options($options, 'role', $data->role, false, true);
            }

            $this->merge_options($options, 'allowselectseperatepdf', $data->allowselectseperatepdf, false, true);
            $this->merge_options($options, 'seperatepdfdefault', $data->seperatepdfdefault, false, true);

            $this->merge_options($options, 'allowselectqr', $data->allowselectqr, false, true);
            $this->merge_options($options, 'qrdefault', $data->qrdefault, false, true);

            $this->merge_options($options, 'allowselectenrolperiod', $data->allowselectenrolperiod, false, true);
            $this->merge_options($options, 'enrolperioddefault', $data->enrolperioddefault, false, true);

            // Finish up and save.
            $instance->configuration = json_encode($options);
            $instance->timemodified = time();
            $DB->update_record('block_coupon_rusers', $instance);
            redirect($redirect);
        }

        // We MUST do this here, IN form definition() causes a rest of set values...
        // See https://tracker.moodle.org/browse/MDL-53889.
        $configuration = json_decode($instance->configuration);
        if (empty($configuration)) {
            $configuration = new \stdClass();
        }
        if (!empty($configuration->courses)) {
            $configuration->course = $configuration->courses;
        } else {
            $configuration->course = [];
        }
        $mform->set_data($configuration);

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequestusers', $this->page->url->params());
        $mform->display();
        echo $this->output->footer();
    }

    /**
     * Merge options data
     *
     * @param \stdClass|array $options the options
     * @param string $key key in options
     * @param mixed $data data to merge
     * @param bool $ismulti is this array based data?
     * @param bool $makeunique are the data keys/values supposed to be unique?
     */
    private function merge_options(&$options, $key, $data, $ismulti = false, $makeunique = true) {
        if (empty($options->{$key})) {
            if ($ismulti && is_scalar($data)) {
                $data = [$data];
            }
            $options->{$key} = $data;
        } else {
            $newdata = $options->{$key};
            if ($ismulti) {
                // Merge.
                $toobject = false;
                if (is_object($newdata) || is_object($data)) {
                    $toobject = true;
                }
                if (is_scalar($data)) {
                    $data = [$data];
                }
                $newdata = (array)$newdata;
                $data = (array)$data;
                $newdata = array_merge($newdata, $data);
                if ($makeunique) {
                    $newdata = array_unique($newdata);
                }
                if ($toobject) {
                    $newdata = (object)$newdata;
                }
            } else {
                // Simple overwrite.
                $newdata = $data;
            }
            $options->{$key} = $newdata;
        }
    }

    /**
     * Process delete requestuser instance
     */
    protected function process_delete_user() {
        global $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'users']);
        }

        $params = array('action' => 'deleteuser', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_rusers', ['id' => $itemid]);
        $user = \core_user::get_user($instance->userid);

        $options = [
            get_string('delete:requestuser:header', 'block_coupon', $user),
            get_string('delete:requestuser:description', 'block_coupon', $user),
            get_string('delete:requestuser:confirmmessage', 'block_coupon', $user)
        ];
        $mform = new \block_coupon\forms\confirmation($url, $options);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            if ((bool) $data->confirm) {
                $DB->delete_records('block_coupon_rusers', ['id' => $itemid]);
            }
            redirect($redirect);
        }
        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequestusers', $this->page->url->params());
        $mform->display();
        echo $this->output->footer();
    }

    /**
     * Display requests overview table
     */
    protected function process_request_overview() {
        $table = new \block_coupon\tables\requests();
        $table->baseurl = new \moodle_url($this->page->url->out());

        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequests', $this->page->url->params());
        echo $table->render(25);
        echo $this->output->footer();
    }

    /**
     * Process deny request instance
     */
    protected function process_deny_request() {
        global $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'requests']);
        }

        $params = array('action' => 'denyrequest', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_requests', ['id' => $itemid]);
        $user = \core_user::get_user($instance->userid);

        $options = [
            get_string('request:deny:heading', 'block_coupon', $user),
            $this->renderer->requestdetails($instance)
        ];
        $mform = new \block_coupon\forms\request\deny($url, $options);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            $DB->delete_records('block_coupon_requests', ['id' => $itemid]);
            // Send message if applicable.
            $from = \core_user::get_noreply_user();
            $subject = get_string('request:deny:subject', 'block_coupon');
            $messagehtml = $data->message['text'];
            $messagetext = format_text_email($messagehtml, FORMAT_MOODLE);
            \block_coupon\helper::do_email_to_user($user, $from, $subject, $messagetext, $messagehtml);
            redirect($redirect);
        }
        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequests', $this->page->url->params());
        $mform->display();
        echo $this->output->footer();
    }

    /**
     * Process deny request instance
     */
    protected function process_accept_request() {
        global $DB;
        $itemid = required_param('itemid', PARAM_INT);
        $redirect = optional_param('redirect', null, PARAM_LOCALURL);
        if (empty($redirect)) {
            $redirect = $this->get_url(['action' => 'requests']);
        }

        $params = array('action' => 'acceptrequest', 'itemid' => $itemid);
        $url = $this->get_url($params);

        $instance = $DB->get_record('block_coupon_requests', ['id' => $itemid]);
        $user = \core_user::get_user($instance->userid);

        $options = [
            get_string('request:accept:heading', 'block_coupon', $user),
            $this->renderer->requestdetails($instance)
        ];
        $mform = new \block_coupon\forms\request\accept($url, $options);
        if ($mform->is_cancelled()) {
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            $dbt = $DB->start_delegated_transaction();
            try {
                $record = $DB->get_record('block_coupon_requests', ['id' => $itemid]);
                $options = unserialize($record->configuration);
                $generator = new \block_coupon\coupon\generator();
                $generator->generate_coupons($options);

                $DB->delete_records('block_coupon_requests', ['id' => $record->id]);

                // Generate and send off.
                $coupons = $DB->get_records_list('block_coupon', 'id', $generator->get_generated_couponids());
                \block_coupon\helper::mail_requested_coupons($user, $coupons,
                        $options, $data->message['text']);

                $dbt->allow_commit();
            } catch (\Exception $ex) {
                $dbt->rollback($ex);
            }
            redirect($redirect);
        }
        echo $this->output->header();
        echo $this->renderer->get_tabs($this->page->context, 'cprequests', $this->page->url->params());
        $mform->display();
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
