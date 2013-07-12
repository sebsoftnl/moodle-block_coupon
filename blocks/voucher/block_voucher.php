<?php

/*
 * File: block_voucher.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

defined('MOODLE_INTERNAL') || die();
require_once 'class/settings.php';

class block_voucher extends block_base
{

    function init()
    {
        $this->title = get_string('blockname', BLOCK_VOUCHER);
        include BLOCK_VOUCHER_DIRROOT . 'version.php';
        $this->version = $plugin->version;
        $this->cron = $plugin->cron;
    }

    function get_content()
    {
        if ($this->content !== NULL)
        {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance))
        {
            print_error('No instance ' . BLOCK_VOUCHER);
        }
        $permissions = voucher_Helper::getPermission();

        $arrParam = array();
        $arrParam['id'] = $this->instance->id;
        $arrParam['courseid'] = $this->course->id;

        if ($permissions['generatevouchers'])
        {
            
            $url = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/generate_voucher.php', array('id' => $this->instance->id));
            $this->content->footer = html_writer::link($url, get_string('url:generate_vouchers', BLOCK_VOUCHER));
            
//            $this->content->text .= "<div>" . get_string('heading:generatevouchers', BLOCK_VOUCHER) . "</div>";
//            $this->content->text .= "<a href='" . BLOCK_VOUCHER_WWWROOT . "view/generate_voucher.php'>" . get_string('url:generate_vouchers', BLOCK_VOUCHER) . "</a>";
            
        } elseif ($permissions['inputvouchers']) {
            
            /*TODO:
             * create input field & button
             */
            
        }
    }

    function applicable_formats()
    {
        return array('my' => true,
            'site-index' => true,
            'course-view' => true,
            'course-view-social' => true,
            'mod' => true,
            'mod-quiz' => true);
    }

    function specialization()
    {
        global $COURSE;
        $this->course = $COURSE;
    }

    function instance_allow_config()
    {
        return true;
    }

    function instance_allow_multiple()
    {
        return false;
    }

    function hide_header()
    {
        return false;
    }

    function cron()
    {
        mtrace(' ');
        mtrace('---------------------------------------------------------------------------------');
        mtrace('-- RUN CRON FOR ' . strtoupper(BLOCK_VOUCHER));
        require_once BLOCK_VOUCHER_CLASSROOT . 'core/Cron.php';
        $c = new voucher_Cron();
        return $c->run();
        mtrace('---------------------------------------------------------------------------------');
    }

    /**
     * has own config
     */
    function has_config()
    {
        return true;
    }

}
