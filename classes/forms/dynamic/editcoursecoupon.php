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
class editcoursecoupon extends dynamic_form {
    /**
     * @var int coupon id.
     */
    protected $id;

    /**
     * @var array of coupon instances
     */
    protected $coupon;

    /**
     * @var array of coupon link instances
     */
    protected $links;
    /**
     * @var array
     */
    protected $coursedata;

    /**
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', "id");
        $mform->setType("id", PARAM_INT);
        $mform->addElement('hidden', "typ");
        $mform->setType("typ", PARAM_ALPHANUMEXT);

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
            $mform->addElement('static', '_lnowncourses', '', get_string('editcoursecoupon:known:desc', 'block_coupon'));
            $first = true;
            foreach ($this->coursedata as $course) {
                $name = format_string($course->fullname, true, ['context' => \context_course::instance($course->id)]);
                if (!empty($course->idnumber)) {
                    $name .= " ({$course->idnumber})";
                }
                if ($first) {
                    $mform->addElement(
                        'advcheckbox',
                        'ecourse[' . $course->id . ']',
                        get_string('editcoursecoupon:known', 'block_coupon'),
                        $name
                    );
                } else {
                    $mform->addElement('advcheckbox', 'ecourse[' . $course->id . ']', $name);
                }
                $first = false;
            }

            $mform->addElement(
                'static',
                '_courses',
                '',
                get_string('editcoursecoupon:add:desc', 'block_coupon') .
                '<br/>' . get_string('searchcourses:desc', 'block_coupon')
            );
            $mform->addElement('findcourses', 'courses', get_string('course'), ['multiple' => true]);
        }
    }

    /**
     * Initializer
     */
    protected function initialize() {
        global $DB, $CFG;

        \MoodleQuickForm::registerElementType(
            'findcourses',
            $CFG->dirroot . '/blocks/coupon/classes/forms/element/findcourses.php',
            '\\block_coupon\\forms\\element\\findcourses'
        );

        $this->errorlist = [];
        $this->coursedata = [];
        $this->id = $this->required_param('id', PARAM_INT);
        $this->typ = $this->required_param('typ', PARAM_ALPHANUMEXT);
        $this->coupon = $DB->get_record('block_coupon', ['id' => $this->id]);
        if (!$this->coupon->typ == 'course') {
            $this->errorlist[] = get_string('err:editcoupon:notcoursecoupon', 'block_coupon');
        }
        $this->links = $DB->get_records('block_coupon_courses', ['couponid' => $this->id]);
        $this->coursedata = $DB->get_records_list('course', 'id', array_column($this->links, 'courseid'));
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
        global $DB;
        $this->initialize();
        $data = $this->get_data();

        $dbt = $DB->start_delegated_transaction();

        // Remove some.
        foreach ($data->ecourse as $cid => $keep) {
            if (!$keep) {
                $DB->delete_records('block_coupon_courses', ['couponid' => $data->id, 'courseid' => $cid]);
            }
        }

        // Add some.
        foreach ($data->courses as $courseid) {
            $DB->insert_record('block_coupon_courses', (object)['couponid' => $this->id, 'courseid' => $courseid]);
        }

        $dbt->allow_commit();

        return true;
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

        $ec = [];
        foreach ($this->coursedata as $course) {
            $ec[$course->id] = 1;
        }
        $this->set_data([
            'id' => $this->id,
            'typ' => $this->typ,
            'ecourse' => $ec,
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

    /**
     * Checks if a parameter was passed in the previous form submission
     *
     * @param string $name the name of the page parameter we want, for example 'id' or 'element[sub][13]'
     * @param string $type expected type of parameter
     * @return mixed
     */
    protected function required_param($name, $type) {
        $p = $this->optional_param($name, null, $type);
        if ($p === null) {
            throw new \moodle_exception('missingparam', '', '', $name);
        }
        return $p;
    }
}
