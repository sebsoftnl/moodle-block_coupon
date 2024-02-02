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

/**
 * block_coupon\coupon\generatoroptions
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdfoptions {

    /**
     * @var bool whether or not we create one single PDF file
     */
    public $createsinglepdf = false;

    /**
     * @var bool whether or not we use a template
     */
    public $usetemplate = false;

    /**
     * @var int|null template ID
     */
    public $usetemplateid = null;

    /**
     * coupon logo ID (0 indicates default, all other values refer to file IDs)
     * @var int
     */
    public $logoid = 0;

    /**
     * Font used for the PDF
     *
     * @var string
     */
    public $font = 'helvetica';

    /**
     * Render QR code?
     * @var int
     */
    public $renderqrcode = true;

}
