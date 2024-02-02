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
 * Preview modal implementation.
 *
 * @copyright  2019 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export default {
    init: function(formselector) {
        const form = document.querySelector(formselector);
        $(formselector).on('click', '[name="submitbutton"]', () => {
            form.querySelector('.processing.hidden').classList.remove('hidden');
            let elements = form.querySelectorAll('input[type="submit"]');
            for (let el of elements) {
                // We cannot set "disabled" bevause it will prevent submit() to work.
                el.classList.add('hidden');
            }
        });
    }
};
