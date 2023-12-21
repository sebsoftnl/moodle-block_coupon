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
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot . '/lib/formslib.php');

\MoodleQuickForm::registerElementType('bccolourpicker',
    $CFG->dirroot . '/blocks/coupon/classes/forms/element/colourpicker.php', '\\block_coupon\\forms\\element\\colourpicker');
use core_form\dynamic_form;

/**
 * The form for handling editing a template element.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_element_dform extends dynamic_form {

    /**
     * @var element The element object.
     */
    protected $element;

    /**
     * @var \block_coupon\template The template object.
     */
    protected $template;

    /**
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        $mform->updateAttributes(array('id' => 'editelementform'));

        $mform->addElement('hidden', 'id', $this->element->get_id());
        $mform->setType('id', PARAM_INT);

        // Add the field for the name of the element, this is required for all elements.
        $mform->addElement('text', 'name', get_string('elementname', 'block_coupon'), 'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', get_string('element:' . $this->element->element, 'block_coupon'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('name', 'elementname', 'block_coupon');

        $this->element->set_edit_element_form($this);
        $this->element->render_form_elements($mform);
    }

    /**
     * Fill in the current page data for this template.
     */
    public function definition_after_data() {
        $this->element->definition_after_data($this->_form);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    public function validation($data, $files) {
        $errors = array();

        if (\core_text::strlen($data['name']) > 255) {
            $errors['name'] = get_string('nametoolong', 'block_coupon');
        }

        $errors += $this->element->validate_form_elements($data, $files);

        return $errors;
    }
    /**
     * Returns context where this form is used
     *
     * This context is validated in {@see \external_api::validate_context()}
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
        global $DB;
        $elementid = $this->optional_param('id', 0, PARAM_INT);
        $element = $DB->get_record('block_coupon_elements', ['id' => $elementid]);
        $page = $DB->get_record('block_coupon_pages', ['id' => $element->pageid]);
        $this->element = element_factory::get_element_instance($element);
        $this->template = new \block_coupon\template($DB->get_record('block_coupon_templates', ['id' => $page->templateid]));

        return $this->template->get_context();
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
        $this->template->require_manage();
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
        $data = $this->get_data();

        $element = $DB->get_record('block_coupon_elements', ['id' => $data->id]);
        // Get an instance of the element class.
        if ($e = \block_coupon\template\element_factory::get_element_instance($element)) {
            $e->save_form_elements($data);
        }

        return $DB->get_record('block_coupon_elements', ['id' => $data->id]);
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
        // No-op, it's done in the definition....
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
        $elementid = $this->optional_param('id', 0, PARAM_INT);
        return new \moodle_url('/blocks/coupon/view/templates/edit_element.php', ['id' => $elementid]);
    }

}
