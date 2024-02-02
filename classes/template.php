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
 * Class represents a coupon template.
 *
 * @package    block_coupon
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon;

use block_coupon\template\element_factory;

/**
 * Class represents a coupon template.
 *
 * @package    block_coupon
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template {

    /**
     * @var int $id The id of the template.
     */
    protected $id;

    /**
     * @var string $name The name of this template
     */
    protected $name;

    /**
     * @var int $contextid The context id of this template
     */
    protected $contextid;

    /**
     * The constructor.
     *
     * @param \stdClass $template
     */
    public function __construct($template) {
        $this->id = $template->id;
        $this->name = $template->name;
        $this->contextid = $template->contextid;
    }

    /**
     * Handles saving data.
     *
     * @param \stdClass $data the template data
     */
    public function save($data) {
        global $DB;

        $savedata = new \stdClass();
        $savedata->id = $this->id;
        $savedata->name = $data->name;
        $savedata->timemodified = time();

        $DB->update_record('block_coupon_templates', $savedata);

        \block_coupon\event\template_updated::create_from_template($this)->trigger();
    }

    /**
     * Handles adding another page to the template.
     *
     * @return int the id of the page
     */
    public function add_page() {
        global $DB;

        // Set the page number to 1 to begin with.
        $sequence = 1;
        // Get the max page number.
        $sql = "SELECT MAX(sequence) as maxpage
                  FROM {block_coupon_pages} cp
                 WHERE cp.templateid = :templateid";
        if ($maxpage = $DB->get_record_sql($sql, array('templateid' => $this->id))) {
            $sequence = $maxpage->maxpage + 1;
        }

        // New page creation.
        $page = new \stdClass();
        $page->templateid = $this->id;
        $page->width = '210';
        $page->height = '297';
        $page->sequence = $sequence;
        $page->timecreated = time();
        $page->timemodified = $page->timecreated;

        // Insert the page.
        $pageid = $DB->insert_record('block_coupon_pages', $page);

        $page->id = $pageid;

        \block_coupon\event\page_created::create_from_page($page, $this)->trigger();
        \block_coupon\event\template_updated::create_from_template($this)->trigger();

        return $page->id;
    }

    /**
     * Handles saving page data.
     *
     * @param \stdClass $data the template data
     */
    public function save_page($data) {
        global $DB;

        // Set the time to a variable.
        $time = time();

        // Get the existing pages and save the page data.
        if ($pages = $DB->get_records('block_coupon_pages', array('templateid' => $data->tid))) {
            // Loop through existing pages.
            foreach ($pages as $page) {
                // Get the name of the fields we want from the form.
                $width = 'pagewidth_' . $page->id;
                $height = 'pageheight_' . $page->id;
                $leftmargin = 'pageleftmargin_' . $page->id;
                $rightmargin = 'pagerightmargin_' . $page->id;
                // Create the page data to update the DB with.
                $p = new \stdClass();
                $p->id = $page->id;
                $p->width = $data->$width;
                $p->height = $data->$height;
                $p->leftmargin = $data->$leftmargin;
                $p->rightmargin = $data->$rightmargin;
                $p->timemodified = $time;
                // Update the page.
                $DB->update_record('block_coupon_pages', $p);

                \block_coupon\event\page_updated::create_from_page($p, $this)->trigger();
            }

            \block_coupon\event\template_updated::create_from_template($this)->trigger();
        }
    }

    /**
     * Handles deleting the template.
     *
     * @return bool return true if the deletion was successful, false otherwise
     */
    public function delete() {
        global $DB;

        // Delete the elements.
        $sql = "SELECT e.*
                  FROM {block_coupon_elements} e
            INNER JOIN {block_coupon_pages} p
                    ON e.pageid = p.id
                 WHERE p.templateid = :templateid";
        if ($elements = $DB->get_records_sql($sql, array('templateid' => $this->id))) {
            foreach ($elements as $element) {
                // Get an instance of the element class.
                if ($e = element_factory::get_element_instance($element)) {
                    $e->delete();
                } else {
                    // The plugin files are missing, so just remove the entry from the DB.
                    $DB->delete_records('block_coupon_elements', array('id' => $element->id));
                }
            }
        }

        // Delete the pages.
        if (!$DB->delete_records('block_coupon_pages', array('templateid' => $this->id))) {
            return false;
        }

        // Now, finally delete the actual template.
        if (!$DB->delete_records('block_coupon_templates', array('id' => $this->id))) {
            return false;
        }

        \block_coupon\event\template_deleted::create_from_template($this)->trigger();

        return true;
    }

    /**
     * Handles deleting a page from the template.
     *
     * @param int $pageid the template page
     */
    public function delete_page($pageid) {
        global $DB;

        // Get the page.
        $page = $DB->get_record('block_coupon_pages', array('id' => $pageid), '*', MUST_EXIST);

        // Delete this page.
        $DB->delete_records('block_coupon_pages', array('id' => $page->id));

        \block_coupon\event\page_deleted::create_from_page($page, $this)->trigger();

        // The element may have some extra tasks it needs to complete to completely delete itself.
        if ($elements = $DB->get_records('block_coupon_elements', array('pageid' => $page->id))) {
            foreach ($elements as $element) {
                // Get an instance of the element class.
                if ($e = element_factory::get_element_instance($element)) {
                    $e->delete();
                } else {
                    // The plugin files are missing, so just remove the entry from the DB.
                    $DB->delete_records('block_coupon_elements', array('id' => $element->id));
                }
            }
        }

        // Now we want to decrease the page number values of
        // the pages that are greater than the page we deleted.
        $sql = "UPDATE {block_coupon_pages}
                   SET sequence = sequence - 1
                 WHERE templateid = :templateid
                   AND sequence > :sequence";
        $DB->execute($sql, array('templateid' => $this->id, 'sequence' => $page->sequence));

        \block_coupon\event\template_updated::create_from_template($this)->trigger();
    }

    /**
     * Handles deleting an element from the template.
     *
     * @param int $elementid the template page
     */
    public function delete_element($elementid) {
        global $DB;

        // Ensure element exists and delete it.
        $element = $DB->get_record('block_coupon_elements', array('id' => $elementid), '*', MUST_EXIST);

        // Get an instance of the element class.
        if ($e = element_factory::get_element_instance($element)) {
            $e->delete();
        } else {
            // The plugin files are missing, so just remove the entry from the DB.
            $DB->delete_records('block_coupon_elements', array('id' => $elementid));
        }

        // Now we want to decrease the sequence numbers of the elements
        // that are greater than the element we deleted.
        $sql = "UPDATE {block_coupon_elements}
                   SET sequence = sequence - 1
                 WHERE pageid = :pageid
                   AND sequence > :sequence";
        $DB->execute($sql, array('pageid' => $element->pageid, 'sequence' => $element->sequence));

        \block_coupon\event\template_updated::create_from_template($this)->trigger();
    }

    /**
     * Generate the PDF for the template.
     *
     * @param array $coupons generated (or fake) array of coupons.
     * @param bool $preview true if it is a preview, false otherwise
     * @param int $userid the id of the user whose certificate we want to view
     * @param bool $return Do we want to return the contents of the PDF?
     * @param string $relativefilename relative filename (relative to $CFG->dataroot)
     * @return string|void Can return the PDF in string format if specified.
     */
    public function generate_pdf(array $coupons = [], bool $preview = false,
            int $userid = null, bool $return = false, $relativefilename = null) {
        global $CFG, $DB, $USER;

        if (empty($userid)) {
            $user = $USER;
        } else {
            $user = \core_user::get_user($userid);
        }

        require_once($CFG->libdir . '/pdflib.php');
        require_once($CFG->dirroot . '/blocks/coupon/lib.php');

        // Get the pages for the template, there should always be at least one page for each template.
        if ($pages = $DB->get_records('block_coupon_pages', array('templateid' => $this->id), 'sequence ASC')) {
            // Create the pdf object.
            $pdf = new \pdf('P', 'mm', 'A4', true, 'UTF-8');

            // Remove full-stop at the end, if it exists, to avoid "..pdf" being created and being filtered by clean_filename.
            if (empty($relativefilename)) {
                $relativefilename = rtrim(format_string($this->name, true, ['context' => $this->get_context()]), '.');
                $filename = $CFG->dataroot . '/' . $relativefilename;
            }

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetTitle(get_string('pdf:titlename', 'block_coupon'));
            $pdf->SetAutoPageBreak(true, 0);

            $pdf->SetHeaderMargin(0);
            $pdf->SetFooterMargin(0);
            $pdf->SetMargins(0, 0, 0, true); // L-T-R.

            $pdf->SetFont('helvetica', '', 12);
            $pdf->SetCreator('PDF Generator build 1.0');
            $pdf->SetAuthor('Sebsoft PDF Generator build 1.0');

            $pdf->SetTitle(get_string('pdf-meta:title', 'block_coupon'));
            $pdf->SetSubject(get_string('pdf-meta:subject', 'block_coupon'));
            $pdf->SetKeywords(get_string('pdf-meta:keywords', 'block_coupon'));

            $fixname = false; // Might remove in future, for now this remains unused.
            if ($fixname) {
                // This is the logic the TCPDF library uses when processing the name. This makes names
                // such as 'الشهادة' become empty, so set a default name in these cases.
                $relativefilename = preg_replace('/[\s]+/', '_', $relativefilename);
                $relativefilename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $relativefilename);
                $relativefilename = clean_filename($relativefilename . '.pdf');
            }

            // Loop through the pages and display their content.
            $this->render_pages($pdf, $pages, $coupons, $preview, $userid);

            if ($return) {
                return $pdf->Output('', 'S');
            }

            if ($preview) {
                $pdf->Output('', 'I');
            }

            $filename = "{$CFG->dataroot}/{$relativefilename}";
            $pdf->Output($filename, 'F');

            return [$relativefilename, $filename];
        }
    }

    /**
     * Render pages
     *
     * @param \pdf $pdf
     * @param array $pages
     * @param array $coupons
     * @param bool $preview
     * @param int $userid
     */
    protected function render_pages($pdf, $pages, array $coupons = [], bool $preview = false, int $userid = null) {
        global $DB;
        foreach ($coupons as $coupon) {
            foreach ($pages as $page) {
                // Add the page to the PDF.
                if ($page->width > $page->height) {
                    $orientation = 'L';
                } else {
                    $orientation = 'P';
                }
                $pdf->AddPage($orientation, array($page->width, $page->height));
                $pdf->SetMargins($page->leftmargin, 0, $page->rightmargin);
                // Get the elements for the page.
                if ($elements = $DB->get_records('block_coupon_elements', array('pageid' => $page->id), 'sequence ASC')) {
                    // Loop through and display.
                    foreach ($elements as $element) {
                        // Get an instance of the element class.
                        if ($e = element_factory::get_element_instance($element)) {
                            $extradata = null;
                            switch ($element->element) {
                                case 'qrcode':
                                    $extradata = (object)[
                                        'code' => $coupon->submission_code
                                    ];
                                    break;
                                case 'code':
                                    $extradata = (object)[
                                        'code' => $coupon->submission_code
                                    ];
                                    break;
                            }
                            $e->render($pdf, $preview, $userid, $extradata);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handles copying this template into another.
     *
     * @param object $copytotemplate The template instance to copy to
     */
    public function copy_to_template($copytotemplate) {
        global $DB;

        $copytotemplateid = $copytotemplate->get_id();

        // Get the pages for the template, there should always be at least one page for each template.
        if ($templatepages = $DB->get_records('block_coupon_pages', array('templateid' => $this->id))) {
            // Loop through the pages.
            foreach ($templatepages as $templatepage) {
                $page = clone($templatepage);
                $page->templateid = $copytotemplateid;
                $page->timecreated = time();
                $page->timemodified = $page->timecreated;
                // Insert into the database.
                $page->id = $DB->insert_record('block_coupon_pages', $page);
                \block_coupon\event\page_created::create_from_page($page, $this)->trigger();
                // Now go through the elements we want to load.
                if ($templateelements = $DB->get_records('block_coupon_elements', array('pageid' => $templatepage->id))) {
                    foreach ($templateelements as $templateelement) {
                        $element = clone($templateelement);
                        $element->pageid = $page->id;
                        $element->timecreated = time();
                        $element->timemodified = $element->timecreated;
                        // Ok, now we want to insert this into the database.
                        $element->id = $DB->insert_record('block_coupon_elements', $element);
                        // Load any other information the element may need to for the template.
                        if ($e = element_factory::get_element_instance($element)) {
                            if (!$e->copy_element($templateelement)) {
                                // Failed to copy - delete the element.
                                $e->delete();
                            } else {
                                \block_coupon\event\element_created::create_from_element($e)->trigger();
                            }
                        }
                    }
                }
            }

            // Trigger event for template instance being copied to.
            if ($copytotemplate->get_context() == \context_system::instance()) {
                // If CONTEXT_SYSTEM we're creating a new template.
                \block_coupon\event\template_created::create_from_template($copytotemplate)->trigger();
            } else {
                // Otherwise we're loading template in a course module instance.
                \block_coupon\event\template_updated::create_from_template($copytotemplate)->trigger();
            }
        }
    }

    /**
     * Handles moving an item on a template.
     *
     * @param string $itemname the item we are moving
     * @param int $itemid the id of the item
     * @param string $direction the direction
     */
    public function move_item($itemname, $itemid, $direction) {
        global $DB;

        $table = 'block_coupon_';
        if ($itemname == 'page') {
            $table .= 'pages';
        } else { // Must be an element.
            $table .= 'elements';
        }

        if ($moveitem = $DB->get_record($table, array('id' => $itemid))) {
            // Check which direction we are going.
            if ($direction == 'up') {
                $sequence = $moveitem->sequence - 1;
            } else { // Must be down.
                $sequence = $moveitem->sequence + 1;
            }

            // Get the item we will be swapping with. Make sure it is related to the same template (if it's
            // a page) or the same page (if it's an element).
            if ($itemname == 'page') {
                $params = array('templateid' => $moveitem->templateid);
            } else { // Must be an element.
                $params = array('pageid' => $moveitem->pageid);
            }
            $swapitem = $DB->get_record($table, $params + array('sequence' => $sequence));
        }

        // Check that there is an item to move, and an item to swap it with.
        if ($moveitem && !empty($swapitem)) {
            $DB->set_field($table, 'sequence', $swapitem->sequence, array('id' => $moveitem->id));
            $DB->set_field($table, 'sequence', $moveitem->sequence, array('id' => $swapitem->id));

            \block_coupon\event\template_updated::create_from_template($this)->trigger();
        }
    }

    /**
     * Returns the id of the template.
     *
     * @return int the id of the template
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Returns the name of the template.
     *
     * @return string the name of the template
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Returns the context id.
     *
     * @return int the context id
     */
    public function get_contextid() {
        return $this->contextid;
    }

    /**
     * Returns the context id.
     *
     * @return \context the context
     */
    public function get_context() {
        return \context::instance_by_id($this->contextid);
    }

    /**
     * Ensures the user has the proper capabilities to manage this template.
     *
     * @throws \required_capability_exception if the user does not have the necessary capabilities (ie. Fred)
     */
    public function require_manage() {
        require_capability('block/coupon:administration', $this->get_context());
    }

    /**
     * Creates a template.
     *
     * @param string $templatename the name of the template
     * @param int $contextid the context id
     * @return \block_coupon\template the template object
     */
    public static function create($templatename, $contextid) {
        global $DB;

        $template = new \stdClass();
        $template->name = $templatename;
        $template->contextid = $contextid;
        $template->timecreated = time();
        $template->timemodified = $template->timecreated;
        $template->id = $DB->insert_record('block_coupon_templates', $template);

        $template = new \block_coupon\template($template);

        \block_coupon\event\template_created::create_from_template($template)->trigger();

        return $template;
    }

    /**
     * Return the list of possible fonts to use.
     */
    public static function get_fonts() {
        global $CFG;

        require_once($CFG->libdir . '/pdflib.php');

        $arrfonts = [];
        $pdf = new \pdf();
        $fontfamilies = $pdf->get_font_families();
        foreach ($fontfamilies as $fontfamily => $fontstyles) {
            foreach ($fontstyles as $fontstyle) {
                $fontstyle = strtolower($fontstyle);
                if ($fontstyle == 'r') {
                    $filenamewoextension = $fontfamily;
                } else {
                    $filenamewoextension = $fontfamily . $fontstyle;
                }
                $fullpath = \TCPDF_FONTS::_getfontpath() . $filenamewoextension;
                // Set the name of the font to null, the include next should then set this
                // value, if it is not set then the file does not include the necessary data.
                $name = null;
                // Some files include a display name, the include next should then set this
                // value if it is present, if not then $name is used to create the display name.
                $displayname = null;
                // Some of the TCPDF files include files that are not present, so we have to
                // suppress warnings, this is the TCPDF libraries fault, grrr.
                @include($fullpath . '.php');
                // If no $name variable in file, skip it.
                if (is_null($name)) {
                    continue;
                }
                // Check if there is no display name to use.
                if (is_null($displayname)) {
                    // Format the font name, so "FontName-Style" becomes "Font Name - Style".
                    $displayname = preg_replace("/([a-z])([A-Z])/", "$1 $2", $name);
                    $displayname = preg_replace("/([a-zA-Z])-([a-zA-Z])/", "$1 - $2", $displayname);
                }

                $arrfonts[$filenamewoextension] = $displayname;
            }
        }
        ksort($arrfonts);

        return $arrfonts;
    }

    /**
     * Handles uploading an image.
     *
     * @param int $draftitemid the draft area containing the files
     * @param int $contextid the context we are storing this image in
     * @param string $filearea indentifies the file area.
     */
    public static function upload_files($draftitemid, $contextid, $filearea = 'image') {
        global $CFG;
        // Save the file if it exists that is currently in the draft area.
        require_once($CFG->dirroot . '/lib/filelib.php');
        file_save_draft_area_files($draftitemid, $contextid, 'block_coupon', $filearea, 0);
    }

}
