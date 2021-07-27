Version 4.0.9.1 (build 2020010814)
* Fixed bug in new function introduced in 4.0.9.

----------

Version 4.0.9 (build 2020010813)
* Fixed part of code checker compliance.
* Added method to allow signup, even when no signups are allowed in the site (disabled by default).

----------

Version 4.0.8 (build 2020010810)
* Fixed error notification with usage in qr code.
* Fixed error notification(s) if coupon is already used.

----------


Version 4.0.7 (build 2020010809)
* Fixed coupon type enrolment extension. Status is now forced to ACTIVE (Thanks Hiro Nakamura).
* Changed some language strings (EN only) so the period character does not end up outside of the containing paragraph.

----------

Version 4.0.7 (build 2020010808)
* Fixed main assert_not_claimed function: reinstated exception
* Added component to generic error (input_coupon.php)

----------

Version 4.0.7 (build 2020010807)
* Added task to remove cohort members (i.e. take care of cohort unenrolment).
* Fixed webservice call return for generate_coupons_for_cohorts.
* Fixed webservice call return for generate_coupons_for_course.

----------

Version 4.0.6 (build 2020010806)
* Added autocompletes for cohort/cohort courses.
* Added user_deleted observer.
* Fixed notice (generate_pdf) on POST data (user requesting coupon; made field an advcheckbox).
* Fixed CSS issue... _sigh_.
* Undo previous work on user column (used coupons).
* Fix sorting on user column.
* Add sorting on owner column.
* Added user/claimed_on columns for personalised coupons.
* Fixed langstring 'coupon_notification_content'.
* Fixed get_string variables (in helper).
* Fixed "Curly brace syntax for accessing array elements and string offsets has
   been deprecated in PHP 7.4. Found: $codealphabet{$n}" in codegenerator.

----------

Version 4.0.6 (build 2020010805)
* Added INDICES to block_coupon.

----------

Version 4.0.6 (build 2020010804)
* Changed course selection when generating coupons to auto-complete element.
* Changed cohort selection when generating coupons to auto-complete element.
* Added services.
* Added 10px whitespace between buttons in block.
* Set width of most default for elements to 75% (never EVER set to 100% due to autocomplete element).
* Modified course group selection to checkboxes.
* Added tab dedicated to personalised coupons (uses global setting to enable!).
* Add field to manipulate code size for coupons.

Special thanks to Gemma Lesterhuis (Lesterhuis Training & Consultancy)
for useful input, funding of these changes, bug reports and beta testing.

----------

Version 4.0.5 (build 2020010803)
* Added option to include PDF when sending personalised coupons.
* Modified (AGAIN) sending process of personal coupons.

----------

Version 4.0.4.2 (build 2020010802)
* Changed length of batchid to 255.

----------

Version 4.0.4.1 (build 2020010801)
* Updated privacy provider.
* Removed https verification from custom signup page.

Special thanks to Gemma Lesterhuis (Lesterhuis Training & Consultancy)
for useful input, bug reports and beta testing.

----------

Version 4.0.4 (build 2020010800)
* Validated Moodle 3.8
* Changed a few table implementations (fixes sorting bugs)
* Changed headers/sorting on tables for USER fields

Special thanks to Gemma Lesterhuis (Lesterhuis Training & Consultancy)
for donating to Moodle 3.8 validation, useful input and beta testing.

----------

Version 4.0.3-RC1 (build 2019031805)
* Release candidate for beta testing

----------

Version 4.0.2 (build 2019031804)
* Added ADVANCED global $CFG options to manipulate template bot output offsets.
* Fixed wrongful addition of "batchID" when adding any filter (used/unused coupon view)
* Fixed PHP notice in batchID filter
* Fixed some more Oracle table alias issues
* Fixed wrong links in notification context urls
* Added maillog tab. Still too many requests about mails not being sent, so we decided
   to wrap Moodle's email_to_user method and log *any* debug output if relevant.
* General code cleanup (as a result of Travis-CI)

----------

Version 4.0.1 (build 2019031802)
* Fixed param type on download page.
* Added an overview to display downloadable batches (only scans for coupon archives in moodledata).
* Added option to download from this overview.
* Fixed issue #27 again (wrong link to user profile)
* Fixed issue #38 again (wrong link to user profile, different view)
* Fixed issue #37 (configurable button class for links; see global plugin configuration)
* Fixed issue #36 (had already been done in build 2018050301, only noted here for automatic issue resolving)
* Filter forms are now collapsed by default. Always expanded drove me nuts on larger filters/forms.
* Fixed breaking LIMIT database issues affecting OracleDB ("LIMIT" clauses not supported as such)
* Fixed breaking alias issues affecting OracleDB (Oracle does NOT support the "AS" keyword)
* Fixed issue #39 (partial) Added filter capabilities in error report.
* Fixed issue #31 (part 2: message sent twice)

* Special thanks to Theodore Pylarinos for providing extremely detailed information
   that helped to haunt down the bug of downloads not being found!
* Special thanks to Wade Colclough from Zuken Limited for providing excellent feedback
   and willingness to put in the time and effort to help fixing issues related to Oracle DB.
----------

Version 4.0.0 (build 2019031801)
* Added correct userid identifier on requests table so the link to the user
   profile points to the correct one (extension to issue #27)
----------

Version 4.0.0 (build 2019031800)
* Small change in workflow of user requested coupons
* Added mailing and notification of accepted user requested coupons
* Added internal Moodle notifications
* Code overhaul: nearly all logic has been rewritten for maintainability reasons.
* Verified workings on Moodle 3.4 (updated version.php accordingly)
* AMD modules have now properly been minified
* We're NO longer "following" Moodle versions.

* Thanks everyone for all feedback!
----------

Version 3.5.2 (build 2018050306)
* Fixed issue(s) with CSV/Manual recipients not working.
* Coupon batches now all have the same "timecreated"
* Removed sorting of "action" column in coupon overviews
* Custom mail function is now replaced with Moodle's own email_to_user()
* Due to MANY issues where the email containing the coupons is not received, the
   decision was made to change the process to sending an email containing a
   download link where the generated batch can be downloaded from instead of
   providing the email with an attachment.
* Fixed issue #22 (we now use TCPDF's own QR Code generator)
* Added/fixed validation on "request user" configuration form
* Added more information to coupons requests overview table
* In line with issue 32, configuration of the enrolment duration for course type
  coupons has been moved to the course selection page.
* Fixed issue #35 (bug in observers)
* Fixed issue #34 (feature request for zip name)
* Fixed issue #32 (PDF preview)
* Fixed issue #30 (request user - required field setting amount of coupons has no effect)
* Fixed issue #29 (request user settings: added explanations)
* Fixed issue #28 (wrong paging/view for requestusers)
* Fixed issue #27 (wrong link to user profile in requestusers table)
* Changed sourcecode here and there to prevent debug output/PHP notices
* Removed restriction on amount of coupons when only generating the codes.
* Added coupon cleanup confirmation screen.

* Thanks Franky Just for your valuable feedback, bug reports and additional remarks!
----------

Version 3.5.1 (build 2018050305)
* Fixed issue #23 (invalid use of PARAM_RAW)
* Fixed issue #24 (redirect vulnerabilities -> changed all to PARAM_LOCALURL)
* Fixed issue #25 (removed use of deprecated function notify_problem)
* Fixed some missing capability checks / security issues

* Thanks Dan Marsden for your valuable feedback, bug reports and plugin review!
* Thanks Jesús Rincón for raising issue #25

* backported *some* of the issues to the Moodle 3.0+ release
* NOTE: All other releases except one Moodle 3.0+ release and the most recent
        Moodle 3.5+ release have been pulled.
----------

Version 3.5.0 (build 2018050304)
* Fixed issue #21 (cohort_is_member fatal error: thanks Michael Neulinger)
----------

Version 3.5.0 (build 2018050303)
* Fixed faulty course / cohort ID filter
* Added course groups filter possibilities
* Added "timeclaimed" information to keep track of when coupons were actually claimed
* Removed table sorting for courses/cohorts/groups (didn't work and should have never been part of the tables)
* Added quick rudimentary check to validate whether a person is already "signed up" when entering a code.
  (i.e. member in all linked cohorts or enrolled for all linked courses).

* Thanks Kevin Freeborn for your very valuable feedback!
----------

Version 3.5.0 (build 2018050302)
* Added possibility to only generate coupon codes (no PDFs will be generated, nor will they be sent!)
* Added batchid field to database to track generated batches of coupons
* Added custom batch naming when generating coupons (system automatically creates one if not provided)
* Added possibility to filter coupons on BATCH ID/name
----------

Version 3.5.0 (build 2018050301)
* Changed internal signup to comply to Moodle 3.5.
* Added option global to use login page layout (which is a Moodle standard really, but wasn't enforced)
* Added coupon code error checking to signup form (refuse signup in case coupon code is invalid)
* Added coupon request possibilities (please refer to manual for usage description)
* Added global options for and display of help texts for custom signup and coupon input texts
* Added possibility to request coupons for course types only. Involves admin to configure both the courses and allowed users
* Removed deprecated verify_https_required() call from custom (internal) signup
----------

Version 3.5.0 (build 2018050300)
* FIXED issue #15 ("mdb->get_record() found more than one record" when calling find_block_instance_id()).
* FIXED issue #19 where enrolment was never updated when using a new coupon after enrolment expired.
* Fixed "Sent" column in coupon overviews (displayed information based on wrong field)
* Added privacy provider
* Validated functionality for Moodle 3.5 onwards
* Minimum required Moodle version: 3.5
----------

Version 3.3.2 (build 2017092503)
* Added role selection for coupons (course type only)
----------

Version 3.3.1 (build 2017092501)
* Added coupon code to "progress report" for validation purposes (feature request)
* Added claimed user to "used coupons" overview for validation purposes (feature request)
----------

Version 3.3.0 (build 2017092500)
* Fixed deprecated pix_url references (replaced by image_url)
* Validated functionality for Moodle 3.3 onwards
* Minimum required Moodle version: 3.3
----------

Version 2017052402 (Release 3.0.3 (build 2017052402))
* Fixed bug in cohort type coupons (fixed incorrect usercheck and cohort synchronisation)
* Added option to display QR Code in PDF or leave it out.

* Thanks to everyone for their valuable input and remarks.
-----

Version 2017052401 (Release 3.0.2 (build 2017052401))
* Resolved issue #14

* Thanks to everyone for their valuable input and remarks.
-----

Version 2017052400 (Release 3.0.1 (build 2017052400))
* Added new type of coupon: extend (course) enrolment
* Added webservices!
* Incorporated some changes here and the in overviews

* Thanks to everyone for their valuable input and remarks.
-----

Version 2017050100 (Release 3.0.0 (build 2017050100))
* Added $CFG->wwwroot on all moodle_urls
* Added setting to choose default assigned role (resolve issue #12: Student role problem.
  Roles were fetched by shortname = 'student', NOT foolproof)
* Disabled sorting by 'owner' column in coupon overviews (resolve issue #13).
* Fixed error notification when inserting to table block_coupon_courses
* Added course name(s) to coupons
* Changed default coupon template-main contents (language files)
* Added filtering for progress reports
* Added filtering for coupon overview
* Code overhaul to comply to stricter Moodle coding standards (codechecker, Moodle phpdoc check)
* Added course_deleted event handler
* Added cohort_deleted event handler
* Added custom cleaning of coupons
* Added QR based coupon to PDF including processing
* Added signup for QR based coupon when user is not logged on yet
* Added possibility for signup with a coupon code (in block_coupon, when user is not yet logged in)
* Added new manager to manage multiple coupon background
* Added option to select coupon background to use
* minimum required version: Moodle3 3.0

* Thanks to everyone for their valuable input and remarks.
-----

Version 2016040800 (Release 2.7.3 (build 2016040800))
* raising memory limit and maximum execution time when generating coupons.
* Fixed cleanup task
* Fixed last page of wizard not displaying selected groups
* Updated readme
* Added ability to delete unused coupons.

* Feedback and general remarks or ideas for improvement are still highly valued.
-----

Version 2015010107 (Release 2.7.2 (build 2015010107))
* Changed "max coupons" setting to textual input. No more hardcoded limit of 100 coupons.
* Added delete option to unused coupons overview
* Added settings and task to automatically clean up unused/unclaimed coupons

* Thanks to anyone and everyone for their much valued feedback!
-----

Version 2015010102 (Release 2.7.0 (build 2015010102))
* Added missing db/tasks.php
* Limited applicable_formats: block can only be used on main site page and "my" dashboard view
* Improved renderer (more use of html_writer)
* Fixed default setting when starting coupon generator
* Included settings file in /classes/task/sendcoupons.php (gave NOTICE)
* Fixed a few small (non critical) bugs
* Added generatoroptions and generator for cleaner code to generate coupons.

* Thanks to, a.o., David Mudrák for raising issues leading to this version.
-----
