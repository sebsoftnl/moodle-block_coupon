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
 * This file contains the form for handling editing a template element.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\dynamic;

use core_form\dynamic_form;
use context_system;

/**
 * The form for handling editing a template element.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class revertcoursemod extends dynamic_form {
    /**
     * @var array of error messages
     */
    protected $errorlist;
    /**
     * @var array of warnings
     */
    protected $warninglist;
    /**
     * @var string
     */
    protected $typ;
    /**
     * @var int
     */
    protected $numitems;
    /**
     * @var \stdClass
     */
    protected $sourcecourse;
    /**
     * @var \stdClass
     */
    protected $targetcourse;
    /**
     * @var string
     */
    protected $descriptor;

    /**
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        $this->_form->addElement('hidden', 'procid');
        $this->_form->setType('procid', PARAM_ALPHANUMEXT);

        $this->_form->addElement('hidden', 'modid');
        $this->_form->setType('modid', PARAM_INT);

        // Force extra form class attribute.
        $oldclass = $this->_form->getAttribute('class');
        if (!empty($oldclass)) {
            $this->_form->updateAttributes(['class' => trim(str_replace('mform', '', $oldclass))
                . ' mform couponmodal']);
        } else {
            $this->_form->updateAttributes(['class' => 'mform couponmodal']);
        }

        if (count($this->errorlist) > 0) {
            // We'll only display the errors.
            foreach ($this->errorlist as $msg) {
                $mform->addElement('html', \html_writer::div($msg, 'alert alert-danger'));
            }
        } else {
            foreach ($this->warninglist as $msg) {
                $mform->addElement('html', \html_writer::div($msg, 'alert alert-warning'));
            }

            $mform->addElement('html', $this->descriptor);

            $mform->addElement('advcheckbox', 'confirm', get_string('revert:confirm', 'block_coupon'));
        }
    }

    /**
     * Initializer
     */
    protected function initialize() {
        global $DB;

        $this->numitems = 1;
        $this->errorlist = [];
        $this->warninglist = [];

        $procid = $this->optional_param('procid', null, PARAM_ALPHANUMEXT);
        $modid = $this->optional_param('modid', null, PARAM_INT);

        if (empty($procid) && empty($modid)) {
            $this->errorlist[] = get_string('err:revert:params', 'block_coupon');
        }

        if (!empty($procid)) {
            $this->numitems = $DB->count_records('block_coupon_modifications', ['procid' => $procid]);
            $onerecord = $DB->get_record('block_coupon_modifications', ['procid' => $procid], '*', IGNORE_MULTIPLE);
        } else {
            $onerecord = $DB->get_record('block_coupon_modifications', ['id' => $modid]);
        }

        $this->sourcecourse = $DB->get_record('course', ['id' => $onerecord->oldrefid]);
        $this->targetcourse = $DB->get_record('course', ['id' => $onerecord->newrefid]);
        if (empty($this->sourcecourse->id)) {
            $this->errorlist[] = get_string('err:revertcoursemod:sourcecoursenotexists', 'block_coupon');
        } else {
            $this->sourcecourse->fullname = format_string(
                $this->sourcecourse->fullname,
                true,
                ['context' => \context_course::instance($this->sourcecourse->id)]
            );
        }
        if (empty($this->targetcourse->id)) {
            $this->warninglist[] = get_string('err:revertcoursemod:targetcoursenotexists', 'block_coupon');
        } else {
            $this->targetcourse->fullname = format_string(
                $this->targetcourse->fullname,
                true,
                ['context' => \context_course::instance($this->targetcourse->id)]
            );
        }

        if (empty($this->errorlist)) {
            if (!empty($onerecord)) {
                if (empty($procid)) {
                    $extinfo = get_string('revert:mod', 'block_coupon', $onerecord->id);
                } else {
                    $extinfo = get_string('revert:proc', 'block_coupon', $onerecord->procid);
                }
                $a = (object)[
                    'extinfo' => $extinfo,
                    'numitems' => $this->numitems,
                    'fromname' => $this->sourcecourse->fullname,
                    'fromidnumber' => empty($this->sourcecourse->idnumber) ? '-' : $this->sourcecourse->idnumber,
                    'toname' => $this->targetcourse->fullname,
                    'toidnumber' => empty($this->targetcourse->idnumber) ? '-' : $this->targetcourse->idnumber,
                ];
                $this->descriptor = get_string('revertcoursemod:descriptor', 'block_coupon', $a);
            }
        }
    }

    /**
     * Returns context where this form is used
     *
     * This context is validated in {@see external_api::validate_context()}
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * Example:
     *     $cmid = $this->optional_param('cmid', 0, PARAM_INT);
     *     return context_module::instance($cmid);
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): \context {
        $this->initialize();
        return context_system::instance();
    }

    /**
     * Checks if current user has access to this form, otherwise throws exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * Example:
     *     require_capability('dosomething', $this->get_context_for_dynamic_submission());
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('block/coupon:administration', $this->get_context_for_dynamic_submission());
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * Example:
     *     $data = $this->get_data();
     *     file_postupdate_standard_filemanager($data, ....);
     *     api::save_entity($data); // Save into the DB, trigger event, etc.
     *
     * @return mixed
     */
    public function process_dynamic_submission() {
        $this->initialize();
        $data = $this->get_data();

        if ((bool)$data->confirm) {
            $notifications = [];
            if (!empty($data->procid)) {
                \block_coupon\helper::undo_course_modification($data->procid, true, $notifications);
            } else if (!empty($data->modid)) {
                \block_coupon\helper::undo_course_modification($data->modid, false, $notifications);
            }

            return (object)[
                'result' => true,
                'notifications' => $notifications,
            ];
        }

        return (object)['result' => false];
    }

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $id = $this->optional_param('id', 0, PARAM_INT);
     *     $data = api::get_entity($id); // For example, retrieve a row from the DB.
     *     file_prepare_standard_filemanager($data, ...);
     *     $this->set_data($data);
     */
    public function set_data_for_dynamic_submission(): void {
        $this->initialize();

        $procid = $this->optional_param('procid', '', PARAM_ALPHANUMEXT);
        $modid = $this->optional_param('modid', 0, PARAM_INT);

        $this->set_data([
            'procid' => $procid,
            'modid' => $modid,
        ]);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * Example:
     *     $id = $this->optional_param('id', 0, PARAM_INT);
     *     return new moodle_url('/my/page/where/form/is/used.php', ['id' => $id]);
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        $this->initialize();
        return new \moodle_url('/blocks/coupon/view/coupons.php');
    }
}
