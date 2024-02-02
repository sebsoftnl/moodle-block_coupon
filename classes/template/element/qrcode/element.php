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
 * This file contains the element QR code's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template\element\qrcode;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tcpdf/tcpdf_barcodes_2d.php');

/**
 * The element QR code's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \block_coupon\template\element {

    /**
     * @var string The barcode type.
     */
    const BARCODETYPE = 'QRCODE';

    /**
     * This function renders the form elements when adding a element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        \block_coupon\template\element_helper::render_form_element_width($mform);

        \block_coupon\template\element_helper::render_form_element_height($mform);

        if ($this->showposxy) {
            \block_coupon\template\element_helper::render_form_element_position($mform);
        }
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
        $errors += \block_coupon\template\element_helper::validate_form_element_width($data, false);

        // Validate the height.
        $errors += \block_coupon\template\element_helper::validate_form_element_height($data, false);

        if ($this->showposxy) {
            $errors += \block_coupon\template\element_helper::validate_form_element_position($data);
        }

        return $errors;
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
            'width' => !empty($data->width) ? (int)$data->width : 0,
            'height' => !empty($data->height) ? (int)$data->height : 0
        ];

        return json_encode($arrtostore);
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        parent::definition_after_data($mform);

        // Set the image, width, height and alpha channel for this element.
        if (!empty($this->get_data())) {
            $imageinfo = json_decode($this->get_data());

            if (!empty($imageinfo->height)) {
                $element = $mform->getElement('height');
                $element->setValue($imageinfo->height);
            }
        }
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass $extradata -- expects "code" to be present
     */
    public function render($pdf, $preview, $user, \stdClass $extradata = null) {
        global $DB, $CFG, $USER;

        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $imageinfo = json_decode($this->get_data());

        $code = ($extradata?->code) ?? random_string();
        $sha = '';
        $sha .= ($extradata?->id) ?? 0;
        $sha .= ($extradata?->ownerid) ?? $USER->id;
        $sha .= $code;
        $qrcodeurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/qrin.php', array(
            'c' => $code,
            'h' => sha1($sha),
        ));
        $qrcodeurl = $qrcodeurl->out(false);

        $barcode = new \TCPDF2DBarcode($qrcodeurl, self::BARCODETYPE);
        $image = $barcode->getBarcodePngData($imageinfo->width, $imageinfo->height);

        $location = make_request_directory() . '/target';
        file_put_contents($location, $image);

        $pdf->Image($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
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
            return;
        }

        $imageinfo = json_decode($this->get_data());

        $qrcodeurl = new \moodle_url('/');
        $qrcodeurl = $qrcodeurl->out(false);

        $barcode = new \TCPDF2DBarcode($qrcodeurl, self::BARCODETYPE);

        // Required image dimensions are stored in mm (!!!).
        // The provided code leads to a number of rows and columns.
        // We'll divide the required width/height by the number of cols/rows...
        // ... and convert them to pixels.
        $barcodearray = $barcode->getBarcodeArray();
        $numcols = $barcodearray['num_cols'];
        $numrows = $barcodearray['num_rows'];

        $pxinmm = 3.779527559055;

        $w = $pxinmm * $imageinfo->width / $numcols;
        $h = $pxinmm * $imageinfo->height / $numrows;

        return $barcode->getBarcodeHTML($w, $h);
    }
}
