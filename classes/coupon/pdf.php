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
 * Coupon code generator PDF
 *
 * File         pdf.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon;

defined('MOODLE_INTERNAL') || die();

use block_coupon\helper;

require_once($CFG->dirroot . '/lib/pdflib.php');

/**
 * block_coupon\coupon\pdf
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdf extends \pdf {

    /**
     * full path to font directory
     * @var string
     */
    protected $fontpath = '';
    /**
     * Title for document
     * @var string
     */
    private $namestring;
    /**
     * PDF generator date
     * @var string
     */
    private $generatordate;
    /**
     * coupons to render
     * @var array
     */
    protected $coupons;
    /**
     * full path to logo file
     * @var string
     */
    protected $logo;

    /**
     * main text template
     *
     * @var string
     */
    protected $templatemain = '';
    /**
     * botleft text template
     *
     * @var string
     */
    protected $templatebotleft = '';
    /**
     * botright text template
     *
     * @var string
     */
    protected $templatebotright = '';
    /**
     * include QR code?
     *
     * @var bool
     */
    protected $includeqr = true;

    /**
     * Get main text template
     *
     * @return string main template
     */
    public function get_templatemain() {
        return $this->templatemain;
    }

    /**
     * Get bot right text template
     *
     * @return string bot right template
     */
    public function get_templatebotright() {
        return $this->templatebotright;
    }

    /**
     * Get bot left text template
     *
     * @return string bot left template
     */
    public function get_templatebotleft() {
        return $this->templatebotleft;
    }

    /**
     * Set main text template
     *
     * @param string $templatemain text template
     * @return \self
     */
    public function set_templatemain($templatemain) {
        $this->templatemain = $templatemain;
        return $this;
    }

    /**
     * Set botright text template
     *
     * @param string $templatebotright text template
     * @return \self
     */
    public function set_templatebotright($templatebotright) {
        $this->templatebotright = $templatebotright;
        return $this;
    }

    /**
     * Set botleft text template
     *
     * @param string $templatebotleft text template
     * @return \self
     */
    public function set_templatebotleft($templatebotleft) {
        $this->templatebotleft = $templatebotleft;
        return $this;
    }

    /**
     * DO we include the QR code somewhere?
     *
     * @return bool
     */
    public function get_includeqr() {
        return $this->includeqr;
    }

    /**
     * Set whether or not to include QR code
     *
     * @param bool $includeqr
     * @return \block_coupon\coupon\pdf
     */
    public function set_includeqr($includeqr) {
        $this->includeqr = $includeqr;
        return $this;
    }

    /**
     * Get logo
     *
     * @return string full filepath to logo
     */
    public function get_logo() {
        return $this->logo;
    }

    /**
     * Set logo
     *
     * @param string $logo full filepath to logo
     * @return \self
     */
    public function set_logo($logo) {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Is the PDF already rendered?
     *
     * @var boolean
     */
    protected $isrendered = false;

    /**
     * image template ID
     *
     * @var string
     */
    protected $imagetemplateid = false;

    /**
     * collection of logo templates mappings
     * @var array
     */
    protected $logotemplates;

    /**
     * colelction of logo filepaths
     * @var array
     */
    protected $logos;

    /**
     * coupon we're currently rendering
     * @var \stdClass
     */
    protected $currentcoupon;

    /**
     * Create a new instance
     *
     * @param string $titlestring
     */
    public function __construct($titlestring) {
        $this->namestring = $titlestring;
        $this->generatordate = date('Y-m-d', time());
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8');

        $this->SetFont('helvetica', '', 12);
        $this->SetCreator('PDF Generator build 1.0');
        $this->SetAuthor('Sebsoft PDF Generator build 1.0');

        $this->SetTitle(get_string('pdf-meta:title', 'block_coupon'));
        $this->SetSubject(get_string('pdf-meta:subject', 'block_coupon'));
        $this->SetKeywords(get_string('pdf-meta:keywords', 'block_coupon'));

        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetMargins(0, 0, 0, true); // L-T-R.

        $this->SetAutoPageBreak(false, 0);
    }

    /**
     * Set generator date
     *
     * @param string $string date
     */
    public function set_generatordate($string) {
        $this->generatordate = $string;
    }

    /**
     * Output header
     * @return boolean
     */
    public function header() {
        // Try to load our logo.
        $this->set_coupon_logo($this->currentcoupon);
        // And continue processing.
        if (empty($this->imagetemplateid)) {
            return;
        }
        $this->printTemplate($this->imagetemplateid);
    }

    /**
     * Output footer
     * @return boolean
     */
    public function footer() {
        return false;
    }

    /**
     * generate() generate/render the PDF
     *
     * @param array $coupons
     * @return bool
     */
    public function generate($coupons) {
        if (!is_array($coupons)) {
            $coupons = array($coupons);
        }
        $this->coupons = $coupons;
        if ($this->isrendered) {
            return true;
        }
        $this->write_coupon_pages();
        $this->isrendered = true;

        return true;
    }

    /**
     * Set the logo to use for the coupon we're generating
     *
     * @param \stdClass $coupon
     */
    protected function set_coupon_logo($coupon) {
        global $CFG;
        if (isset($this->logotemplates[$coupon->logoid])) {
            $this->logo = $this->logos[$coupon->logoid];
            $this->imagetemplateid = $this->logotemplates[$coupon->logoid];
            return;
        }
        switch ($coupon->logoid) {
            case -1:
                // None.
                $this->logos[$coupon->logoid] = null;
                $this->logotemplates[$coupon->logoid] = null;
                break;
            case 0:
                // Default.
                $this->logos[$coupon->logoid] = $CFG->dirroot . '/blocks/coupon/pix/couponlogo.png';
                $this->logotemplates[$coupon->logoid] = $this->startTemplate();
                $dpi = 300; // Could also be 96? Seems the default.
                $this->Image($this->logos[$coupon->logoid], 0, 0, 0, 0,
                        '', '', '', false, $dpi, '', false, false, 1, true, false, true);
                $this->endTemplate();
                break;
            default:
                // File ID.
                $tempfile = \block_coupon\logostorage::get_tempfile_for($coupon->logoid);
                $this->logos[$coupon->logoid] = $tempfile->get_filepath();
                $this->logotemplates[$coupon->logoid] = $this->startTemplate();
                $dpi = 300; // Could also be 96? Seems the default.
                $this->Image($this->logos[$coupon->logoid], 0, 0, 0, 0,
                        '', '', '', false, $dpi, '', false, false, 1, true, false, true);
                $this->endTemplate();
                // Destroy tempfile.
                unset($tempfile);
        }
        // Now set internal variables.
        $this->logo = $this->logos[$coupon->logoid];
        $this->imagetemplateid = $this->logotemplates[$coupon->logoid];
    }

    /**
     * write coupon pages in the PDF.
     */
    protected function write_coupon_pages() {
        foreach ($this->coupons as $coupon) {
            // Set current coupon (we HAVE to use this method because templates/images only work in state 2).
            $this->currentcoupon = $coupon;
            // Get coupon courses.
            $courses = helper::get_coupon_courses($coupon);

            $txtmain = $this->compile_main($coupon, $courses);
            $txtbotleft = $this->compile_botleft();
            $txtbotright = $this->compile_botright();

            $this->startPage();
            $this->SetFont('helvetica', '', 10);

            $this->MultiCell(135, 50, $txtmain, false, 'C', false, 1, 15, 75, true, 0, true);
            $this->MultiCell(90, 100, $txtbotleft, false, 'L', false, 2, 4, 210, true, 0, true);
            $this->MultiCell(90, 100, $txtbotright, false, 'L', false, 2, 109, 210, true, 0, true);
            // QR.
            if ($this->includeqr) {
                $qr = $this->get_qr($coupon);
                $dpi = 300; // Could also be 96? Seems the default.
                $this->Image($qr->get_filepath(), 150, 70, 0, 0, '', '', '', false, $dpi, '', false, false, 0, true, false, true);
                unset($qr);
            }

            $this->endPage();
        }
    }

    /**
     * Get tempfile for the QR.
     *
     * @param \stdClass $coupon
     * @return \block_coupon\tempfile
     */
    protected function get_qr($coupon) {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/coupon/thirdparty/QrCode/src/QrCode.php');
        $data = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/qrin.php', array(
            'c' => $coupon->submission_code,
            'h' => sha1($coupon->id . $coupon->ownerid . $coupon->submission_code),
        ));

        $code = new \Endroid\QrCode\QrCode();
        $code->setText($data->out(false));
        $code->setSize(120);
        $code->setPadding(6);
        $code->setErrorCorrection('high');
        $code->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0));
        $code->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0));
        $code->setLabelFontSize(16);

        $filepath = \block_coupon\tempfile::get_storage_path('qr' . microtime(true) . uniqid('', true) . '.png');
        $code->render($filepath, 'png');
        return \block_coupon\tempfile::create_from_path($filepath);
    }

    /**
     * compile main template
     *
     * @param \stdClass $coupon
     * @param array $courses
     * @return string compiled string
     */
    protected function compile_main($coupon, $courses) {
        $find = array(
            '{coupon_code}',
            '{accesstime}',
            '{courses}'
        );
        if ((int)$coupon->enrolperiod === 0) {
            $accesstime = get_string('unlimited_access', 'block_coupon');
        } else if ($coupon->typ == generatoroptions::ENROLEXTENSION ) {
            $accesstime = get_string('extendaccess', 'block_coupon', format_time($coupon->enrolperiod));
        } else {
            $accesstime = format_time($coupon->enrolperiod);
        }
        $replace = array(
            '<div style="text-align: center; font-size: 200%; font-weight: bold">'.$coupon->submission_code.'</div>',
            $accesstime,
            '<b>'.implode(', ', $courses).'</b>'
        );

        return str_replace($find, $replace, $this->templatemain);
    }

    /**
     * compile botleft template
     *
     * @return string compiled string
     */
    protected function compile_botleft() {
        global $CFG;

        $find = array(
            '{site_url}'
        );
        $replace = array(
            $CFG->wwwroot
        );

        return str_replace($find, $replace, $this->templatebotleft);
    }

    /**
     * compile botright template
     *
     * @return string compiled string
     */
    protected function compile_botright() {
        global $CFG;

        $find = array(
            '{site_url}'
        );
        $replace = array(
            $CFG->wwwroot
        );

        return str_replace($find, $replace, $this->templatebotright);
    }

    /**
     * get_pdf_string() get the rendered PDF as a string
     *
     * @return string
     */
    public function get_pdf_string() {
        // Output as string.
        return $this->Output('ignore', 'S');
    }

}
