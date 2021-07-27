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

defined('MOODLE_INTERNAL') || die();

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
     * @param array $exclude characters to exclude
     * @return string guaranteed unique coupon code
     */
    public static function generate_unique_code($size, $flags = self::ALL, $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
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
     * @param array $exclude characters to exclude
     */
    private static function generate_code($size, $flags = self::ALL, $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
        return static::get_token($size, $flags, $exclude);
    }

    /**
     * check whether or not a specified flag is active in the given flags
     *
     * @param int $value combined flags value
     * @param int $flag specific flag
     * @return bool
     */
    private static function is_flag($value, $flag) {
        return (($value & $flag) === $flag);
    }

    /**
     * Calculate max number of unique codes we have left
     *
     * @param int $size code size
     * @param int $flags generator flags
     * @param array $exclude characters to exclude
     * @return array [maximum, have]
     */
    public static function calc_max_codes_for_size($size, $flags = self::ALL,
            $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
        global $DB;

        $have = $DB->count_records_sql('SELECT COUNT(1) FROM {block_coupon} WHERE LENGTH(submission_code) = ?', [$size]);

        $charstr = static::get_char_str($flags, $exclude);
        $max = pow(strlen($charstr), $size);
        // Because we do NOT support numeric values as first character, take this into account.
        $chars = '0123456789';
        $max -= count(array_diff(str_split($chars), $exclude));

        return [$max, $have];
    }

    /**
     * Fetch character string ti use.
     *
     * @param int $flags generator flags
     * @param array $exclude characters to exclude
     */
    private static function get_char_str($flags = self::ALL,
            $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
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

        return implode('', array_diff(str_split($chars), $exclude));
    }

    /**
     * Generate crypto (original).
     * Nasty mt_rand usage which is OLD and crappy.
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    protected static function crypto_mt_rand($min, $max) {
        static $seeded;
        if (empty($seeded)) {
            mt_srand();
            $seeded = true;
        }
        return mt_rand($min, $max);
    }

    /**
     * Generate crypto.
     *
     * @see https://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    protected static function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 1) {
            return $min; // Not so random...
        }
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // Length in bytes.
        $bits = (int) $log + 1; // Length in bits.
        $filter = (int) (1 << $bits) - 1; // Set all lower bits to 1.
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // Discard irrelevant bits.
        } while ($rnd > $range);
        return $min + $rnd;
    }

    /**
     * get token.
     *
     * @see https://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string
     *
     * @param int $size code size
     * @param int $flags generator flags
     * @param array $exclude characters to exclude
     * @return string
     */
    protected static function get_token($size, $flags = self::ALL, $exclude = array('i', 'I', 'l', 'L', 1, 0, 'o', 'O')) {
        $codealphabet = static::get_char_str($flags, $exclude);
        $token = '';
        $max = strlen($codealphabet);
        $func = '';
        if (function_exists('random_int')) {
            $func = 'random_int';
        } else if (function_exists('openssl_random_pseudo_bytes')) {
            $func = [static::class, 'crypto_rand_secure'];
        } else {
            // Original. This is the worst implementation.
            $func = [static::class, 'crypto_mt_rand'];
        }

        while (true && (strlen($token) < $size)) {
            $n = $func(0, $max - 1);
            $appendcharacter = $codealphabet[$n];
            if (strlen($token) == 0) {
                // Do not use number as first char.
                if (!is_numeric($appendcharacter)) {
                    $token .= $appendcharacter;
                }
            } else {
                $token .= $appendcharacter;
            }
        }

        return $token;
    }

}
