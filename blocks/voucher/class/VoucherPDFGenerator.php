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

        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetMargins(0,0,0, true); //L-T-R

        $this->SetAutoPageBreak(false, 0);

        $fn = BLOCK_VOUCHER_LOGOFILE;
        if (!file_exists($fn)) {
            $fn = BLOCK_VOUCHER_DIRROOT . 'pix/Logo.png';
        }
        if (file_exists($fn)) {
            $this->_logo = $fn;
        }
    }

    public function setGeneratorDate($string) {
        $this->generatorDate = $string;
    }

    function header() {

        $this->Image($this->_logo, 0, 0, 0, 0, 'png', '', '', 2, 96, '', false, false, 1, true, false, true);
    }

    function footer() {

        return false;

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
            
            $this->MultiCell(150, 150, $txt_main, false, 'L', false, 1, 22, 100, true, 0, true);
            $this->MultiCell(80, 100, $txt_botleft, false, 'L', false, 2, 15, 210, true, 0, true);
            $this->MultiCell(80, 100, $txt_botright, false, 'L', false, 2, 109, 210, true, 0, true);
            
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