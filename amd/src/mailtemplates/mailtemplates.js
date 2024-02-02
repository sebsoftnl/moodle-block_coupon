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

import $ from 'jquery';
import * as Str from 'core/str';
import Notification from 'core/notification';
import * as Service from 'block_coupon/mailtemplates/service';
import * as DynamicTable from 'core_table/dynamic';

/**
 * Duplicate template
 * @param {Event} e
 */
const duplicateTemplate = function(e) {
    e.preventDefault();
    const templateId = $(e.currentTarget).attr('data-id');
    Str.get_strings([
        {key: 'confirm', component: 'moodle'},
        {key: 'duplicatetemplateconfirm', component: 'block_coupon', param: $(e.currentTarget).attr('data-name')},
        {key: 'duplicate', component: 'block_coupon'},
        {key: 'cancel', component: 'moodle'}
    ]).done(function(s) {
        Notification.confirm(s[0], s[1], s[2], s[3], function() {
            Service.duplicateTemplate(templateId).then((result) => {
                if (result.result) {
                    let dt = $(e.currentTarget).closest('.table-dynamic');
                    if (dt.length) {
                        DynamicTable.refreshTableContent(DynamicTable.getTableFromId(dt.data('tableUniqueid')));
                    }

                }
            });
        });
    }).fail(Notification.exception);
};

/**
 * Delete template
 * @param {Event} e
 */
const deleteTemplate = function(e) {
    e.preventDefault();
    const templateId = $(e.currentTarget).attr('data-id');
    Str.get_strings([
        {key: 'confirm', component: 'moodle'},
        {key: 'deletetemplateconfirm', component: 'block_coupon', param: $(e.currentTarget).attr('data-name')},
        {key: 'delete', component: 'moodle'},
        {key: 'cancel', component: 'moodle'}
    ]).done(function(s) {
        Notification.confirm(s[0], s[1], s[2], s[3], function() {
            Service.deleteTemplate(templateId).then((result) => {
                if (result.result) {
                    let dt = $(e.currentTarget).closest('.table-dynamic');
                    if (dt.length) {
                        DynamicTable.refreshTableContent(DynamicTable.getTableFromId(dt.data('tableUniqueid')));
                    }
                }
            });
        });
    }).fail(Notification.exception);
};

export default {
    init: (selector) => {
        $(selector).on('click', '[data-action="delete"]', deleteTemplate);
        $(selector).on('click', '[data-action="duplicate"]', duplicateTemplate);
    }
};
