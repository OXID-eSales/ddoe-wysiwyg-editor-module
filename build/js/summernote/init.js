/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

import '../../less/backend_editor.less'

import { addMediaPlugin } from "./plugins/media-library.js";
import { addVideoResponsivePlugin } from "./plugins/video-responsive.js";
import { injectOxidBridge } from "./plugins/oxid-bridge.js";
import { configureLinkDialogModule, replaceLinkDialogModule } from "./plugins/link.js";

function overrideTooltip() {
    var tooltipPlugin = $.fn.tooltip;

    $.fn.tooltip = function(options) {
        if (Object.prototype.toString.call(options) === '[object Object]') {
            options.container = '#ddoew';
        }

        return tooltipPlugin.call(this, options);
    };
}

export function initializeSummernote(element, options) {
    const defaultSettings = {
        lang: 'de-DE',
        minHeight: 100,

        toolbar: [
            ['style', ['style']],
            ['formatting', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['layout', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'videoResponsive', 'hr']],
            ['misc', ['codeview']]
        ],

        dialogsInBody: false,

        buttons: {
            ddmedia: 'ddmedia'
        },

        disableDragAndDrop: true,
        codeviewFilter: true,
        codeviewIframeFilter: true,

        useProtocol: false,
    };

    addMediaPlugin();
    addVideoResponsivePlugin();
    injectOxidBridge();
    configureLinkDialogModule();

    const settings = { ...defaultSettings, ...options };
    var summernote = element.summernote(settings);
    replaceLinkDialogModule(summernote);
    return summernote;
}

export function autoInitializeSummernote() {
    overrideTooltip();

    if (typeof $().summernote === 'function') {
        $('.ddoe-wysiwyg-editor > textarea').each(function () {
            var dataRte = $( this ).attr('data-rte');

            if (typeof dataRte === 'undefined') {
                $(this).attr('data-rte', '1');

                $(this).closest('td').find( '.messagebox' ).remove();

                var iHeight = $(this).height();

                var $editor = initializeSummernote($(this), {
                    minHeight: iHeight,
                    lang: $(this).data('lang') == 'de' ? 'de-DE' : 'en-US',
                    defaultProtocol: $(this).data('ssl') == '1' ? 'https://' : 'http://'
                });

                if ('disabled' === $(this).attr('disabled')) {
                    $(this).summernote('disable');
                }

                var editorContext = $editor.data( 'summernote' );
                editorContext.invoke('codeview.activate');
                editorContext.invoke('codeview.deactivate');
            }
        });

        var $form = $('.ddoe-wysiwyg-editor').first().closest('form');

        $form.find('*[type="submit"]').first().on('click', function() {
            $('.ddoe-wysiwyg-editor > textarea', $form).each(function () {
                var context = $( this ).data( 'summernote' );

                // deactivate codeview before getting value
                if(context.invoke('codeview.isActivated')) {
                    context.invoke( 'codeview.deactivate' );
                }
                context.invoke( 'codeview.activate' );
            });
        });
    }
}
