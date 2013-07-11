<?php

/**
 * File: block_voucher.php
 * Encoding: UTF-8
 * @package: voucher
 *
 * @Version: 1.0.0
 * @Since 11-jul-2013
 * @Author: Menno de Ridder :: menno@sebsoft.nl
 * @Copyright menno@sebsoft.nl
 *
 * */
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

        if ($permissions['administration'])
        {
            $this->content->text .= html_writer::start_tag('div');
            $this->content->text .= html_writer::tag('div', get_string('heading:administration', BLOCK_VOUCHER));
            $this->content->text .= html_writer::end_tag('div');

			// add code here
        }
        elseif ($permissions['use_voucher'])
        {
            $this->content->text .= html_writer::start_tag('div');
            $this->content->text .= html_writer::tag('div', get_string('heading:use_voucher', BLOCK_VOUCHER));
            $this->content->text .= html_writer::start_tag('div');
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
