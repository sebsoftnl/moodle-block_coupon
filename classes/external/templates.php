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
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\external;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use stdClass;
use external_api;
use external_value;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;

/**
 * Webservices implementation for block_coupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
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

        $params = self::validate_parameters(self::update_element_positions_parameters(), array(
            'tid' => $tid,
            'values' => $values,
        ));

        $template = $DB->get_record('block_coupon_templates', ['id' => $params['tid']]);
        $template = new \block_coupon\template($template);
        $context = $template->get_context();

        self::validate_context($context);
        require_capability('block/coupon:administration', $context);
        $template->require_manage();

        foreach ($params['values'] as $value) {
            $value = (object)$value;
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
            'values' => new external_multiple_structure($struct)
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
        return new external_function_parameters(
            array(
                'templateid' => new external_value(PARAM_INT, 'The template id'),
                'elementid' => new external_value(PARAM_INT, 'The element id'),
                'values' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'The field to update'),
                            'value' => new external_value(PARAM_RAW, 'The value of the field'),
                        )
                    )
                )
            )
        );
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

        $params = array(
            'templateid' => $templateid,
            'elementid' => $elementid,
            'values' => $values
        );
        self::validate_parameters(self::save_element_parameters(), $params);

        $template = $DB->get_record('block_coupon_templates', array('id' => $templateid), '*', MUST_EXIST);
        $element = $DB->get_record('block_coupon_elements', array('id' => $elementid), '*', MUST_EXIST);

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
        return new external_function_parameters(
            array(
                'templateid' => new external_value(PARAM_INT, 'The template id'),
                'elementid' => new external_value(PARAM_INT, 'The element id'),
            )
        );
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

        $params = array(
            'templateid' => $templateid,
            'elementid' => $elementid
        );
        self::validate_parameters(self::get_element_html_parameters(), $params);

        $template = $DB->get_record('block_coupon_templates', array('id' => $templateid), '*', MUST_EXIST);
        $element = $DB->get_record('block_coupon_elements', array('id' => $elementid), '*', MUST_EXIST);

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
        return new external_function_parameters(array(
            'id' => $id
        ));
    }

    /**
     * Delete a template
     *
     * @param int $id
     * @return stdClass
     */
    public static function delete_template($id) {
        global $DB;

        $params = self::validate_parameters(self::delete_template_parameters(), array(
            'id' => $id
        ));

        $context = \context_system::instance();
        require_capability('block/coupon:administration', $context);

        $template = $DB->get_record('block_coupon_templates', array('id' => $params['id']), '*', MUST_EXIST);
        $template = new \block_coupon\template($template);

        try {
            $template->delete();

            return (object)[
                'result' => true,
                'message' => get_string('success:template:delete', 'block_coupon', (object)['id' => $id])
            ];
        } catch (\Exception $e) {
            return (object)[
                'result' => false,
                'message' => get_string('err:template:delete', 'block_coupon') . $e->getMessage()
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
            'message' => new external_value(PARAM_RAW, 'Result message')
        ]);
    }

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function duplicate_template_parameters(): external_function_parameters {
        $id = new external_value(PARAM_INT, 'Template ID');
        return new external_function_parameters(array(
            'id' => $id
        ));
    }

    /**
     * Delete a template
     *
     * @param int $id
     * @return stdClass
     */
    public static function duplicate_template($id) {
        global $DB;

        $params = self::validate_parameters(self::duplicate_template_parameters(), array(
            'id' => $id
        ));

        $context = \context_system::instance();
        require_capability('block/coupon:administration', $context);

        $template = $DB->get_record('block_coupon_templates', array('id' => $params['id']), '*', MUST_EXIST);
        $template = new \block_coupon\template($template);

        try {
            // Create another template to copy the data to.
            $name = $template->get_name() . ' (' . strtolower(get_string('duplicate', 'block_coupon')) . ')';
            $newtemplate = \block_coupon\template::create($name, $template->get_contextid());

            // Copy the data to the new template.
            $template->copy_to_template($newtemplate);

            return (object)[
                'result' => true,
                'message' => get_string('success:template:duplicate', 'block_coupon', (object)['id' => $id])
            ];
        } catch (\Exception $e) {
            return (object)[
                'result' => false,
                'message' => get_string('err:template:duplicate', 'block_coupon') . $e->getMessage()
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
            'message' => new external_value(PARAM_RAW, 'Result message')
        ]);
    }

}
