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
        global $CFG;
        
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

        // We'll fill the array of menu items with everything the logged in user has permission to
        $menu_items = array();
        
        // Generate Voucher
        if ($permissions['generatevouchers'])
        {
            $url_generate_vouchers = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/generate_voucher.php', array('id' => $this->instance->id));
            $url_uploadimage = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/uploadimage.php', array('id' => $this->instance->id));
            $url_api_docs = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/api_docs.php', array('id' => $this->instance->id, 'page'=>'index'));
            
            $menu_items[] = html_writer::link($url_generate_vouchers, get_string('url:generate_vouchers', BLOCK_VOUCHER));
            $menu_items[] = html_writer::link($url_uploadimage, get_string('url:uploadimage', BLOCK_VOUCHER));
            $menu_items[] = html_writer::link($url_api_docs, get_string('url:api_docs', BLOCK_VOUCHER));
            
        }
        
        // View Reports
        if ($permissions['viewreports'])
        {
            $url_reports = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/reports.php', array('id' => $this->instance->id));
            $url_unused_reports = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/unused_vouchers.php', array('id' => $this->instance->id));
            
            $menu_items[] = html_writer::link($url_reports, get_string('url:view_reports', BLOCK_VOUCHER));
            $menu_items[] = html_writer::link($url_unused_reports, get_string('url:view_unused_vouchers', BLOCK_VOUCHER));
        }

        // Input Voucher
        if ($permissions['inputvouchers']) {
            $url_input_voucher = new moodle_url(BLOCK_VOUCHER_WWWROOT . 'view/input_voucher.php', array('id' => $this->instance->id));

            $voucher_form = "
                <form action='$url_input_voucher' method='POST'>
                    <table>
                        <tr>
                            <td>" . get_string('label:enter_voucher_code', BLOCK_VOUCHER) . ":</td>
                        </tr>
                        <tr>
                            <td><input type='text' name='voucher_code'></td>
                        </tr>
                        <tr>
                            <td><input type='submit' value='" . get_string('button:submit_voucher_code', BLOCK_VOUCHER) . "'></td>
                        </tr>
                    </table>
                    <input type='hidden' name='submitbutton' value='Submit Voucher' />
                    <input type='hidden' name='_qf__input_voucher_form' value='1' />
                    <input type='hidden' name='sesskey' value='" . sesskey() . "' />
                </form>";

            $menu_items[] = $voucher_form;
        }
        
        // Now print the menu blocks
        foreach($menu_items as $item) {
            
            $this->content->footer .= $item . "<br />";
            
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
        require_once BLOCK_VOUCHER_CLASSROOT . 'Cron.php';
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
