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

const showHideElement = function(e) {
    e.preventDefault();
    const elementid = e.currentTarget.dataset.id;
    const target = document.getElementById(`element-${elementid}`);
    if (target === null) {
        return;
    }
    let visible = e.currentTarget.checked;

    if (visible) {
        target.style.display = null;
    } else {
        target.style.display = 'none';
    }
};

const showHideAll = function(e) {
    e.preventDefault();
    let els = $('[data-action=shel]');
    let visible = e.currentTarget.checked;

    els.each((idx, el) => {
        if (visible) {
            $(el).prop('checked', true);
        } else {
            $(el).prop('checked', false);
        }
        $(el).trigger('change');
    });
};

export default {
    init: (selector) => {
        $(selector).on('change', '[data-action=shel]', showHideElement);
        $(selector).on('change', '[data-action=shelall]', showHideAll);
    }
};
