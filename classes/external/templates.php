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
 * Webservices implementation for block_coupon
 *
 * File         externallib.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\external;

use stdClass;
use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;

/**
 * Webservices implementation for block_coupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class templates extends external_api {
    /**
     * Get all non-sidewide and visible courses.
     *
     * @param int $tid
     * @param array $values
     * @return array
     */
    public static function update_element_positions($tid, array $values = []) {
        global $DB;

        $params = self::validate_parameters(self::update_element_positions_parameters(), [
            'tid' => $tid,
            'values' => $values,
        ]);

        $template = $DB->get_record('block_coupon_templates', ['id' => $params['tid']]);
        $template = new \block_coupon\template($template);
        $context = $template->get_context();

        self::validate_context($context);
        require_capability('block/coupon:administration', $context);
        $template->require_manage();

        foreach ($params['values'] as $value) {
            $value = (object) $value;
            $element = new stdClass();
            $element->id = $value->id;
            $element->posx = $value->posx;
            $element->posy = $value->posy;
            $DB->update_record('block_coupon_elements', $element);
        }

        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function update_element_positions_parameters() {
        $struct = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'element id'),
            'posx' => new external_value(PARAM_INT, 'pos X'),
            'posy' => new external_value(PARAM_INT, 'pos Y'),
        ]);
        return new external_function_parameters([
            'tid' => new external_value(PARAM_INT, 'template id'),
            'values' => new external_multiple_structure($struct),
        ]);
    }

    /**
     * Returns description of method return parameters
     *
     * @return external_multiple_structure
     */
    public static function update_element_positions_returns() {
        return new external_value(PARAM_BOOL);
    }

    /**
     * Returns the save_element() parameters.
     *
     * @return external_function_parameters
     */
    public static function save_element_parameters() {
        return new external_function_parameters([
            'templateid' => new external_value(PARAM_INT, 'The template id'),
            'elementid' => new external_value(PARAM_INT, 'The element id'),
            'values' => new external_multiple_structure(
                new external_single_structure([
                    'name' => new external_value(PARAM_ALPHANUMEXT, 'The field to update'),
                    'value' => new external_value(PARAM_RAW, 'The value of the field'),
                ])
            ),
        ]);
    }

    /**
     * Handles saving element data.
     *
     * @param int $templateid The template id.
     * @param int $elementid The element id.
     * @param array $values The values to save
     * @return array
     */
    public static function save_element($templateid, $elementid, $values) {
        global $DB;

        $params = [
            'templateid' => $templateid,
            'elementid' => $elementid,
            'values' => $values,
        ];
        self::validate_parameters(self::save_element_parameters(), $params);

        $template = $DB->get_record('block_coupon_templates', ['id' => $templateid], '*', MUST_EXIST);
        $element = $DB->get_record('block_coupon_elements', ['id' => $elementid], '*', MUST_EXIST);

        // Set the template.
        $template = new \block_coupon\template($template);

        // Perform checks.
        self::validate_context(\context_system::instance());
        // Make sure the user has the required capabilities.
        $template->require_manage();

        // Set the values we are going to save.
        $data = new stdClass();
        $data->id = $element->id;
        $data->name = $element->name;
        foreach ($values as $value) {
            $field = $value['name'];
            $data->$field = $value['value'];
        }

        // Get an instance of the element class.
        if ($e = \block_coupon\template\element_factory::get_element_instance($element)) {
            return $e->save_form_elements($data);
        }

        return false;
    }

    /**
     * Returns the save_element result value.
     *
     * @return external_value
     */
    public static function save_element_returns() {
        return new external_value(PARAM_BOOL, 'True if successful, false otherwise');
    }

    /**
     * Returns get_element() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_element_html_parameters() {
        return new external_function_parameters([
            'templateid' => new external_value(PARAM_INT, 'The template id'),
            'elementid' => new external_value(PARAM_INT, 'The element id'),
        ]);
    }

    /**
     * Handles return the element's HTML.
     *
     * @param int $templateid The template id
     * @param int $elementid The element id.
     * @return string
     */
    public static function get_element_html($templateid, $elementid) {
        global $DB;

        $params = [
            'templateid' => $templateid,
            'elementid' => $elementid,
        ];
        self::validate_parameters(self::get_element_html_parameters(), $params);

        $template = $DB->get_record('block_coupon_templates', ['id' => $templateid], '*', MUST_EXIST);
        $element = $DB->get_record('block_coupon_elements', ['id' => $elementid], '*', MUST_EXIST);

        // Set the template.
        $template = new \block_coupon\template($template);

        // Perform checks.
        self::validate_context(\context_system::instance());

        // Get an instance of the element class.
        if ($e = \block_coupon\template\element_factory::get_element_instance($element)) {
            return $e->render_html();
        }

        return '';
    }

    /**
     * Returns the get_element result value.
     *
     * @return external_value
     */
    public static function get_element_html_returns() {
        return new external_value(PARAM_RAW, 'The HTML');
    }

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function delete_template_parameters(): external_function_parameters {
        $id = new external_value(PARAM_INT, 'Template ID');
        return new external_function_parameters(['id' => $id]);
    }

    /**
     * Delete a template
     *
     * @param int $id
     * @return stdClass
     */
    public static function delete_template($id) {
        global $DB;

        $params = self::validate_parameters(self::delete_template_parameters(), ['id' => $id]);

        $context = \context_system::instance();
        require_capability('block/coupon:administration', $context);

        $template = $DB->get_record('block_coupon_templates', ['id' => $params['id']], '*', MUST_EXIST);
        $template = new \block_coupon\template($template);

        try {
            $template->delete();

            return (object) [
                        'result' => true,
                        'message' => get_string('success:template:delete', 'block_coupon', (object) ['id' => $id]),
            ];
        } catch (\Exception $e) {
            return (object) [
                        'result' => false,
                        'message' => get_string('err:template:delete', 'block_coupon') . $e->getMessage(),
            ];
        }
    }

    /**
     * Returns description of external function result value.
     *
     * @return external_description
     */
    public static function delete_template_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Result of call'),
            'message' => new external_value(PARAM_RAW, 'Result message'),
        ]);
    }

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function duplicate_template_parameters(): external_function_parameters {
        $id = new external_value(PARAM_INT, 'Template ID');
        return new external_function_parameters(['id' => $id]);
    }

    /**
     * Delete a template
     *
     * @param int $id
     * @return stdClass
     */
    public static function duplicate_template($id) {
        global $DB;

        $params = self::validate_parameters(self::duplicate_template_parameters(), ['id' => $id]);

        $context = \context_system::instance();
        static::validate_context($context);
        require_capability('block/coupon:administration', $context);

        $template = $DB->get_record('block_coupon_templates', ['id' => $params['id']], '*', MUST_EXIST);
        $template = new \block_coupon\template($template);

        try {
            // Create another template to copy the data to.
            $name = $template->get_name() . ' (' . strtolower(get_string('duplicate', 'block_coupon')) . ')';
            $newtemplate = \block_coupon\template::create($name, $template->get_contextid());

            // Copy the data to the new template.
            $template->copy_to_template($newtemplate);

            return (object) [
                        'result' => true,
                        'message' => get_string('success:template:duplicate', 'block_coupon', (object) ['id' => $id]),
            ];
        } catch (\Exception $e) {
            return (object) [
                        'result' => false,
                        'message' => get_string('err:template:duplicate', 'block_coupon') . $e->getMessage(),
            ];
        }
    }

    /**
     * Returns description of external function result value.
     *
     * @return external_description
     */
    public static function duplicate_template_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Result of call'),
            'message' => new external_value(PARAM_RAW, 'Result message'),
        ]);
    }

    /**
     * Parameter definition for get_elements_for_page
     *
     * @return external_function_parameters
     */
    public static function get_elements_for_page_parameters() {
        return new external_function_parameters([
            'templateid' => new external_value(PARAM_INT, 'template ID'),
            'pageid' => new external_value(PARAM_INT, 'Page ID'),
        ]);
    }

    /**
     * Return definition for get_elements_for_page
     *
     * @return external_
     */
    public static function get_elements_for_page_returns() {
        $elementstruct = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'element ID'),
            'pageid' => new external_value(PARAM_INT, 'page ID'),
            'name' => new external_value(PARAM_RAW, 'name'),
            'element' => new external_value(PARAM_RAW, 'element'),
            'data' => new external_value(PARAM_RAW, 'element data'),
            'font' => new external_value(PARAM_RAW, 'element font'),
            'fontsize' => new external_value(PARAM_INT, 'element fontsize'),
            'colour' => new external_value(PARAM_RAW, 'element colour'),
            'posx' => new external_value(PARAM_INT, 'posX'),
            'posy' => new external_value(PARAM_INT, 'posY'),
            'width' => new external_value(PARAM_INT, 'width'),
            'refpoint' => new external_value(PARAM_INT, 'refpoint'),
            'alignment' => new external_value(PARAM_ALPHA, 'alignment'),
            'sequence' => new external_value(PARAM_INT, 'sequence'),
            'draggable' => new external_value(PARAM_INT, 'draggale'),
            'visible' => new external_value(PARAM_INT, 'visible'),
            'class' => new external_value(PARAM_RAW, 'class'),
            'rendered' => new external_value(PARAM_RAW, 'rendered element'),
        ]);
        return new external_multiple_structure($elementstruct);
    }

    /**
     * Fetch elements for page.
     *
     * @param int $templateid
     * @param int $pageid
     * @return array
     */
    public static function get_elements_for_page($templateid, $pageid) {
        global $DB;
        try {
            $params = static::validate_parameters(static::get_elements_for_page_parameters(), [
                'templateid' => $templateid,
                'pageid' => $pageid,
            ]);

            // We'll have to validate context to initialise theme etc.
            // Otherwise we end up with $PAGE->context errors.
            $context = \core\context\system::instance();
            static::validate_context($context);
            require_capability('block/coupon:administration', $context);

            // Implement...
            $pageelements = $DB->get_records('block_coupon_elements', ['pageid' => $params['pageid']], 'sequence');
            $elements = [];
            foreach ($pageelements as $element) {
                // Get an instance of the element class.
                if ($e = \block_coupon\template\element_factory::get_element_instance($element)) {
                    switch ($e->get_refpoint()) {
                        case \block_coupon\template\element_helper::COUPON_REF_POINT_TOPRIGHT:
                            $class = 'element refpoint-right';
                            break;
                        case \block_coupon\template\element_helper::COUPON_REF_POINT_TOPCENTER:
                            $class = 'element refpoint-center';
                            break;
                        case \block_coupon\template\element_helper::COUPON_REF_POINT_TOPLEFT:
                        default:
                            $class = 'element refpoint-left';
                    }
                    switch ($e->get_alignment()) {
                        case \block_coupon\template\element::ALIGN_CENTER:
                            $class .= ' align-center';
                            break;
                        case \block_coupon\template\element::ALIGN_RIGHT:
                            $class .= ' align-right';
                            break;
                        case \block_coupon\template\element::ALIGN_LEFT:
                        default:
                            $class .= ' align-left';
                            break;
                    }

                    if (!$e->is_draggable_in_html_view()) {
                        $class .= ' nodrag';
                    }
                    if (!$e->is_visible_in_html_view()) {
                        $class .= ' invisible';
                    }

                    $el = clone $element;
                    unset($el->timecreated);
                    unset($el->timemodified);
                    $el->draggable = $e->is_draggable_in_html_view();
                    $el->visible = $e->is_visible_in_html_view();
                    $el->class = $class;
                    $el->rendered = $e->render_html();
                    $elements[] = $el;
                }
            }

            return $elements;
        } catch (Exception $ex) {
            return [];
        }
    }
}
