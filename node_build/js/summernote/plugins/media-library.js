/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

function ddmedia(context) {
    var layoutInfo = context.layoutInfo;
    var $toolbar   = layoutInfo.toolbar;
    var lang       = context.options.langInfo;

    var ui = $.summernote.ui;

    this.initialize = function () {
        // create button
        var button = ui.button({
            contents: '<i class="fa fa-file-image-o fa-file-image"></i>',
            tooltip: lang.image.image,
            click: function () {
                if (typeof MediaLibrary === 'undefined') {
                    if (top.basefrm && top.basefrm.OverlayInstance) {
                        top.basefrm.OverlayInstance.showOverlay(context);
                    }
                } else {
                    MediaLibrary.open(/image\/.*/i, function (id, file, fullpath) {
                        context.invoke('editor.insertImage', fullpath, function ($image) {
                            top.basefrm.mediaUrls[id] = fullpath;
                            $image.css('max-width', '100%');
                            $image.attr('src', fullpath);
                            $image.attr('data-source', 'media');
                            $image.attr('data-id', id);
                            $image.addClass('dd-wysiwyg-media-image');
                        });
                    });
                }
            }
        });

        // generate jQuery element from button instance.
        this.$button = button.render();

        if($toolbar.find('.note-btn-group.note-insert').length) {
            $toolbar.find( '.note-btn-group.note-insert' ).append( this.$button );
        } else {
            $toolbar.append($('<div class="note-btn-group btn-group" />').append(this.$button));
        }
    };

    this.destroy = function () {
        this.$button.remove();
        this.$button = null;
    };
}

export function addMediaPlugin() {
    $.extend($.summernote.plugins, {
        ddmedia: ddmedia
    });
}
