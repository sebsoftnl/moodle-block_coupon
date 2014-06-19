<?php

/*
 * File: input_voucher.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once $CFG->dirroot . '/blocks/voucher/class/settings.php';

$id = required_param('id', PARAM_INT);

$doc_page = required_param('page', PARAM_RAW). '.html';


if ($id)    //DEFAULT CHECKS
{
    if (!$instance = $DB->get_record('block_instances', array('id' => $id)))
    {
        print_error("Instance id incorrect");
    }
    $context = get_context_instance(CONTEXT_BLOCK, $instance->id);
    $courseid = get_courseid_from_context($context);

    if (!$course = $DB->get_record("course", array("id" => $courseid)))
    {
        $course = get_site();
    }

    require_login($course, true);
    //ADD course LINK
    $PAGE->navbar->add(ucfirst($course->fullname), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$url = new moodle_url('/blocks/voucher/view/input_voucher.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:api:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:api:heading', BLOCK_VOUCHER));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

//make sure the moodle editmode is off
voucher_Helper::forceNoEditingMode();

echo $OUTPUT->header();


$url_doc_page = BLOCK_VOUCHER_DIRROOT . 'view/api_docs/' . $doc_page;
if (file_exists($url_doc_page)) {
    require_once($url_doc_page);
} else {
    print_error("error:wrong_doc_page", BLOCK_VOUCHER);
}


echo $OUTPUT->footer();
