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
     * Create a new instance
     *
     * @param string $titlestring
     */
    public function __construct($titlestring) {
        global $CFG;

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

        $fn = helper::get_coupon_logo();
        if (file_exists($fn)) {
            $this->logo = $fn;
        }
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
        if (!file_exists($this->logo)) {
            return;
        }
        if (empty($this->imagetemplateid)) {
            $this->imagetemplateid = $this->startTemplate();
            $this->Image($this->logo, 0, 0, 0, 0, '', '', '', false, 300, '', false, false, 1, true, false, true);
            //$this->Image($this->logo, 0, 0, 0, 0, '', '', '', true, 96, '', false, false, 1, true, false, true);
            $this->endTemplate();
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
     * write coupon pages in the PDF.
     */
    protected function write_coupon_pages() {

        foreach ($this->coupons as $coupon) {

            $txtmain = $this->compile_main($coupon);
            $txtbotleft = $this->compile_botleft();
            $txtbotright = $this->compile_botright();

            $this->startPage();
            $this->SetFont('helvetica', '', 10);

            $this->MultiCell(150, 150, $txtmain, false, 'L', false, 1, 15, 80, true, 0, true);
            $this->MultiCell(90, 100, $txtbotleft, false, 'L', false, 2, 4, 210, true, 0, true);
            $this->MultiCell(90, 100, $txtbotright, false, 'L', false, 2, 109, 210, true, 0, true);

            $this->endPage();
        }
    }

    /**
     * compile main template
     *
     * @param \stdClass $coupon
     * @return string compiled string
     */
    protected function compile_main($coupon) {
        $find = array(
            '{coupon_code}',
            '{accesstime}'
        );
        if ((int)$coupon->enrolperiod === 0) {
            $accesstime = get_string('unlimited_access', 'block_coupon');
        } else {
            $accesstime = get_string('days_access', 'block_coupon', $coupon->enrolperiod);
        }
        $replace = array(
            '<div style="text-align: center; font-size: 200%; font-weight: bold">'.$coupon->submission_code.'</div>',
            $accesstime
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
