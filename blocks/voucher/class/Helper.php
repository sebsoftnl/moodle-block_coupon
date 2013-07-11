<?php

/**
 * File: Helper.php
 * Encoding: UTF-8
 * @package: voucher
 *
 * @Version: 1.0.0
 * @Since 10-jul-2012
 * @Author: Menno de Ridder :: menno@sebsoft.nl
 * @Copyright menno@sebsoft.nl
 * 
 * */

/**
 * Helper class for various functionality
 */
class voucher_Helper
{

    /**
     * __construct() HIDE: WE'RE STATIC 
     */
    protected function __construct()
    {
        // static's only please!
    }

    /**
     * Check if we have permission for this
     * @param type $name
     * @return array | boolean
     */
    final static public function getPermission($name = '')
    {
        $context = get_context_instance(CONTEXT_SYSTEM);

        $array = array();
        //FIRST check if you are a super admin
        $array['administration'] = (has_capability('blocks/voucher:administration', $context)) ? true : false;
        //$array['secretariat'] = (has_capability('blocks/voucher:secretariat', $context)) ? true : false;
        //$array['student'] = (has_capability('blocks/voucher:view', $context)) ? true : false;

        if (!empty($name))
        {
            return $array[$name];
        }
        else
        {
            return $array;
        }
    }

    final public static function formatSize($size)
    {
        $size = (int) $size;
        $fmt = '%.02f' . self::_size_modifier($size);
        while ($size > 1024)
        {
            $size /= 1024.0;
        }
        return sprintf($fmt, $size);
    }

    final private static function _size_modifier($size)
    {
        if ($size > 1099511627776)
            return 'TB';
        elseif ($size > 1073741824)
            return 'GB';
        elseif ($size > 1048576)
            return 'MB';
        elseif ($size > 1024)
            return 'kB';
        else
            return 'B';
    }

    /**
     * Make sure editing mode is off and moodle doesn't use complete overview
     * @global object $USER
     * @global object $PAGE
     * @param moodle_url $redirectUrl
     */
    public static function forceNoEditingMode($redirectUrl = '')
    {
        global $USER, $PAGE;
        if (!empty($USER->editing))
        {
            $USER->editing = 0;

            if (empty($redirectUrl))
            {
                $params = $PAGE->url->params();
                $redirectUrl = new moodle_url($PAGE->url, $params);
            }
            redirect($redirectUrl);
        }
    }

    public static final function printAutoRedirect(moodle_url $url, $return = false)
    {
        $str = '';
        $str .= '<div id="redir" style="color: #00aa00; background-color: #ffff80; text-align: center; font-weight: bold; font-family: Verdana, Helvetica, Arial, Sans-serif"></div>';
        $str .= '<script type = "text/javascript">';
        $str .= 'var bc=5;';
        $str .= 'function autorefresh(){bc--; if (bc>0){ document.getElementById(\'redir\').innerHTML="' . get_string('redirect_in', BLOCK_VOUCHER) . '" + bc + " ' . get_string('seconds', BLOCK_VOUCHER) . '";setTimeout("autorefresh()",1000); }else {window.location="' . $url->out(true) . '";}}';
        $str .= '</script>';

        if ($return)
        {
            return $str;
        }
        else
        {
            echo $str;
        }
    }

    public static final function createBlockUrl($relUrl, $params)
    {
        return new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
    }

    public static final function createBlockLink($relUrl, $params, $linktext, $linktitle = '')
    {
        $uri = new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
        $aparams = array('href' => str_replace('&amp;', '&', $uri));
        if (!empty($linktitle))
        {
            $aparams['title'] = $linktitle;
        }
        return html_writer::tag('a', $linktext, $aparams);
    }

    public static final function createBlockButton($relUrl, $params, $buttontext, $title = '')
    {
        $uri = new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
        $aparams = array('onclick' => 'window.location=\''.str_replace('&amp;', '&', $uri).'\'');
        if (!empty($title))
        {
            $aparams['title'] = $title;
        }
        return html_writer::tag('button', $buttontext, $aparams);
    }

}