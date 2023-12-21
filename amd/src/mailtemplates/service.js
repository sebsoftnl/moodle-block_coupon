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
 * Duplicate template
 *
 * @param {Integer} templateid
 * @returns {duplicateTemplate.promises}
 */
const duplicateTemplate = (templateid) => {
    var promises = Ajax.call([{
            methodname: 'block_coupon_duplicate_mailtemplate',
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
            methodname: 'block_coupon_delete_mailtemplate',
            args: {id: templateid}
        }]);
    promises[0].fail(Notification.exception);
    return promises[0];
};

export default {
    duplicateTemplate,
    deleteTemplate
};
