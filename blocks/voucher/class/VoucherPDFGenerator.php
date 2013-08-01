<?php

/*
 * File: Helper.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 18-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

require_once $CFG->dirroot . '/lib/pdflib.php';

class voucher_PDF extends pdf {

//    protected $_JUMBOYELLOW = array(252, 197, 0);
    protected $_voucherHeaderText = 'Moodle Voucher Avetica';
    protected $_fontPath = '';
    private $namestring;
    private $generatorDate;
    protected $_vouchers;
    protected $_logo;

    /**
     * $_voucherPageTemplate
     * 
     * @access protected
     * @var string
     */
    protected $_voucherPageTemplate = '';

    /**
     * getVoucherPageTemplate() gets _voucherPageTemplate
     * 
     * @access public
     * @return string  value of _voucherPageTemplate
     * @see $_voucherPageTemplate
     */
    public function getVoucherPageTemplate() {
        return $this->_voucherPageTemplate;
    }

    /**
     * setVoucherPageTemplate(string) sets _voucherPageTemplate
     * 
     * @access public
     * @param string $voucherPageTemplate  value of _voucherPageTemplate
     * @see $_voucherPageTemplate
     */
    public function setVoucherPageTemplate($voucherPageTemplate) {
        $this->_voucherPageTemplate = $voucherPageTemplate;
        return $this;
    }

//    public function getFontPath()
//    {
//        return $this->_fontPath;
//    }
//
//    public function setFontPath($fontPath)
//    {
//        $this->_fontPath = $fontPath;
//    }

    public function getLogo() {
        return $this->_logo;
    }

    public function setLogo($logo) {
        $this->_logo = $logo;
    }

    /**
     * $_isRendered
     * 
     * @access protected
     * @var boolean
     */
    protected $_isRendered = false;

    public function __construct($titlestring) {
        global $CFG;

        $this->namestring = $titlestring;
        $this->generatorDate = date('Y-m-d', time());
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8');

        $this->SetFont('helvetica', '', 12);
        $this->SetCreator('PDF Generator build 1.0');
        $this->SetAuthor('Sebsoft PDF Generator build 1.0');

        $this->SetTitle(get_string('pdf-meta:title', BLOCK_VOUCHER));
        $this->SetSubject(get_string('pdf-meta:subject', BLOCK_VOUCHER));
        $this->SetKeywords(get_string('pdf-meta:keywords', BLOCK_VOUCHER));

        $this->SetHeaderMargin(50);
        $this->SetFooterMargin(20);
        $this->SetMargins(10, 40, 10, true); //L-T-R

        $this->SetAutoPageBreak(TRUE, 15);

        //$this->setFontPath($CFG->dirroot . '/blocks/jumbobase/fonts/');
        //$this->_loadFonts();
        $fn = BLOCK_VOUCHER_LOGOFILE;
        if (!file_exists($fn)) {
            $fn = BLOCK_VOUCHER_DIRROOT . 'pix/Logo.png';
        }
        if (file_exists($fn)) {
            $this->_logo = $fn;
        }
    }

//    function _loadFonts()
//    {
//        $fonts = array(
//            'trebuchetms' => array('', 'trebuchetms.php'),
//            'trebuchetmsB' => array('B', 'trebuchetmsb.php'),
//            'trebuchetmsBI' => array('BI', 'trebuchetmsbi.php'),
//            'trebuchetmsI' => array('I', 'trebuchetmsi.php'),
//            'jumbosans5' => array('', 'jthsab5_.php'),
//            'jumbosans5I' => array('I', 'jthsab5i.php'),
//            'jumbosans7' => array('', 'jthsab7_.php'),
//            'jumbosans7I' => array('I', 'jthsab7i.php'),
//            'jumbosans9' => array('', 'jthsab9_.php'),
//            'jumbosans9I' => array('I', 'jthsab9i.php'),
//        );
//
//        foreach ($fonts as $family => $font)
//        {
//            $this->addFont($family, $font[0], $this->_fontPath . $font[1]);
//        }
//    }

    public function setGeneratorDate($string) {
        $this->generatorDate = $string;
    }

    function header() {

        // this is just guessing about SVG placement (i hope this will work everywhere)
        //$this->Image($this->_logo, $ml - 18, $this->h - 32, 80, 0, '', 'L', '', '');
        $this->Image($this->_logo, 0, 0, 0, 0, '', '', 'L', '', '');
        // omlijning
        //$rw = $this->w - 10;
        //$rh = $this->h - 10;
        //$this->Rect(5, 5, $rw, $rh);
        // gele balk boven
//        $this->Rect(0, 0, $this->w, 25, 'F', array(), $this->_JUMBOYELLOW);

        // header text
        $this->SetXY(0, 5);
        $this->SetFont('helvetica', '', 24);
        $this->Cell(0, 0, $this->_voucherHeaderText, 0, 1, 'C');
    }

    function footer() {
        // Diplay footer / page number
//        if (empty($this->pagegroups))
//        {
//            $pagenumtxt = $this->l['w_page'] . ' ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
//        }
//        else
//        {
//            $pagenumtxt = $this->l['w_page'] . ' ' . $this->getPageNumGroupAlias() . ' / ' . $this->getPageGroupAlias();
//        }
//        $this->SetFont('helvetica', '', 8);
//        $cur_y = $this->y;
//        $this->SetTextColor(0, 0, 0);
//
//        $this->SetY($cur_y);
//        //Print page number
//        if ($this->getRTL())
//        {
//            $this->SetX($this->original_rMargin);
//            $this->Write(0, 0, $pagenumtxt, 'T', 0, 'L');
//        }
//        else
//        {
//            $this->SetXY(15, $this->h - 10);
//            $this->Write(0, $pagenumtxt, '', 0, 'C');
//        }
        //set style for cell border
        $line_width = 0.85 / $this->k;

        $style = $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
//        $style = $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->_JUMBOYELLOW));

        $ml = $this->lMargin;
        $w = $this->w - $ml;
        $this->Line($ml, $this->h - 20, $w, $this->h - 20, $style);
    }

    function FrontPage() {
        return false;
    }

    /**
     * generate() generate/render the PDF
     * 
     * @access public
     * @return bool
     */
    public function generate($vouchers) {
        if (!is_array($vouchers)) {
            $vouchers = array($vouchers);
        }
        $this->_vouchers = $vouchers;
        if ($this->_isRendered) {
            return true;
        }
        $this->FrontPage();
        $this->_writeVoucherPages();
        $this->_isRendered = true;
        return true;
    }

    protected function _writeVoucherPages() {
        foreach ($this->_vouchers as $voucher) {
            $html = $this->_compileTemplate($voucher);
            $this->startPage();
            $this->SetFont('helvetica', '', 12);
            $this->writeHTML($html);
            $this->endPage();
        }
    }

    protected function _compileTemplate($voucher) {
        global $CFG, $SITE;

        $find = array(
            '{vouchercode}',
            '{site_url}',
            '{site_name}'
        );
        $replace = array(
            $voucher->submission_code,
            $CFG->wwwroot,
            $SITE->fullname
        );
        $html = $this->_voucherPageTemplate;
        $html = str_replace($find, $replace, $html);

        return $html;
    }

    /**
     * getPDFString() get the rendered PDF as a string
     * 
     * @access public
     * @return string
     */
    public function getPDFString() {
        // output as string
        return $this->Output('ignore', 'S');
    }

}

?>