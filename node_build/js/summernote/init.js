/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

import '../../scss/backend_editor.scss'

import { addMediaPlugin } from "./plugins/media-library.js";
import { addVideoResponsivePlugin } from "./plugins/video-responsive.js";
import { injectOxidBridge } from "./plugins/oxid-bridge.js";
import { anchorMediaUrlsInMarkup } from "./media-image-markup.js";
import { createNoteSyncModule } from "./plugins/note-sync.js";
import { configureLinkDialogModule } from "./plugins/link.js";
import { overrideEditorMethods} from "./plugins/custom-editor.js";
import DOMPurify from 'dompurify';

function overrideTooltip() {
    var tooltipPlugin = $.fn.tooltip;

    $.fn.tooltip = function(options) {
        if (Object.prototype.toString.call(options) === '[object Object]') {
            options.container = '#ddoew';
        }

        return tooltipPlugin.call(this, options);
    };
}

function overrideCodeviewPurify(context, sanitize) {
    if (context?.modules?.codeview?.purify) {
        const originalPurify = context.modules.codeview.purify.bind(context.modules.codeview);
        context.modules.codeview.purify = (value) => sanitize(originalPurify(value));
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
    const sanitize = (html) => DOMPurify.sanitize(html, purifyConfig);

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

    const settings = {
        ...defaultSettings,
        ...options,
        // the options are merged shallowly, so the remaining modules have to be carried over
        modules: {
            ...$.summernote.options.modules,
            autoSync: createNoteSyncModule((editorContent) => {
                let anchoredContent = anchorMediaUrlsInMarkup(editorContent);
                anchoredContent = sanitize(anchoredContent);
                return encodeEmojisToHtmlEntities(anchoredContent);
            }),
        },
    };

    element.val(sanitize(element.val()));
    var summernote = element.summernote(settings);

    overrideEditorMethods();

    const context = element.data('summernote');
    overrideCodeviewPurify(context, sanitize);
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
    }
}
