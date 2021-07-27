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
 * Logo storage helper
 *
 * File         logostorage.php
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
 * block_coupon\logostorage
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logostorage {

    /**
     * @var string
     */
    const FILE_AREA = 'logos';
    /**
     * @var int
     */
    const AREA_ITEM_ID = 1;

    /**
     * Add the form elements.
     *
     * @param \HTML_QuickForm $mform
     */
    public static function add_form_elements(&$mform) {
        $mform->addElement('static', '_logoman', '', get_string('logomanager:desc', 'block_coupon'));

        $maxbytes = 10 * 1024 * 1024;
        $options = array(
            'subdirs' => 0,
            'maxbytes' => $maxbytes,
            'areamaxbytes' => 10485760,
            'maxfiles' => 10,
            'accepted_types' => array('image'),
        );
        $mform->addElement('filemanager', 'logos', '', null, $options);

        $draftid = file_get_submitted_draft_itemid('logos');
        self::prepare_draftarea($draftid);
        $mform->setDefault('logos', $draftid);
    }

    /**
     * Add select form elements.
     *
     * @param \HTML_QuickForm $mform
     */
    public static function add_select_form_elements(&$mform) {
        $mform->addElement('static', '_logo', '', get_string('select:logo:desc', 'block_coupon'));
        $mform->addElement('select', 'logo', get_string('select:logo', 'block_coupon'), static::get_file_menu());
    }

    /**
     * Prepare the draft area.
     *
     * @param int $draftitemid
     * @return int
     */
    public static function prepare_draftarea(&$draftitemid) {
        $context = \context_system::instance();
        $options = array('subdirs' => 0, 'maxfiles' => 10);
        file_prepare_draft_area($draftitemid, $context->id, 'block_coupon',
                self::FILE_AREA, self::AREA_ITEM_ID, $options);
        return $draftitemid;
    }

    /**
     * Store draft items.
     *
     * @param string $draftitemid
     */
    public static function store_draft_files($draftitemid) {
        $context = \context_system::instance();
        $options = array('subdirs' => 0, 'maxfiles' => 10);
        $text = null;
        return file_save_draft_area_files($draftitemid, $context->id, 'block_coupon',
                self::FILE_AREA, self::AREA_ITEM_ID, $options, $text);
    }

    /**
     * Store item from content.
     *
     * @param string $filename
     * @param string $content
     * @return \stored_file stored file
     */
    public static function store_from_content($filename, $content) {
        global $USER;
        $context = \context_system::instance();

        $filerecord = new \stdClass();
        $filerecord->contextid = $context->id;
        $filerecord->component = 'block_coupon';
        $filerecord->filearea = 'logos';
        $filerecord->itemid = '1';
        $filerecord->filepath = '/';
        $filerecord->filename = $filename;
        $filerecord->source = $filename;
        $filerecord->userid = $USER->id;
        $filerecord->author = fullname($USER);
        $filerecord->license = 'allrightsreserved';

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $content);
        return $file;
    }

    /**
     * Get stored files.
     *
     * @return \stored_file[]
     */
    public static function get_files() {
        $fs = get_file_storage();
        $context = \context_system::instance();
        if (!$files = $fs->get_area_files($context->id, 'block_coupon', self::FILE_AREA, self::AREA_ITEM_ID, 'id DESC', false)) {
            return false;
        }
        return $files;
    }

    /**
     * Get stored files menu.
     * This will include the default coupon image.
     *
     * @return \stored_file[]
     */
    public static function get_file_menu() {
        $fs = get_file_storage();
        $context = \context_system::instance();
        $rs = array(
            -1 => get_string('logo:none', 'block_coupon'),
            0 => get_string('logo:default', 'block_coupon')
        );
        if (!$files = $fs->get_area_files($context->id, 'block_coupon', self::FILE_AREA, self::AREA_ITEM_ID, 'id DESC', false)) {
            return $rs;
        }
        foreach ($files as $file) {
            $rs[$file->get_id()] = $file->get_filepath() . $file->get_filename();
        }
        return $rs;
    }

    /**
     * Get stored files table.
     *
     * @return \stored_file[]
     */
    public static function get_file_table() {
        $fs = get_file_storage();
        $context = \context_system::instance();
        $rs = array();
        if (!$files = $fs->get_area_files($context->id, 'block_coupon', self::FILE_AREA, self::AREA_ITEM_ID, 'id DESC', false)) {
            return $rs;
        }
        $table = new \html_table();
        $table->head = array(
            'id',
            'name',
            'path',
            'size',
            'author',
            '',
        );
        foreach ($files as $file) {
            $table->data[] = array(
                $file->get_id(),
                $file->get_filename(),
                $file->get_filepath(),
                $file->get_filesize(),
                $file->get_author(),
                \html_writer::img(\moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        $file->get_itemid(),
                        $file->get_filepath(),
                        $file->get_filename()
                    ), $file->get_filename()
                )
            );
        }
        return $table;
    }

    /**
     * Get a tempfile instance for the given file ID
     *
     * @param int $fileid
     * @return \block_coupon\tempfile
     */
    public static function get_tempfile_for($fileid) {
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($fileid);
        $fn = $file->get_filename();
        $parts = explode('.', $fn);
        $tempfile = \block_coupon\tempfile::create_from_content($file->get_content(), end($parts));
        return $tempfile;
    }

    /**
     * Get a file instance for the given file ID
     *
     * @param int $fileid
     * @return \stored_file
     */
    public static function get_file($fileid) {
        $fs = get_file_storage();
        return $fs->get_file_by_id($fileid);
    }

}
