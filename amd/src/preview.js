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
import * as Str from 'core/str';
import * as Notification from 'core/notification';

/**
 * Encode uri parameters.
 *
 * @param {Object} data
 * @return {String}
 */
const encodeQueryParams = function (data) {
    const ret = [];
    for (let d in data) {
        ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
    }
    return ret.join('&');
};

class CouponPreview {
    /**
     * RearrangeArea class.
     *
     * @param {String} selector
     * @param {string} url
     */
    constructor(selector, url) {
        this.selector = selector;
        this.url = url;
        Str.get_string('preview-pdf', 'block_coupon').then(function(title) {
            this.title = title;
            this.setupHandlers();
        }.bind(this)).fail(Notification.exception);
    }

    /**
     * @var {string} modal title
     */
    title = null;

    /**
     * @var {string} preview loader url
     */
    url = null;

    /**
     * @var {Object} modal
     */
    modal = null;

    /**
     * @var {string} click selector opening the modal
     */
    selector = null;

    /**
     * Close handler
     */
    closeHandler() {
        this.destroy();
    }

    /**
     * Out of bounds click handler handler
     * @param {Event} e
     */
    clickOutHandler(e) {
        if ($(e.target) == this.modal) {
            this.destroy();
        }
    }

    /**
     * Handler to setup the modal
     * @param {Event} e
     */
    setupHandler(e) {
        var url = this.url, extraparams = {};
        var el = e.currentTarget;
        if (el.dataset.templated) {
            extraparams.templateid = el.dataset.templateid;
        } else {
            extraparams.font = el.dataset.font;
            extraparams.logoid = el.dataset.logo;
            extraparams.qr = el.dataset.qr;
        }

        if (Object.keys(extraparams).length) {
            if (url.indexOf('?') >= 0) {
                url += '&' + encodeQueryParams(extraparams);
            } else {
                url += '?' + encodeQueryParams(extraparams);
            }
        }
        // Create container.
        var html = '<div id="block-coupon-modal"><div class="block-coupon-modal-content">';
        html += '<div class="block-coupon-modal-header">';
        html += '<span id="block-modal-close" class="close">&times;</span>';
        html += '<h2>' + this.title + '</h2>';
        html += '</div>';
        html += '<div class="block-coupon-modal-body">';
        html += '<iframe src="' + url + '"></iframe>';
        html += '</div>';
        html += '</div></div>';
        this.modal = $(html);
        $('body').append(this.modal);
        this.modal.hide();
        $('#block-modal-close').on('click', this.destroy.bind(this));
        $('body').on('click', this.clickOutHandler.bind(this));
        this.modal.show();
    }

    /**
     * Setup external handlers
     */
    setupHandlers() {
        $(this.selector).on('click', this.setupHandler.bind(this));
    }

    /**
     * Destroy the modal
     */
    destroy() {
        // Destroy some handlers.
        $('#block-modal-close').off('click');
        $('body').off('click');
        // Destroy modal container.
        this.modal.hide();
        this.modal.remove();
    }

}

export default {
    init: function(clickselector, url) {
        new CouponPreview(clickselector, url);
    }
};
