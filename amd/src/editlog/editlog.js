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
import * as DynamicTable from 'core_table/dynamic';
import ModalForm from 'core_form/modalform';
import {add as addToast} from 'core/toast';

const revertProc = (e) => {
    e.preventDefault();

    let mfArgs = {
        procid: e.currentTarget.dataset.procid
    };
    if (e.currentTarget.dataset.jArgs !== undefined) {
        let jArgs = e.currentTarget.dataset.jArgs;
        if (typeof jArgs === 'string') {
            jArgs = JSON.parse(jArgs);
        }
        for (const [key, value] of Object.entries(jArgs)) {
            mfArgs[key] = value;
        }
    }

    const revertType = e.currentTarget.dataset.type;
    let formClass = '', title = '';
    if (revertType === 'course') {
        formClass = 'block_coupon\\forms\\dynamic\\revertcoursemod';
        title = Str.get_string('revertcoursemod', 'block_coupon');
    } else if (revertType === 'cohort') {
        formClass = 'block_coupon\\forms\\dynamic\\revertcohortmod';
        title = Str.get_string('revertcohortmod', 'block_coupon');
    }

    const modalForm = new ModalForm({
        formClass,
        modalConfig: {title},
        args: mfArgs,
        returnFocus: e.target
    });


    // When table ID not provided, detect.
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (result) => {
        if (result.detail.result) {
            let dt = e.currentTarget.closest('.table-dynamic');
            if (dt.dataset.tableUniqueid !== undefined) {
                DynamicTable.refreshTableContent(DynamicTable.getTableFromId(dt.dataset.tableUniqueid));
            }
            if (result.detail.notifications.length > 0) {
                result.detail.notifications.forEach( m => addToast(m.msg, {type:m.type}) );
            }
        }
    });
    modalForm.show();
};

const revertMod = (e) => {
    e.preventDefault();

    let mfArgs = {
        modid: e.currentTarget.dataset.modid
    };
    if (e.currentTarget.dataset.jArgs !== undefined) {
        let jArgs = e.currentTarget.dataset.jArgs;
        if (typeof jArgs === 'string') {
            jArgs = JSON.parse(jArgs);
        }
        for (const [key, value] of Object.entries(jArgs)) {
            mfArgs[key] = value;
        }
    }

    const revertType = e.currentTarget.dataset.type;
    let formClass = '', title = '';
    if (revertType === 'course') {
        formClass = 'block_coupon\\forms\\dynamic\\revertcoursemod';
        title = Str.get_string('revertcoursemod', 'block_coupon');
    } else if (revertType === 'cohort') {
        formClass = 'block_coupon\\forms\\dynamic\\revertcohortmod';
        title = Str.get_string('revertcohortmod', 'block_coupon');
    }

    const modalForm = new ModalForm({
        formClass,
        modalConfig: {title},
        args: mfArgs,
        returnFocus: e.target
    });

    // When table ID not provided, detect.
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (result) => {
        if (result.detail.result) {
            let dt = e.currentTarget.closest('.table-dynamic');
            if (dt.dataset.tableUniqueid !== undefined) {
                DynamicTable.refreshTableContent(DynamicTable.getTableFromId(dt.dataset.tableUniqueid));
            }
            if (result.detail.notifications.length > 0) {
                result.detail.notifications.forEach( m => addToast(m.msg, {type:m.type}) );
            }
        }
    });
    modalForm.show();
};

export default {
    init: (selector) => {
        $(selector).on('click', '[data-action="revertproc"]', revertProc);
        $(selector).on('click', '[data-action="revertmod"]', revertMod);
    }
};
