/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
import '../../scss/overlay.scss'

class Overlay {
    static VERSION = '1.0.0';

    $overlay = null;
    overlayContext = null;

    constructor() {
        this.loadStyles();
        this.loadOverlay();
        this.setEvents();
    }

    loadStyles() {
        const cssPath = `${top.basefrm.OverlayStyles}`;
        if (!document.querySelector(`link[href="${cssPath}"]`)) {
            const cssLink = document.createElement('link');
            cssLink.rel = 'stylesheet';
            cssLink.href = cssPath;
            document.head.appendChild(cssLink);
        }

        const fontUrl = 'https://fonts.googleapis.com/css?family=Open+Sans';
        if (!document.querySelector(`link[href="${fontUrl}"]`)) {
            const fontLink = document.createElement('link');
            fontLink.rel = 'stylesheet';
            fontLink.href = fontUrl;
            document.head.appendChild(fontLink);
        }
    }

    loadOverlay() {
        const overlayHTML = `
            <div class="dd-backend-overlay">
                <div class="dd-overlay-backdrop"></div>
                <div class="dd-overlay-dialog">
                    <div class="dd-overlay-dialog-header">
                        ${this.translate("DD_MEDIA_DIALOG")}
                        <a href="javascript:void(0);" class="dd-overlay-dialog-close">&times;</a>
                    </div>
                    <div class="dd-overlay-dialog-body">
                        <iframe src="" id="overlayFrame" frameborder="0" style="width: 100%; height: 100%;"></iframe>
                    </div>
                    <div class="dd-overlay-dialog-footer">
                        <button type="button" class="dd-overlay-dialog-button dd-overlay-dialog-cancel">
                            ${this.translate("DD_CANCEL")}
                        </button>
                    </div>
                </div>
            </div>`;

        this.$overlay = $(overlayHTML);
        $('html').append(this.$overlay);
    }

    setEvents() {
        var self = this;

        $('.dd-overlay-dialog-close, .dd-overlay-dialog-cancel', this.$overlay).on('click', function (e) {
            e.preventDefault();
            self.hideOverlay();
        });

    };

    onContentLoad(callback) {
        if (typeof callback === 'function') {
            callback.call(this);
        }
    };

    showOverlay(context) {
        this.overlayContext = context;

        if (top.basefrm.editorControllerUrl) {
            $('#overlayFrame', this.$overlay).attr('src', top.basefrm.editorControllerUrl);
            $('#overlayFrame', this.$overlay).data('context', context);
        }

        this.$overlay.show();
    };

    hideOverlay() {
        this.$overlay.hide();

        $('#overlayFrame', this.$overlay).attr('src', '');
        $('.dd-overlay-dialog-footer > *', this.$overlay).not('.dd-overlay-dialog-cancel').remove();
    };

    translate(string) {
        return typeof i18n === 'object' && i18n[string] ? i18n[string] : string;
    }
}

// Export as an ES module
export const OverlayInstance = new Overlay();
