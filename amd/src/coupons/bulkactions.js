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
import * as Notification from 'core/notification';
import * as Str from 'core/str';
import {add as addToast} from 'core/toast';
import {Service} from 'block_coupon/coupons/service';
import ModalForm from 'core_form/modalform';

const SELECTORS = {
    checkbox: '[data-action="bulk"]',
    checkboxselectall: '[data-action="bulkcheckall"]',
    bulkactionselect: '[data-type="bulkaction"]',
    bulkcounter: '#bulk-counter',
    bulkactions: {
        container: '[data-region="bulkactions"]',
        "delete": '[data-action="bulkdelete"]',
        editcourses: '[data-action="editcourses"]',
        editcohorts: '[data-action="editcohorts"]',
    },
    massactions: {
        container: '[data-region="massactions"]',
        replacecohorts: '[data-action="replacecohorts"]',
        replacecourses: '[data-action="replacecourses"]',
    },
    actions: {
        editcoupon: '[data-action="editcoupon"]',
    }
};

const enableBulkActions = (enable) => {
    if (enable) {
        document.querySelector(SELECTORS.bulkactionselect).removeAttribute('disabled');
    } else {
        document.querySelector(SELECTORS.bulkactionselect).setAttribute('disabled', 'disabled');
    }
};

const handleCheckboxChange = () => {
    // Initialize dropdown state.
    let size = $(`${SELECTORS.checkbox}:checked`).length;
    $(SELECTORS.bulkcounter).html(`${size}`);
    enableBulkActions(size > 0);
    if (size > 0) {
        // Enable/disable cohort/course types.
        let types = getSelectedTypes();
        document.querySelector(`${SELECTORS.bulkactions.container} ${SELECTORS.bulkactions.editcourses}`)
                .classList.remove('hidden');
        document.querySelector(`${SELECTORS.bulkactions.container} ${SELECTORS.bulkactions.editcohorts}`)
                .classList.add('hidden');

        if (types.length > 1) {
            // Multiple types: disable all.
            document.querySelector(`${SELECTORS.bulkactions.container} ${SELECTORS.bulkactions.editcourses}`)
                    .classList.add('hidden');
            document.querySelector(`${SELECTORS.bulkactions.container} ${SELECTORS.bulkactions.editcohorts}`)
                    .classList.add('hidden');
        } else {
            let typ = types[0];
            if (typ === 'cohort') {
                // Enable cohort type.
                document.querySelector(`${SELECTORS.bulkactions.container} ${SELECTORS.bulkactions.editcohorts}`)
                        .classList.remove('hidden');
            }
            if (typ === 'course') {
                // Enable course type.
                document.querySelector(`${SELECTORS.bulkactions.container} ${SELECTORS.bulkactions.editcourses}`)
                        .classList.remove('hidden');
            }
        }
    }
};

const getSelectedIds = () => {
    let boxes = $(`${SELECTORS.checkbox}:checked`);
    let ids = [];
    boxes.each((idx, el) => {
        ids.push(el.dataset.id);
    });
    return ids;
};

const getSelectedTypes = () => {
    let boxes = $(`${SELECTORS.checkbox}:checked`);
    let typs = [];
    boxes.each((idx, el) => {
        typs.push(el.dataset.typ);
    });
    return Array.from(new Set(typs));
};

const checkTypes = async() => {
    let rs = getSelectedTypes();
    if (rs.length > 1) {
        let args = await Str.get_strings([
            {key: 'err:bulkaction:typ:diff:title', component: 'block_coupon'},
            {key: 'err:bulkaction:typ:diff:msg', component: 'block_coupon'},
            {key: 'ok'},
        ]);
        Notification.alert(...args);
        return false;
    } else {
        return true;
    }
};

const bulkDelete = async(e) => {
    let rs = await checkTypes();
    if (rs) {
        // Display confirmation box.
        e.preventDefault();
        Str.get_strings([
            {key: 'confirm', component: 'moodle'},
            {key: 'deletecouponsconfirm', component: 'block_coupon'},
            {key: 'deletecoupons', component: 'block_coupon'},
            {key: 'cancel', component: 'moodle'}
        ]).done(function(s) {
            Notification.confirm(s[0], s[1], s[2], s[3], async () => {
                let ids = getSelectedIds();
                let rs = await Service.deleteCoupons(ids);
                if (rs.result) {
                    addToast(rs.msg, {type: 'success'});
                    // Sleep 3/4 sec before reloading page.
                    setTimeout(() => {window.location.reload();}, 750);
                } else {
                    addToast(rs.msg, {type: 'error'});
                }
            });
        }).fail(Notification.exception);
    }
};

const bulkEditCourses = async(e) => {
    let rs = await checkTypes();
    if (rs) {
        // Display dynamic form.
        e.preventDefault();

        let mfArgs = {};
        if (e.currentTarget.dataset.jArgs !== undefined) {
            let jArgs = e.currentTarget.dataset.jArgs;
            if (typeof jArgs === 'string') {
                jArgs = JSON.parse(jArgs);
            }
            for (const [key, value] of Object.entries(jArgs)) {
                mfArgs[key] = value;
            }
        }

        mfArgs.id = getSelectedIds();

        const modalForm = new ModalForm({
            formClass: 'block_coupon\\forms\\dynamic\\editcourses',
            modalConfig: {title: Str.get_string('editcourses', 'block_coupon')},
            args: mfArgs,
            returnFocus: e.target
        });

        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => window.location.reload());
        modalForm.show();
    }
};

const bulkEditCohorts = async(e) => {
    let rs = await checkTypes();
    if (rs) {
        // Display dynamic form.
        e.preventDefault();

        let mfArgs = {};
        if (e.currentTarget.dataset.jArgs !== undefined) {
            let jArgs = e.currentTarget.dataset.jArgs;
            if (typeof jArgs === 'string') {
                jArgs = JSON.parse(jArgs);
            }
            for (const [key, value] of Object.entries(jArgs)) {
                mfArgs[key] = value;
            }
        }

        mfArgs.id = getSelectedIds();

        const modalForm = new ModalForm({
            formClass: 'block_coupon\\forms\\dynamic\\editcohorts',
            modalConfig: {title: Str.get_string('editcohorts', 'block_coupon')},
            args: mfArgs,
            returnFocus: e.target
        });

        // When table ID not provided, detect.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => window.location.reload());
        modalForm.show();
    }
};

const bulkSelectAll = (e) => {
    let state = e.currentTarget.checked;
    let els = document.querySelectorAll(SELECTORS.checkbox);
    [...els].forEach( (cb) => {cb.checked = state;});
    handleCheckboxChange();
};

const bulkReplaceCohorts = async(e) => {
    e.preventDefault();

    let mfArgs = {};
    if (e.currentTarget.dataset.jArgs !== undefined) {
        let jArgs = e.currentTarget.dataset.jArgs;
        if (typeof jArgs === 'string') {
            jArgs = JSON.parse(jArgs);
        }
        for (const [key, value] of Object.entries(jArgs)) {
            mfArgs[key] = value;
        }
    }

    const modalForm = new ModalForm({
        formClass: 'block_coupon\\forms\\dynamic\\replacecohorts',
        modalConfig: {title: Str.get_string('replacecohorts', 'block_coupon')},
        args: mfArgs,
        returnFocus: e.target
    });

    // When table ID not provided, detect.
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => window.location.reload());
    modalForm.show();
};

const bulkReplaceCourses = async(e) => {
    e.preventDefault();

    let mfArgs = {};
    if (e.currentTarget.dataset.jArgs !== undefined) {
        let jArgs = e.currentTarget.dataset.jArgs;
        if (typeof jArgs === 'string') {
            jArgs = JSON.parse(jArgs);
        }
        for (const [key, value] of Object.entries(jArgs)) {
            mfArgs[key] = value;
        }
    }

    const modalForm = new ModalForm({
        formClass: 'block_coupon\\forms\\dynamic\\replacecourses',
        modalConfig: {title: Str.get_string('replacecourses', 'block_coupon')},
        args: mfArgs,
        returnFocus: e.target
    });

    // When table ID not provided, detect.
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => window.location.reload());
    modalForm.show();
};

const editCoupon = (e) => {
    e.preventDefault();

    let mfArgs = {
        id: e.currentTarget.dataset.id,
        typ: e.currentTarget.dataset.typ,
    };
    let formClass = '';
    switch (e.currentTarget.dataset.typ) {
        case 'course':
            formClass = 'block_coupon\\forms\\dynamic\\editcoursecoupon';
            break;
        case 'cohort':
            formClass = 'block_coupon\\forms\\dynamic\\editcohortcoupon';
            break;
    }
    if (e.currentTarget.dataset.jArgs !== undefined) {
        let jArgs = e.currentTarget.dataset.jArgs;
        if (typeof jArgs === 'string') {
            jArgs = JSON.parse(jArgs);
        }
        for (const [key, value] of Object.entries(jArgs)) {
            mfArgs[key] = value;
        }
    }

    const modalForm = new ModalForm({
        formClass,
        modalConfig: {title: Str.get_string('edit')},
        args: mfArgs,
        returnFocus: e.target
    });

    // When table ID not provided, detect.
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => window.location.reload());
    modalForm.show();
};

const initBulkActions = (selector) => {
    $(SELECTORS.checkboxselectall).on('change', bulkSelectAll);
    // Initialize dropdown state.
    enableBulkActions($(`${SELECTORS.checkbox}:checked`).length > 0);
    // Initialize checkbox state changes.
    $(SELECTORS.checkbox).on('change', handleCheckboxChange);
    // Initialize bulk actions.
    $(SELECTORS.bulkactions.container).on('click', SELECTORS.bulkactions.delete, bulkDelete);
    $(SELECTORS.bulkactions.container).on('click', SELECTORS.bulkactions.editcourses, bulkEditCourses);
    $(SELECTORS.bulkactions.container).on('click', SELECTORS.bulkactions.editcohorts, bulkEditCohorts);
    $(SELECTORS.massactions.container).on('click', SELECTORS.massactions.replacecourses, bulkReplaceCourses);
    $(SELECTORS.massactions.container).on('click', SELECTORS.massactions.replacecohorts, bulkReplaceCohorts);

    $(selector).on('click', SELECTORS.actions.editcoupon, editCoupon);
};

export const init = (selector) => {
    initBulkActions(selector);
};
