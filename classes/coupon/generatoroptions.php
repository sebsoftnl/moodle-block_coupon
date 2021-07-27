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
 * Coupon code generator options
 *
 * File         generatoroptions.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\coupon\generatoroptions
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generatoroptions {
    /**
     * COURSE type generator
     */
    const COURSE = 'course';
    /**
     * COHORT type generator
     */
    const COHORT = 'cohort';
    /**
     * ENROLEXTENSION type generator
     */
    const ENROLEXTENSION = 'enrolext';

    /**
     * generator type
     * @var string 'course' of 'cohort'
     */
    public $type;
    /**
     * coupon code size
     * @var int
     */
    public $codesize;
    /**
     * Number of coupons to generate
     * @var int
     */
    public $amount;
    /**
     * moodle user id that generated the coupons
     * @var int
     */
    public $ownerid;
    /**
     * Length of enrolment (only applicable for course type coupons)
     * @var int
     */
    public $enrolperiod = 0;
    /**
     * URL to redirect to after coupon submission
     * @var string
     */
    public $redirecturl;
    /**
     * coupon recipients (only applicable when personalized)
     * @var array
     */
    public $recipients;
    /**
     * Date to send out coupons (only applicable when personalized)
     * @var int
     */
    public $senddate = 0;
    /**
     * Email template (only applicable when personalized)
     * @var string
     */
    public $emailbody;
    /**
     * Cohort IDS the coupons are generated for
     * @var array
     */
    public $cohorts = array();
    /**
     * Course IDS the coupons are generated for
     * @var array
     */
    public $courses = array();
    /**
     * Group IDS the coupons are generated for (only applicable for course type)
     * @var array
     */
    public $groups = array();

    /**
     * Recipient's emailaddress to either send a status to, or the coupons itself
     * @var array
     */
    public $emailto;
    /**
     * Do we render one PDF with coupons? Or are they all generated seperately?
     * @var bool
     */
    public $generatesinglepdfs = false;

    /**
     * CSV string indicating the recipients for personalized coupons
     * @var string
     */
    public $csvrecipients;

    /**
     * coupon logo ID (0 indicates default, all other values refer to file IDs)
     * @var int
     */
    public $logoid = 0;

    /**
     * Render QR code?
     * @var int
     */
    public $renderqrcode = true;

    /**
     * coupon extend targets (only applicable when using extend enrolment type coupon)
     * @var array
     */
    public $extendusers;

    /**
     * coupon role id
     * @var int
     */
    public $roleid;

    /**
     * coupon batch id
     * @var string
     */
    public $batchid;

    /**
     * Only generate codes?
     * @var bool
     */
    public $generatecodesonly = false;

    /**
     * What generator method do we use?
     * @var string
     */
    public $generatormethod = 'amount';

    /**
     * CSV Delimiter if applicable
     * @var string
     */
    public $csvdelimitername = ',';

    /**
     * create a new instance
     */
    public function __construct() {
        $this->codesize = get_config('block_coupon', 'coupon_code_length');
        if (!$this->codesize) {
            $this->codesize = 16;
        }
        $this->batchid = md5(uniqid((string)microtime(true), true));
    }

    /**
     * Serialize options to session.
     */
    public function to_session() {
        global $SESSION;
        $SESSION->generatoroptions = json_decode(json_encode($this));
    }

    /**
     * Load generatoroptions from session
     * @return \self
     */
    public static function from_session() {
        global $SESSION;
        $generatoroptions = new self();
        if (isset($SESSION->generatoroptions)) {
            $options = $SESSION->generatoroptions;
            foreach ($options as $key => $value) {
                $generatoroptions->{$key} = $value;
            }
        }
        return $generatoroptions;
    }

    /**
     * Clean generatoroptions from session
     */
    public static function clean_session() {
        global $SESSION;
        if (isset($SESSION->generatoroptions)) {
            unset($SESSION->generatoroptions);
        }
    }

    /**
     * Validate if we have generatoroptions in session
     */
    public static function validate_session() {
        global $SESSION;
        if (!isset($SESSION->generatoroptions)) {
            throw new \moodle_exception("error:sessions-expired", 'block_coupon');
        }
    }

}
