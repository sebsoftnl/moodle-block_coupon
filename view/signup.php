<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * user signup page.
 *
 * @package    block_coupon
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// No login check is expected since this is a signup script.
// @codingStandardsIgnoreLine
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/login/lib.php');

if (!$authplugin = signup_is_enabled()) {
    if (get_config('block_coupon', 'forceenableemailregistration')) {
        $CFG->registerauth = 'email';
        $authplugin = signup_is_enabled();
    } else {
        throw new \moodle_exception('notlocalisederrormessage', 'error', '',
                'Sorry, you may not use this page (signup is not enabled).');
    }
}

$PAGE->set_url('/blocks/coupon/view/signup.php');
$PAGE->set_context(context_system::instance());

// If wantsurl is empty or /blocks/coupon/view/signup.php, override wanted URL.
// We do not want to end up here again if user clicks "Login".
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = $CFG->wwwroot . '/';
} else {
    $wantsurl = new moodle_url($SESSION->wantsurl);
    if ($PAGE->url->compare($wantsurl, URL_MATCH_BASE)) {
        $SESSION->wantsurl = $CFG->wwwroot . '/';
    }
}

if (isloggedin() and !isguestuser()) {
    // Prevent signing up when already logged in.
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url('/login/logout.php',
        array('sesskey' => sesskey(), 'loginpage' => 1)), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('cannotsignup', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// If verification of age and location (digital minor check) is enabled.
if (\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
    $cache = cache::make('core', 'presignup');
    $isminor = $cache->get('isminor');
    if ($isminor === false) {
        // The verification of age and location (minor) has not been done.
        redirect(new moodle_url('/login/verify_age_location.php'));
    } else if ($isminor === 'yes') {
        // The user that attempts to sign up is a digital minor.
        redirect(new moodle_url('/login/digital_minor.php'));
    }
}

// Plugins can create pre sign up requests.
// Can be used to force additional actions before sign up such as acceptance of policies, validations, etc.
core_login_pre_signup_requests();

$mformsignup = new \block_coupon\forms\coupon\signup();

if ($mformsignup->is_cancelled()) {
    redirect(get_login_url());

} else if ($user = $mformsignup->get_data()) {
    // Add missing required fields.
    $user = signup_setup_new_user($user);

    $authplugin->user_signup($user, false); // Prints notice and link to login/index.php.
    // Added for coupon.
    \block_coupon\helper::claim_coupon($user->submissioncode, $user->id);
    // Unset sessionurl.
    unset($SESSION->wantsurl);
    // We SHALL place the notification ourselves.
    $emailconfirm = get_string('emailconfirm');
    $PAGE->navbar->add($emailconfirm);
    $PAGE->set_title($emailconfirm);
    $PAGE->set_heading($PAGE->course->fullname);
    echo $OUTPUT->header();
    notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");
    exit; // Never reached.
}

$submissioncode = optional_param('submissioncode', '', PARAM_ALPHANUMEXT);
$mformsignup->set_data(array('submissioncode' => $submissioncode));

$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

if (get_config('block_coupon', 'useloginlayoutonsignup')) {
    $PAGE->set_pagelayout('login');
}
$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
// Link to login.
$SESSION->wantsurl = $CFG->wwwroot . '/'; // Resolves issue #14.
$loginurl = new moodle_url($CFG->wwwroot . '/login/index.php');
echo html_writer::link($loginurl, get_string('signup:login', 'block_coupon'));
echo '<hr/>';
// Original code has option to use a renderer. We can NOT use this.
$mformsignup->display();
echo $OUTPUT->footer();
