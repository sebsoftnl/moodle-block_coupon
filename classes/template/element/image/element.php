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
 * This file contains the template element image's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template\element\image;

use block_coupon\template\element_helper;

/**
 * The template element image's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \block_coupon\template\element {

    /**
     * @var array The file manager options.
     */
    protected $filemanageroptions = [];

    /**
     * Constructor.
     *
     * @param \stdClass $element the element data
     */
    public function __construct($element) {
        global $COURSE;

        $this->filemanageroptions = [
            'maxbytes' => $COURSE->maxbytes,
            'subdirs' => 1,
            'accepted_types' => 'image',
        ];

        parent::__construct($element);
    }

    /**
     * This function renders the form elements when adding a template element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        $mform->addElement('select', 'fileid', get_string('image', 'block_coupon'), self::get_images());

        element_helper::render_form_element_width($mform);
        element_helper::render_form_element_height($mform);

        $alphachannelvalues = [
            '0' => 0,
            '0.1' => 0.1,
            '0.2' => 0.2,
            '0.3' => 0.3,
            '0.4' => 0.4,
            '0.5' => 0.5,
            '0.6' => 0.6,
            '0.7' => 0.7,
            '0.8' => 0.8,
            '0.9' => 0.9,
            '1' => 1,
        ];
        $mform->addElement('select', 'alphachannel', get_string('alphachannel', 'block_coupon'), $alphachannelvalues);
        $mform->setType('alphachannel', PARAM_FLOAT);
        $mform->setDefault('alphachannel', 1);
        $mform->addHelpButton('alphachannel', 'alphachannel', 'block_coupon');

        if (get_config('block_coupon', 'showposxy')) {
            element_helper::render_form_element_position($mform);
        }

        $mform->addElement('filemanager', 'templateimage', get_string('uploadimage', 'block_coupon'), '',
            $this->filemanageroptions);
    }

    /**
     * Performs validation on the element values.
     *
     * @param array $data the submitted data
     * @param array $files the submitted files
     * @return array the validation errors
     */
    public function validate_form_elements($data, $files) {
        // Array to return the errors.
        $errors = [];

        // Validate the width.
        $errors += element_helper::validate_form_element_width($data);

        // Validate the height.
        $errors += element_helper::validate_form_element_height($data);

        // Validate the position.
        if (get_config('block_coupon', 'showposxy')) {
            $errors += element_helper::validate_form_element_position($data);
        }

        return $errors;
    }

    /**
     * Handles saving the form elements created by this element.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data
     * @return bool true of success, false otherwise.
     */
    public function save_form_elements($data) {
        global $COURSE, $SITE, $CFG;

        // Set the context.
        if ($COURSE->id == $SITE->id) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($COURSE->id);
        }

        // Handle file uploads.
        \block_coupon\template::upload_files($data->templateimage, $context->id);
        require_once($CFG->dirroot . '/lib/filelib.php');
        file_save_draft_area_files($data->templateimage, $context->id, 'block_coupon', 'image', 0);

        return parent::save_form_elements($data);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * block_coupon_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the json encoded array
     */
    public function save_unique_data($data) {
        $arrtostore = [
            'width' => !empty($data->width) ? (int) $data->width : 0,
            'height' => !empty($data->height) ? (int) $data->height : 0,
        ];

        if (isset($data->alphachannel)) {
            $arrtostore['alphachannel'] = (float) $data->alphachannel;
        }

        if (!empty($data->fileid)) {
            // Array of data we will be storing in the database.
            $fs = get_file_storage();
            if ($file = $fs->get_file_by_id($data->fileid)) {
                $arrtostore += [
                    'contextid' => $file->get_contextid(),
                    'filearea' => $file->get_filearea(),
                    'itemid' => $file->get_itemid(),
                    'filepath' => $file->get_filepath(),
                    'filename' => $file->get_filename(),
                ];
            }
        }

        return json_encode($arrtostore);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param boolean $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass|null $extradata -- expects "code" to be present
     */
    public function render($pdf, $preview, $user, ?\stdClass $extradata = null) {
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $imageinfo = json_decode($this->get_data());

        // If there is no file, we have nothing to display.
        if (empty($imageinfo->filename)) {
            return;
        }

        if ($file = $this->get_file()) {
            $location = make_request_directory() . '/target';
            $file->copy_content_to($location);

            // Check if the alpha channel is set, if it is, use it.
            if (isset($imageinfo->alphachannel)) {
                $pdf->SetAlpha($imageinfo->alphachannel);
            }

            $mimetype = $file->get_mimetype();
            if ($mimetype == 'image/svg+xml') {
                $pdf->ImageSVG($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
            } else {
                $pdf->Image($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
            }

            // Restore to full opacity.
            $pdf->SetAlpha(1);
        }
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
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return '';
        }

        $imageinfo = json_decode($this->get_data());

        // If there is no file, we have nothing to display.
        if (empty($imageinfo->filename)) {
            return '';
        }

        // Get the image.
        $fs = get_file_storage();
        if ($file = $fs->get_file($imageinfo->contextid, 'block_coupon', $imageinfo->filearea, $imageinfo->itemid,
                $imageinfo->filepath, $imageinfo->filename)) {
            $url = \moodle_url::make_pluginfile_url($file->get_contextid(), 'block_coupon', 'image', $file->get_itemid(),
                $file->get_filepath(), $file->get_filename());
            $fileimageinfo = $file->get_imageinfo();
            $whratio = $fileimageinfo['width'] / $fileimageinfo['height'];
            // The size of the images to use in the CSS style.
            $style = '';
            if ($imageinfo->width === 0 && $imageinfo->height === 0) {
                $style .= 'width: ' . $fileimageinfo['width'] . 'px; ';
                $style .= 'height: ' . $fileimageinfo['height'] . 'px';
            } else if ($imageinfo->width === 0) { // Then the height must be set.
                // We must get the width based on the height to keep the ratio.
                $style .= 'width: ' . ($imageinfo->height * $whratio) . 'mm; ';
                $style .= 'height: ' . $imageinfo->height . 'mm';
            } else if ($imageinfo->height === 0) { // Then the width must be set.
                $style .= 'width: ' . $imageinfo->width . 'mm; ';
                // We must get the height based on the width to keep the ratio.
                $style .= 'height: ' . ($imageinfo->width / $whratio) . 'mm';
            } else { // Must both be set.
                $style .= 'width: ' . $imageinfo->width . 'mm; ';
                $style .= 'height: ' . $imageinfo->height . 'mm';
            }

            return \html_writer::tag('img', '', ['src' => $url, 'style' => $style]);
        }
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        global $COURSE, $SITE;

        // Set the image, width, height and alpha channel for this element.
        if (!empty($this->get_data())) {
            $imageinfo = json_decode($this->get_data());
            if (!empty($imageinfo->filename)) {
                if ($file = $this->get_file()) {
                    $element = $mform->getElement('fileid');
                    $element->setValue($file->get_id());
                }
            }

            if (isset($imageinfo->width) && $mform->elementExists('width')) {
                $element = $mform->getElement('width');
                $element->setValue($imageinfo->width);
            }

            if (isset($imageinfo->height) && $mform->elementExists('height')) {
                $element = $mform->getElement('height');
                $element->setValue($imageinfo->height);
            }

            if (isset($imageinfo->alphachannel) && $mform->elementExists('alphachannel')) {
                $element = $mform->getElement('alphachannel');
                $element->setValue($imageinfo->alphachannel);
            }
        }

        // Set the context.
        if ($COURSE->id == $SITE->id) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($COURSE->id);
        }

        // Editing existing instance - copy existing files into draft area.
        $draftitemid = file_get_submitted_draft_itemid('templateimage');
        file_prepare_draft_area($draftitemid, $context->id, 'block_coupon', 'image', 0, $this->filemanageroptions);
        $element = $mform->getElement('templateimage');
        $element->setValue($draftitemid);

        parent::definition_after_data($mform);
    }

    /**
     * This function is responsible for handling the restoration process of the element.
     *
     * We will want to update the file's pathname hash.
     *
     * @param \restore_block_coupon_activity_task $restore
     */
    public function after_restore($restore) {
    }

    /**
     * Fetch stored file.
     *
     * @return \stored_file|bool stored_file instance if exists, false if not
     */
    public function get_file() {
        $imageinfo = json_decode($this->get_data());

        $fs = get_file_storage();

        return $fs->get_file($imageinfo->contextid, 'block_coupon', $imageinfo->filearea, $imageinfo->itemid,
            $imageinfo->filepath, $imageinfo->filename);
    }

    /**
     * Return the list of possible images to use.
     *
     * @return array the list of images that can be used
     */
    public static function get_images() {
        global $COURSE;

        // Create file storage object.
        $fs = get_file_storage();

        // The array used to store the images.
        $arrfiles = [];
        // Loop through the files uploaded in the system context.
        if ($files = $fs->get_area_files(\context_system::instance()->id, 'block_coupon', 'image', false, 'filename', false)) {
            foreach ($files as $hash => $file) {
                $arrfiles[$file->get_id()] = get_string('systemimage', 'block_coupon', $file->get_filename());
            }
        }
        // Loop through the files uploaded in the course context.
        if ($files = $fs->get_area_files(\context_course::instance($COURSE->id)->id, 'block_coupon', 'image', false,
            'filename', false)) {
            foreach ($files as $hash => $file) {
                $arrfiles[$file->get_id()] = get_string('courseimage', 'block_coupon', $file->get_filename());
            }
        }

        \core_collator::asort($arrfiles);
        $arrfiles = ['0' => get_string('noimage', 'block_coupon')] + $arrfiles;

        return $arrfiles;
    }

    /**
     * This handles copying data from another element of the same type.
     *
     * @param \stdClass $data the form data
     * @return bool returns true if the data was copied successfully, false otherwise
     */
    public function copy_element($data) {
        global $COURSE, $DB, $SITE;

        $imagedata = json_decode($data->data);

        // If we are in the site context we don't have to do anything, the image is already there.
        if ($COURSE->id == $SITE->id) {
            return true;
        }

        $coursecontext = \context_course::instance($COURSE->id);
        $systemcontext = \context_system::instance();

        $fs = get_file_storage();

        // Check that a file has been selected.
        if (isset($imagedata->filearea)) {
            // If the course file doesn't exist, copy the system file to the course context.
            if (!$coursefile = $fs->get_file(
                $coursecontext->id,
                'block_coupon',
                $imagedata->filearea,
                $imagedata->itemid,
                $imagedata->filepath,
                $imagedata->filename
            )) {
                $systemfile = $fs->get_file(
                    $systemcontext->id,
                    'block_coupon',
                    $imagedata->filearea,
                    $imagedata->itemid,
                    $imagedata->filepath,
                    $imagedata->filename
                );

                // We want to update the context of the file if it doesn't exist in the course context.
                $fieldupdates = ['contextid' => $coursecontext->id];
                $coursefile = $fs->create_file_from_storedfile($fieldupdates, $systemfile);
            }

            // Set the image to the copied file in the course.
            $imagedata->fileid = $coursefile->get_id();
            $DB->set_field('block_coupon_elements', 'data', $this->save_unique_data($imagedata), ['id' => $this->get_id()]);
        }

        return true;
    }
}
