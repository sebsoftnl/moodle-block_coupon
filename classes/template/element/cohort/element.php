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
 * This file contains the template element cohort's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template\element\cohort;

/**
 * The template element cohort's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \block_coupon\template\element {
    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param boolean $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass|null $extradata -- expects "cohort" to be present
     */
    public function render($pdf, $preview, $user, ?\stdClass $extradata = null) {
        $cohorts = ($extradata?->cohorts) ?? [];
        $cohortnames = [];
        foreach ($cohorts as $cohort) {
            $cohortnames[] = format_string($cohort->name, true);
        }
        $cohortstr = implode(', ', $cohortnames);

        \block_coupon\template\element_helper::render_content($pdf, $this, $cohortstr);
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        $cohort = 'cohort' . random_string();

        return \block_coupon\template\element_helper::render_html_content($this, $cohort);
    }

    /**
     * Get/load extra data that's needed for this element.
     *
     * @param stdClass $coupon
     * @param bool $preview -- is this a preview mode?
     * @return mixed
     */
    public function get_extra_data($coupon, bool $preview) {
        // Get coupon courses.
        if ($preview) {
            $cohorts = [
                (object)['id' => 0, 'name' => 'COHORTNAME'],
            ];
        } else {
            $cohorts = \block_coupon\helper::get_coupon_cohorts($coupon);
        }
        return (object)['cohorts' => $cohorts];
    }
}
