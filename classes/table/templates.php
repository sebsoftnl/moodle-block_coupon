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
 * this file contains the table to display templates
 *
 * File         templates.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use moodle_url;

/**
 * block_coupon\tables\templates
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class templates extends \table_sql implements \core_table\dynamic {

    /**
     * @var \context $context
     */
    protected $context;

    /**
     * Sets up the table.
     */
    public function __construct() {
        global $USER;
        parent::__construct(str_replace('\\', '_', __CLASS__) . '-' . $USER->id);

        $columns = [
            'name',
            'actions'
        ];

        $headers = [
            get_string('name'),
            ''
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->collapsible(false);
        $this->sortable(true);
        $this->no_sorting('actions');
    }

    /**
     * Set the filterseu.
     *
     * @param \core_table\local\filter\filterset $filterset
     * @return void
     */
    public function set_filterset(\core_table\local\filter\filterset $filterset) :void {
        $this->context = \context_system::instance();
        parent::set_filterset($filterset);
    }

    /**
     * Guess the base url for the templates table.
     */
    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/blocks/coupon/view/templates/index.php');
    }

    /**
     * Get the context of the current table.
     *
     * Note: This function should not be called until after the filterset has been provided.
     *
     * @return context
     */
    public function get_context(): \context {
        return $this->context;
    }

    /**
     * Convenience method to call a number of methods for you to display the table.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @param string $downloadhelpbutton
     * @return string
     */
    public function render($pagesize, $useinitialsbar, $downloadhelpbutton='') {
        ob_start();
        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);
        $table = ob_get_clean();
        return $table;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $template
     * @return string
     */
    public function col_name($template) {
        return format_string($template->name, true, ['context' => $this->context]);
    }

    /**
     * Generate the actions column.
     *
     * @param \stdClass $template
     * @return string
     */
    public function col_actions($template) {
        global $OUTPUT;

        // Link to edit the template.
        $editlink = new \moodle_url('/blocks/coupon/view/templates/edit.php', array('tid' => $template->id));
        $editicon = $OUTPUT->action_icon($editlink, new \pix_icon('t/edit', get_string('edit')));

        // Link to duplicate the template.
        $duplicatelink = new \moodle_url('#');
        $duplicateicon = $OUTPUT->action_icon($duplicatelink, new \pix_icon('t/copy', get_string('duplicate')), null,
            array('class' => 'action-icon duplicate-icon', 'data-action' => 'duplicate', 'data-id' => $template->id));

        // Link to delete the template.
        $deletelink = new \moodle_url('#');
        $deleteicon = $OUTPUT->action_icon($deletelink, new \pix_icon('t/delete', get_string('delete')), null,
            array('class' => 'action-icon delete-icon', 'data-action' => 'delete', 'data-id' => $template->id));

        return $editicon . $duplicateicon . $deleteicon;
    }

    /**
     * Query the reader.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        $total = $DB->count_records('block_coupon_templates', array('contextid' => $this->context->id));

        $this->pagesize($pagesize, $total);

        $this->rawdata = $DB->get_records('block_coupon_templates', array('contextid' => $this->context->id),
            $this->get_sql_sort(), '*', $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }
}
