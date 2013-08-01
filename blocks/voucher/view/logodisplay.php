<?php

/**
 * File: file.php
 * Encoding: UTF-8
 * @package: branding
 *
 * @Version: 1.0.0
 * @Since 10-jul-2012
 * @Author: Rogier van Dongen :: sebsoft.nl
 * @Copyright sebsoft.nl
 * 
 * */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once $CFG->dirroot . '/blocks/voucher/class/settings.php';

$url = new moodle_url('/blocks/voucher/view/logodisplay.php');
$PAGE->set_url($url);

require_login(null, true);

$fn = BLOCK_VOUCHER_LOGOFILE;
if (!file_exists($fn)) {
    $fn = BLOCK_VOUCHER_DIRROOT . 'pix/Logo.png';
}
//die($fn);
_imgdisplay($fn);

function _imgdisplay($fn) {
    if (file_exists($fn)) {
        $sizeinfo = getimagesize($fn);
        if ($sizeinfo) {
            list($w, $h, $itype, $tagwh) = $sizeinfo;
            $mime = $sizeinfo['mime'];
            header("Content-type: $mime");
            header("Content-Length: " . filesize($fn));
            $fp = fopen($fn, 'rb');
            if ($fp !== false) {
                fpassthru($fp);
                fclose($fp);
                exit;
            }
        }
    }
}