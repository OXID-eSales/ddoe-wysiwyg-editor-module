/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

import '../../scss/backend_editor.scss'

import { addMediaPlugin } from "./plugins/media-library.js";
import { addVideoResponsivePlugin } from "./plugins/video-responsive.js";
import { injectOxidBridge } from "./plugins/oxid-bridge.js";
import { configureLinkDialogModule } from "./plugins/link.js";
import { overrideEditorMethods} from "./plugins/custom-editor.js";
/* global DOMPurify */

function overrideTooltip() {
    var tooltipPlugin = $.fn.tooltip;

    $.fn.tooltip = function(options) {
        if (Object.prototype.toString.call(options) === '[object Object]') {
            options.container = '#ddoew';
        }

        return tooltipPlugin.call(this, options);
    };
}

function overrideCodeviewPurify(context, purifyConfig) {
    if (context?.modules?.codeview?.purify) {
        const originalPurify = context.modules.codeview.purify.bind(context.modules.codeview);
        context.modules.codeview.purify = (value) => DOMPurify.sanitize(originalPurify(value), purifyConfig);
    }
}

function fixDropdownToggle(context) {
    if (context?.layoutInfo?.toolbar) {
        context.layoutInfo.toolbar.find('.dropdown-toggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.bootstrap && window.bootstrap.Dropdown) {
                window.bootstrap.Dropdown.getOrCreateInstance(this).toggle();
            }
        });
    }
}

function encodeEmojisToHtmlEntities(html) {
    if (!html) return html;
    return html.replace(/[\u{10000}-\u{10FFFF}]/gu, function(char) {
        return '&#' + char.codePointAt(0) + ';';
    });
}

export async function initializeSummernote(element, options, purifyConfig = {}) {
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

    const mediaModule = await import(window.mediaLibraryUrl);
    if (top.basefrm) {
        mediaModule.preloadMediaUrls(top.basefrm.mediaUrls);
    }

    addMediaPlugin();
    addVideoResponsivePlugin();
    injectOxidBridge(mediaModule);
    configureLinkDialogModule();

    const settings = { ...defaultSettings, ...options };
    var summernote = element.summernote(settings);

    overrideEditorMethods();

    const context = element.data('summernote');
    overrideCodeviewPurify(context, purifyConfig);
    fixDropdownToggle(context);

    return summernote;
}

export function autoInitializeSummernote(options, purifyConfig = {}) {
    overrideTooltip();

    if (typeof $().summernote === 'function') {
        $('.ddoe-wysiwyg-editor > textarea').each(function () {
            var dataRte = $( this ).attr('data-rte');

            if (typeof dataRte === 'undefined') {
                $(this).attr('data-rte', '1');

                $(this).closest('td').find( '.messagebox' ).remove();

                var iHeight = $(this).height();

                initializeSummernote($(this), {
                    minHeight: iHeight,
                    lang: $(this).data('lang') == 'de' ? 'de-DE' : 'en-US',
                    defaultProtocol: $(this).data('ssl') == '1' ? 'https://' : 'http://',
                    mediaButtonClick: function (context) {
                        top.basefrm.OverlayInstance.showOverlay(context);
                    },
                    callbacks: {
                        onInit: function () {
                            $('img.dd-wysiwyg-media-image').each(function () {
                                let filepath = $(this).attr('data-filepath');
                                if (!filepath && top.basefrm) {
                                    const id = $(this).attr('data-id');
                                    filepath = top.basefrm.mediaUrls[id];
                                }
                                if (filepath) {
                                    $(this).attr('src', filepath);
                                }
                            });
                        }
                    },
                    ...options
                }, purifyConfig).then(($editor) => {
                    if ($editor.attr('disabled') === 'disabled') {
                        $editor.summernote('disable');
                    }
                });
            }
        });

        var $form = $('.ddoe-wysiwyg-editor').first().closest('form');

        $form.find('*[type="submit"]').first().on('click', function() {
            $('.ddoe-wysiwyg-editor > textarea', $form).each(function () {
                // todo: check why this activation/deactivation is needed
                var context = $( this ).data( 'summernote' );

                if(context.invoke('codeview.isActivated')) {
                    context.invoke( 'codeview.deactivate' );
                }
                context.invoke( 'codeview.activate' );

                var content = DOMPurify.sanitize($( this ).summernote('code'), purifyConfig);
                $( this ).val(encodeEmojisToHtmlEntities(content));
            });
        });
    }
}
