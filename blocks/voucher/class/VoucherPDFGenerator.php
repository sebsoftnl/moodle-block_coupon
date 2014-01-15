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
//    protected $_voucherHeaderText = 'Moodle Voucher Avetica';
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
    protected $_voucherPageTemplateMain = '';
    protected $_voucherPageTemplateBotLeft = '';
    protected $_voucherPageTemplateBotRight = '';

    /**
     * getVoucherPageTemplateMain() gets _voucherPageTemplateMain
     * 
     * @access public
     * @return string  value of _voucherPageTemplateMain
     * @see $_voucherPageTemplateMain
     */
    public function getVoucherPageTemplateMain() {
        return $this->_voucherPageTemplateMain;
    }

    /**
     * getVoucherPageTemplateBotRight() gets _voucherPageTemplateBotRight
     * 
     * @access public
     * @return string  value of _voucherPageTemplateBotRight
     * @see $_voucherPageTemplateBotRight
     */
    public function getVoucherPageTemplateBotRight() {
        return $this->_voucherPageTemplateBotRight;
    }
    
    /**
     * getVoucherPageTemplateMainBotLeft() gets _voucherPageTemplateBotLeft
     * 
     * @access public
     * @return string  value of _voucherPageTemplateBotLeft
     * @see $_voucherPageTemplateBotLeft
     */
    public function getVoucherPageTemplateBotLeft() {
        return $this->_voucherPageTemplateBotLeft;
    }

    /**
     * setVoucherPageTemplateMain(string) sets _voucherPageTemplateMain
     * 
     * @access public
     * @param string $voucherPageTemplateMain  value of _voucherPageTemplateMain
     * @see $_voucherPageTemplateMain
     */
    public function setVoucherPageTemplateMain($voucherPageTemplate) {
        $this->_voucherPageTemplateMain = $voucherPageTemplate;
        return $this;
    }
    
    /**
     * setVoucherPageTemplateBotRight(string) sets _voucherPageTemplateBotRight
     * 
     * @access public
     * @param string $voucherPageTemplateBotRight  value of _voucherPageTemplateBotRight
     * @see $_voucherPageTemplateBotRight
     */
    public function setVoucherPageTemplateBotRight($voucherPageTemplate) {
        $this->_voucherPageTemplateBotRight = $voucherPageTemplate;
        return $this;
    }

    /**
     * setVoucherPageTemplateBotLeft(string) sets _voucherPageTemplateBotLeft
     * 
     * @access public
     * @param string $voucherPageTemplateBotLeft  value of _voucherPageTemplateBotLeft
     * @see $_voucherPageTemplateBotLeft
     */
    public function setVoucherPageTemplateBotLeft($voucherPageTemplate) {
        $this->_voucherPageTemplateBotLeft = $voucherPageTemplate;
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
        $this->Image($this->_logo, 0, 0, 850, 1000, 'png', '', 'C', false, 300, '', false, false, 0, false, false, false);
        
        // header text
        $this->SetXY(0, 5);
        $this->SetFont('helvetica', '', 24);
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
            $txt_main = $this->_compileTemplateMain($voucher);
            $txt_botleft = $this->_compileTemplateBotLeft();
            $txt_botright = $this->_compileTemplateBotRight();
            
            $this->startPage();
            $this->SetFont('helvetica', '', 10);
            
            $this->MultiCell(150, 150, $txt_main, null, 'L', false, 1, 22, 78, true, 0, true);
            $this->MultiCell(80, 100, $txt_botleft, null, 'L', false, 2, 15, 168, true, 0, true);
            $this->MultiCell(80, 100, $txt_botright, null, 'L', false, 2, 107, 168, true, 0, true);
            
            //$this->writeHTML($html);
            $this->endPage();
        }
    }

    protected function _compileTemplateMain($voucher) {

        $find = array(
            '{voucher_code}'
        );
        $replace = array(
            $voucher->submission_code,
        );

        $txt_main = $this->_voucherPageTemplateMain;
        $txt_main = str_replace($find, $replace, $txt_main);

        return $txt_main;
    }

    protected function _compileTemplateBotLeft() {
        global $CFG;

        $find = array(
            '{site_url}'
        );
        $replace = array(
            $CFG->wwwroot
        );

        $txt_botleft = $this->_voucherPageTemplateBotLeft;
        $txt_botleft = str_replace($find, $replace, $txt_botleft);

        return $txt_botleft;
    }

    protected function _compileTemplateBotRight() {
        global $CFG;

        $find = array(
            '{site_url}'
        );
        $replace = array(
            $CFG->wwwroot
        );

        $txt_botright = $this->_voucherPageTemplateBotRight;
        $txt_botright = str_replace($find, $replace, $txt_botright);

        return $txt_botright;
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