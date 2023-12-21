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

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Update element positions
 *
 * @param {Integer} templateid
 * @param {Array} positions - array of objects {id {Integer}, posx {Integer}, posy {Integer}}
 * @returns {updateElementPositions.promises}
 */
const updateElementPositions = (templateid, positions) => {
    var promises = Ajax.call([{
            methodname: 'block_coupon_update_element_positions',
            args: {tid: templateid, values: positions}
        }]);
    promises[0].fail(Notification.exception);
    return promises[0];
};

/**
 * Get element HTML
 *
 * @param {Integer} templateid
 * @param {Integer} elementid
 * @returns {getElementHTML.promises}
 */
const getElementHTML = (templateid, elementid) => {
    var promises = Ajax.call([{
            methodname: 'block_coupon_get_element_html',
            args: {templateid, elementid}
        }]);
    promises[0].fail(Notification.exception);
    return promises[0];
};

/**
 * Save element data.
 *
 * @param {Integer} templateid
 * @param {Integer} elementid
 * @param {Array} inputs (key value array)
 * @returns {saveElement.promises}
 */
const saveElement = (templateid, elementid, inputs) => {
    var promises = Ajax.call([{
            methodname: 'block_coupon_save_element',
            args: {templateid, elementid, values: inputs}
        }]);
    promises[0].fail(Notification.exception);
    return promises[0];
};

/**
 * Duplicate template
 *
 * @param {Integer} templateid
 * @returns {duplicateTemplate.promises}
 */
const duplicateTemplate = (templateid) => {
    var promises = Ajax.call([{
            methodname: 'block_coupon_duplicate_template',
            args: {id: templateid}
        }]);
    promises[0].fail(Notification.exception);
    return promises[0];
};

/**
 * Delete template
 *
 * @param {Integer} templateid
 * @returns {deleteTemplate.promises}
 */
const deleteTemplate = (templateid) => {
    var promises = Ajax.call([{
            methodname: 'block_coupon_delete_template',
            args: {id: templateid}
        }]);
    promises[0].fail(Notification.exception);
    return promises[0];
};

export default {
    duplicateTemplate,
    deleteTemplate,
    updateElementPositions,
    getElementHTML,
    saveElement
};
