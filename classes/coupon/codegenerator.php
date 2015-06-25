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
 * Unique coupon code generator implementation
 *
 * File         generator.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace block_coupon\coupon;

/**
 * block_coupon\coupon\generator
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class codegenerator {

    /**
     * Generator flag for numeric characters
     */
    const NUMERIC = 1;
    /**
     * Generator flag for lowercase alphabet characters
     */
    const LETTERS = 2;
    /**
     * Generator flag for uppercase alphabet characters
     */
    const CAPITALS = 4;
    /**
     * Generator flag for alphanumeric characters
     */
    const ALNUM = 3;
    /**
     * Generator flag for all characters
     */
    const ALL = 7;

    /**
     * Generate a unique coupon code
     *
     * @param int $size code size
     * @param int $flags generator flags
     * @param array $exclude charachters to exclude
     * @return string guaranteed unique coupon code
     */
    static public function generate_unique_code($size, $flags = self::ALL, $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
        global $DB;

        $vcode = self::generate_code($size, $flags = self::ALL, $exclude);
        while ($DB->get_record('block_coupon', array('submission_code' => $vcode))) {
            $vcode = self::generate_code($size, $flags = self::ALL, $exclude);
        }

        return $vcode;
    }

    /**
     * Generate coupon code
     *
     * @param int $size code size
     * @param int $flags generator flags
     * @param array $exclude charachters to exclude
     */
    static private function generate_code($size, $flags = self::ALL, $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
        $chars = '';
        if (self::is_flag($flags, self::NUMERIC)) {
            $chars .= '0123456789';
        }
        if (self::is_flag($flags, self::LETTERS)) {
            $chars .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if (self::is_flag($flags, self::CAPITALS)) {
            $chars .= 'ABCDEFGHIJKLMNOPQRTSUVWXYZ';
        }

        $chars = implode('', array_diff(str_split($chars), $exclude));

        $code = '';
        $max = strlen($chars);
        while (true && (strlen($code) < $size)) {
            $n = rand(0, $max - 1);
            if (strlen($code) == 0) {
                // Do not use number as first char.
                if (!is_numeric($chars{$n})) {
                    $code .= $chars{$n};
                }
            } else {
                $code .= $chars{$n};
            }
        }

        return $code;
    }

    /**
     * check whether or not a specified flag is active in the given flags
     *
     * @param int $value combined flags value
     * @param int $flag specific flag
     * @return bool
     */
    static private function is_flag($value, $flag) {
        return (($value & $flag) === $flag);
    }

}