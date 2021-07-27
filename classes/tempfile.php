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
 * Temporary file implementation
 *
 * File         tempfile.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\tempfile
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tempfile {

    /**
     * Full path to created file
     * @var string
     */
    protected $filepath;

    /**
     * Get the filepath
     * @return string
     */
    public function get_filepath() {
        return $this->filepath;
    }

    /**
     * Set filepath to temp file
     *
     * @param string $filepath
     * @return \block_coupon\tempfile
     */
    public function set_filepath($filepath) {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * Get storage path
     *
     * @param string $relativefilename relative path
     * @return string
     */
    public static function get_storage_path($relativefilename = '') {
        global $CFG;
        $basedir = $CFG->tempdir . '/coupontmp';
        if (!check_dir_exists($basedir, true, true)) {
            mkdir($basedir, 0777);
        }
        if (!empty($relativefilename)) {
            $basedir .= '/' . $relativefilename;
        }
        return $basedir;
    }

    /**
     * Create a new temp file
     */
    public function __construct() {
    }

    /**
     * Create a new temp file from the given contents and extension
     *
     * @param string $filecontent
     * @param string $fileext
     */
    public static function create_from_content($filecontent, $fileext) {
        $basedir = self::get_storage_path();
        $filename = "content-" . random_string(8) . '.' . ltrim($fileext, '.');
        $filepath = $basedir . '/' . $filename;
        $fd = fopen($filepath, 'wb');
        fwrite($fd, $filecontent);
        fclose($fd);

        $self = new self();
        $self->filepath = $filepath;
        return $self;
    }

    /**
     * Create a new temp file from the given contents and extension
     *
     * @param string $filepath full path to file
     */
    public static function create_from_path($filepath) {
        $self = new self();
        $self->filepath = $filepath;
        return $self;
    }

    /**
     * Destroy instance. Will delete temp file if it still exists.
     */
    public function __destruct() {
        // Unlink file.
        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }
    }

}
