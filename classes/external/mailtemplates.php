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
class mailtemplates extends external_api {

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

        try {
            $DB->delete_records('block_coupon_mailtemplates', ['id' => $params['id']]);

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

        try {
            $template = $DB->get_record('block_coupon_mailtemplates', array('id' => $params['id']), '*', MUST_EXIST);
            unset($template->id);

            $n = 1;
            while (true) {
                if ($DB->record_exists('block_coupon_mailtemplates', ['name' => "{$template->name} (copy-{$n})"])) {
                    $n++;
                } else {
                    break;
                }
            }
            $template->name .= " (copy-{$n})";
            $template->id = $DB->insert_record('block_coupon_mailtemplates', $template);

            return (object)[
                'result' => true,
                'message' => get_string('success:template:duplicate', 'block_coupon', (object)['id' => $template->id])
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
