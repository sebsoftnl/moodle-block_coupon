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
 * AMD module used when rearranging a custom certificate.
 *
 * @module     block_coupon/rearrange-area
 * @author     R.J. van Dongen
 * @copyright  2023 R.J. van Dongen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as jqui from 'jqueryui'; // eslint-disable-line no-unused-vars
import ModalForm from 'core_form/modalform';
import * as Service from 'block_coupon/templates/service';

class RearrangeArea {
    /**
     * RearrangeArea class.
     *
     * @param {String} selector The rearrange PDF selector
     */
    constructor(selector) {
        this._node = $(selector);
        this._setEvents();
    }

    _node = null;

    COUPON_REF_POINT_TOPLEFT = 0;
    COUPON_REF_POINT_TOPCENTER = 1;
    COUPON_REF_POINT_TOPRIGHT = 2;
    PIXELSINMM = 3.779527559055;

    _getElementHTML(elementid) {
        // Get the variables we need.
        const templateid = this._node.attr('data-templateid');
        // Call/return the web service to get the updated element.
        return Service.getElementHTML(templateid, elementid);
    }

    _saveElement(elementid) {
        // Get the variables we need.
        const templateid = this._node.attr('data-templateid');
        const inputs = $('#editelementform').serializeArray();
        // Call the web service to save the element.
        return Service.saveElement(templateid, elementid, inputs);
    }

    _setEvents() {
        this._node.on('click', '.element', this._editElement.bind(this));
    }

    async _editElement(event) {
        var elementid = event.currentTarget.dataset.id;

        const modalForm = new ModalForm({
            // Name of the class where form is defined (must extend \core_form\dynamic_form):
            formClass: "block_coupon\\template\\edit_element_dform",
            // Add as many arguments as you need, they will be passed to the form:
            args: {id: elementid},
            // Pass any configuration settings to the modal dialogue, for example, the title:
            modalConfig: {title: 'walla'},
            // DOM element that should get the focus after the modal dialogue is closed:
            //returnFocus: element,
        });
        // Listen to events if you want to execute something on form submit.
        // Event detail will contain everything the process() function returned:
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, this._modalAfterSave.bind(this));
        // Show the form.
        modalForm.show();
        // We may have dragged the element changing it's position.
        // Ensure the form has the current up-to-date location.
        //this._setPositionInForm(elementid);
    }

    _modalAfterSave(data) {
        let element = data.detail;
        this._getElementHTML(element.id).done(function (html) {
            var elementNode = this._node.find('#element-' + element.id);
            var refpoint = parseInt(element.refpoint);
            var refpointClass = '';
            if (refpoint == this.COUPON_REF_POINT_TOPLEFT) {
                refpointClass = 'refpoint-left';
            } else if (refpoint == this.COUPON_REF_POINT_TOPCENTER) {
                refpointClass = 'refpoint-center';
            } else if (refpoint == this.COUPON_REF_POINT_TOPRIGHT) {
                refpointClass = 'refpoint-right';
            }
            elementNode.empty().append(html);
            // Update the ref point.
            elementNode.removeClass();
            elementNode.addClass('element ' + refpointClass);
            elementNode.data('refpoint', refpoint);
            // Move the element.
            var posx = element.posx;
            var posy = element.posy;
            this._setPosition(element.id, refpoint, posx, posy);
        }.bind(this));
    }

    _setPosition(elementid, refpoint, posx, posy) {
        var element = $('#element-' + elementid);
        var pdf = $('#pdf');

        posx = pdf.offset().left + posx * this.PIXELSINMM;
        posy = pdf.offset().top + posy * this.PIXELSINMM;
        var nodewidth = parseFloat(element.width());
        var maxwidth = element.width() * this.PIXELSINMM;

        if (maxwidth && (nodewidth > maxwidth)) {
            nodewidth = maxwidth;
        }

        switch (refpoint) {
            case this.COUPON_REF_POINT_TOPCENTER:
                posx -= nodewidth / 2;
                break;
            case this.COUPON_REF_POINT_TOPRIGHT:
                posx = posx - nodewidth + 2;
                break;
        }

        element.offset({left: posx, top: posy});
    }

    //_setPositionInForm(elementid) {
    //    var posxelement = $('#editelementform #id_posx');
    //    var posyelement = $('#editelementform #id_posy');
    //
    //    if (posxelement.length && posyelement.length) {
    //        var element = $('#element-' + elementid);
    //        var posx = element.offset().left - $('#pdf').offset().left;
    //        var posy = element.offset().top - $('#pdf').offset().top;
    //        var refpoint = parseInt(element.data('refpoint'));
    //        var nodewidth = parseFloat(element.width());
    //
    //        switch (refpoint) {
    //            case this.COUPON_REF_POINT_TOPCENTER:
    //                posx += nodewidth / 2;
    //                break;
    //            case this.COUPON_REF_POINT_TOPRIGHT:
    //                posx += nodewidth;
    //                break;
    //        }
    //
    //        posx = Math.round(parseFloat(posx / this.PIXELSINMM));
    //        posy = Math.round(parseFloat(posy / this.PIXELSINMM));
    //
    //        posxelement.val(posx);
    //        posyelement.val(posy);
    //    }
    //}
}

class Rearrange {
    constructor(templateid, page, elements) {
        this.templateid = templateid;
        this.page = page;
        this.elements = elements;

        // Set the PDF dimensions.
        this.setPdfDimensions();

        // Set the boundaries.
        this.setBoundaries();
        this.setpositions();
        this.createevents();

        window.addEventListener("resize", this.checkWindownResize.bind(this));
    }

    /**
     * The template id.
     */
    templateid = 0;

    /**
     * The page we are displaying.
     */
    page = [];

    /**
     * The custom certificate elements to display.
     */
    elements = [];

    /**
     * Store the X coordinates of the top left of the pdf div.
     */
    pdfx = 0;

    /**
     * Store the Y coordinates of the top left of the pdf div.
     */
    pdfy = 0;

    /**
     * Store the width of the pdf div.
     */
    pdfwidth = 0;

    /**
     * Store the height of the pdf div.
     */
    pdfheight = 0;

    /**
     * Store the location of the element before we move.
     */
    elementxy = 0;

    /**
     * Store the left boundary of the pdf div.
     */
    pdfleftboundary = 0;

    /**
     * Store the right boundary of the pdf div.
     */
    pdfrightboundary = 0;

    /**
     * The number of pixels in a mm.
     */
    pixelsinmm = 3.779527559055; // 3.779528.

    /**
     * Sets the current position of the elements.
     */
    setpositions() {
        // Go through the elements and set their positions.
        for (var key in this.elements) {
            var element = this.elements[key];
            var el = document.querySelector('#element-' + element.id);
            if (el === undefined || el === null) {
                continue;
            }
            var posx = this.pdfx + element.posx * this.pixelsinmm;
            var posy = this.pdfy + element.posy * this.pixelsinmm;
            var cr = el.getBoundingClientRect();
            var nodewidth = cr.width;
            var maxwidth = element.width * this.pixelsinmm;

            if (maxwidth && (nodewidth > maxwidth)) {
                nodewidth = maxwidth;
            }

            switch (element.refpoint) {
                case '1': // Top-center.
                    posx -= nodewidth / 2;
                    break;
                case '2': // Top-right.
                    posx = posx - nodewidth + 2;
                    break;
            }

            $('#element-' + element.id).offset({left: posx, top: posy});
        }
    }

    /**
     * Sets the PDF dimensions.
     */
    setPdfDimensions() {
        const el = document.querySelector('#pdf');
        const cr = el.getBoundingClientRect();
        const offset = $(el).offset();
        this.pdfx = offset.left;
        this.pdfy = offset.top;
        this.pdfwidth = parseFloat(cr.width);
        this.pdfheight = parseFloat(cr.height);
    }

    /**
     * Sets the boundaries.
     */
    setBoundaries() {
        this.pdfleftboundary = this.pdfx;
        if (this.page.leftmargin) {
            this.pdfleftboundary += parseInt(this.page.leftmargin * this.pixelsinmm, 10);
        }

        this.pdfrightboundary = this.pdfx + this.pdfwidth;
        if (this.page.rightmargin) {
            this.pdfrightboundary -= parseInt(this.page.rightmargin * this.pixelsinmm, 10);
        }
    }

    /**
     * Check browser resize and reset position.
     */
    checkWindownResize() {
        this.setPdfDimensions();
        this.setBoundaries();
        this.setpositions();
    }

    /**
     * Creates the JS events for changing element positions.
     */
    createevents() {
        // For now we won't do realtime updates (service calls).
        const instantupdate = true;
        // Trigger a save event when save button is pushed.
        $('.savepositionsbtn [type=submit]').on('click', function(e) {
            e.preventDefault();
            this.savepositions(e).then(function() {
                var formnode = e.currentTarget.closest('form');
                var baseurl = formnode.getAttribute('action');
                var pageinput = formnode.querySelector('[name=pid]');
                if (pageinput) {
                    var pageid = pageinput.value;
                    window.location = baseurl + '?pid=' + pageid;
                } else {
                    var templateid = formnode.querySelector('[name=tid]').value;
                    window.location = baseurl + '?tid=' + templateid;
                }
            });
        }.bind(this));

        // Trigger a save event when apply button is pushed.
        $('.applypositionsbtn [type=submit]').on('click', function(e) {
            e.preventDefault();
            this.savepositions(e);
        }.bind(this));

        // Drag-drop.
        var selector = '#pdf .element';
        var pixelsinmm = this.pixelsinmm;

        var target = $(selector);
        target.draggable({
                    // Snap to elements only if Shift is not held.
                    //snap: e.shiftKey ? false : '.snapdraggable',
                    snap: false,
                    snapMode: 'inner',
                    snapTolerance: 10,
                    // Set containment so it can't be moved far away from the page outlines.
//                    containment: [
//                        page.offset().left - el.width(),
//                        page.offset().top - el.height(),
//                        page.offset().left + page.width(),
//                        page.offset().top + page.height()
//                    ],
                    containment: $('#pdf')
                });
        target.on('dragstart', function(e) {
                $(e.currentTarget).addClass('isdragged');
            })
            .on('dragstop', function(e) {
                var el = $(e.currentTarget),
                    page = el.closest('#pdf'),
                    refpoint = parseInt($(this).data('refpoint')),
                    offset = refpoint ? parseInt($(this).width()) * refpoint / 2 : 0,
                    left = (el.offset().left - page.offset().left + offset) / pixelsinmm,
                    top = (el.offset().top - page.offset().top) / pixelsinmm;
                setTimeout(function() {
                    el.removeClass('isdragged');
                }, 100);

                if (instantupdate) {
                    Service.updateElementPositions(
                        page.data('templateid'),
                        [{id: el.data('id'), posx: Math.round(parseFloat(left)), posy: Math.round(parseFloat(top))}]
                    );
                }
            });
    }

    /**
     * Returns true if any part of the element is placed outside of the PDF div, false otherwise.
     *
     * @param {Element} el
     * @returns {boolean}
     */
    isoutofbounds(el) {
        // Get the width and height of the node.
        var cr = el.getBoundingClientRect();
        var offset = $(el).offset();
        var nodewidth = parseFloat(cr.width);
        var nodeheight = parseFloat(cr.height);

        // Store the positions of each edge of the node.
        var left = offset.left;
        var right = left + nodewidth;
        var top = offset.top;
        var bottom = top + nodeheight;

        const pdf = document.querySelector('#pdf');
        const poffset = $(pdf).offset();
        this.pdfx = poffset.left;
        this.pdfy = poffset.top;

        // Check if it is out of bounds horizontally.
        if ((left < this.pdfleftboundary) || (right > this.pdfrightboundary)) {
            return true;
        }

        // Check if it is out of bounds vertically.
        if ((top < this.pdfy) || (bottom > (this.pdfy + this.pdfheight))) {
            return true;
        }

        return false;
    }

    /**
     * Perform an AJAX call and save the positions of the elements.
     */
    savepositions() {
        // The parameters to send the AJAX call.
        var values = [];

        const offset = $('#pdf').offset();
        this.pdfx = offset.left;
        this.pdfy = offset.top;

        // Go through the elements and save their positions.
        for (var key in this.elements) {
            var element = this.elements[key];
            var el = $('#element-' + element.id);
            if (el.length === 0) {
                continue;
            }
            var eloffset = el.offset();

            // Get the current X and Y positions and refpoint for this element.
            var posx = eloffset.left - this.pdfx;
            var posy = eloffset.top - this.pdfy;
            var refpoint = el.data('refpoint');

            var nodewidth = parseFloat(el.width());

            switch (refpoint) {
                case '1': // Top-center.
                    posx += nodewidth / 2;
                    break;
                case '2': // Top-right.
                    posx += nodewidth;
                    break;
            }

            // Set the parameters to pass to the AJAX request.
            values.push({
                id: element.id,
                posx: Math.round(parseFloat(posx / this.pixelsinmm)),
                posy: Math.round(parseFloat(posy / this.pixelsinmm))
            });
        }

        Service.updateElementPositions(this.templateid, values);
    }

}

export default {
    init: function(selector, templateid, page, elements) {
        new Rearrange(templateid, page, elements);
        new RearrangeArea(selector);
    }
};
