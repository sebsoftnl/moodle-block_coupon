<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once $CFG->dirroot . '/blocks/voucher/class/settings.php';
require_once $CFG->libdir . '/zend/Zend/Rest/Server.php';

// Require the new rest server :) - Sebastian
require_once(__DIR__ . "/../class/Rest_Server.php");
require_once(__DIR__ . "/../class/VoucherAPI.php");

$api_enabled = get_config('voucher', 'api_enabled');
$api_user = get_config('voucher', 'api_user');
$api_password = get_config('voucher', 'api_password');

if (!$api_enabled)
{
    header('HTTP/1.0 503 Service Unavailable');
    die('The voucher web service is currently disabled.');
}

if (!$api_user || empty($api_user) || !$api_password || empty($api_password))
{
    header('HTTP/1.0 503 Service Unavailable');
    die('The voucher API user has not been configured yet.');
}

if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $api_user &&
        isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW'] == $api_password)
{
    $server = new Rest_Server();
    $server->setClass('VoucherAPI');
    $server->handle();
}
else
{
    header('WWW-Authenticate: Basic realm="Voucher API"');
    header('HTTP/1.0 401 Unauthorized');
    die('401 Unauthorized');
}
?>
