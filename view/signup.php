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
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/user/editlib.php');

// Try to prevent searching for sites that allow sign-up.
if (!isset($CFG->additionalhtmlhead)) {
    $CFG->additionalhtmlhead = '';
}
$CFG->additionalhtmlhead .= '<meta name="robots" content="noindex" />';

if (empty($CFG->registerauth)) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}
$authplugin = get_auth_plugin($CFG->registerauth);

if (!$authplugin->can_signup()) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

// HTTPS is required in this page when $CFG->loginhttps enabled.
$PAGE->https_required();

$PAGE->set_url('/login/signup.php');
$PAGE->set_context(context_system::instance());

$mformsignup = new \block_coupon\forms\coupon\signup();

if ($mformsignup->is_cancelled()) {
    redirect(get_login_url());

} else if ($user = $mformsignup->get_data()) {
    $user->confirmed   = 0;
    $user->lang        = current_language();
    $user->firstaccess = time();
    $user->timecreated = time();
    $user->mnethostid  = $CFG->mnet_localhost_id;
    $user->secret      = random_string(15);
    $user->auth        = $CFG->registerauth;
    // Initialize alternate name fields to empty strings.
    $namefields = array_diff(get_all_user_name_fields(), useredit_get_required_name_fields());
    foreach ($namefields as $namefield) {
        $user->$namefield = '';
    }

    $authplugin->user_signup($user, false); // Prints notice and link to login/index.php.
    // Added for coupon.
    \block_coupon\helper::claim_coupon($user->submissioncode, $user->id);
    // Unset sessionurl.
    unset($SESSION->wantsurl);
    // Redirect to homepage?
    $message = get_string('signup:success', 'block_coupon');
    redirect(new moodle_url($CFG->wwwroot . '/login/index.php'), $message, 3);
    exit; // Never reached.
}

// Make sure we really are on the https page when https login required.
$PAGE->verify_https_required();

$submissioncode = optional_param('submissioncode', '', PARAM_ALPHANUMEXT);
$mformsignup->set_data(array('submissioncode' => $submissioncode));

$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
// Link to login.
$SESSION->wantsurl = $CFG->wwwroot . '/'; //Resolves issue #14.
$loginurl = new moodle_url($CFG->wwwroot . '/login/index.php');
echo html_writer::link($loginurl, get_string('signup:login', 'block_coupon'));
echo '<hr/>';
$mformsignup->display();
echo $OUTPUT->footer();
