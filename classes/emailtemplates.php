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
 * Email template helper class
 *
 * File         emailtemplates.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_coupon;

/**
 * Email template helper class
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emailtemplates {

    /**
     * Get template variable definitions
     *
     * @return array
     */
    public static function get_email_template_variables(): array {
        return [
            '###to_name###' => get_string('recipient:name', 'block_coupon'),
            '###site_name###' => get_string('fullsitename'),
            '###siteurl###' => get_string('siteurl', 'hub'),
            '###to_gender###' => get_string('recipient:gender', 'block_coupon'),
            '###extensionperiod###' => get_string('label:extendperiod', 'block_coupon'),
            '###submission_code###' => get_string('label:coupon_code', 'block_coupon'),
        ];
    }

    /**
     * Add template form elements (injects static field with the templatable variables).
     *
     * @param \MoodleQuickForm $mform
     * @param string $elname
     */
    public static function add_form_element(\MoodleQuickForm $mform, $elname = '_email_template_vars') {
        global $DB, $PAGE;
        // Inject switcher.
        if ($DB->record_exists('block_coupon_mailtemplates', [])) {
            $mform->registerNoSubmitButton('tplselectbutton');
            $mform->addElement('submit', 'tplselectbutton', 'loadtemplate', ['style' => 'display: none;']);

            $menu = ['' => get_string('choose')] +
                    $DB->get_records_sql_menu('SELECT id,name FROM {block_coupon_mailtemplates} ORDER BY name ASC');
            $mform->addElement('select', 'tplload', get_string('load_mailtemplate', 'block_coupon'), $menu);
            $mform->addHelpButton('tplload', 'load_mailtemplate', 'block_coupon');
            $PAGE->requires->js_call_amd('block_coupon/templateloader', 'init', []);
        }

        // Inject static descriptor.
        $a = '<table>';
        $vars = static::get_email_template_variables();
        foreach ($vars as $k => $v) {
            $a .= "<tr><td>{$k}</td><td>{$v}</td></tr>";
        }
        $a .= '</table>';
        $desc = get_string('email:templatevars', 'block_coupon', $a);
        $mform->addElement('static', $elname, '', $desc);
    }

    /**
     * Helper function that returns the template data.
     *
     * @param \stdClass $a variables
     * @return array
     */
    protected function get_template_data(\stdClass $a): \stdClass {
        global $CFG, $SITE;
        if (!isset($a->submission_code)) {
            $a->submission_code = random_string();
        }
        if (!isset($a->site_name)) {
            $a->site_name = $SITE->fullname;
        }
        if (!isset($a->siteurl)) {
            $a->siteurl = $CFG->wwwroot;
        }
        return $a;
    }

    /**
     * Modify templateable variables.
     *
     * @param array|\stdClass $a variables
     * @return array array of key/values for use in strtr
     * @throws \moodle_exception
     */
    protected function mod_vars($a): array {
        if (is_object($a)) {
            $a = (array)$a;
        }
        $tr = array();
        foreach ($a as $k => $v) {
            $tr['###'.$k.'###'] = $v;
            $tr['##'.$k.'##'] = $v;
        }
        return $tr;
    }

}
