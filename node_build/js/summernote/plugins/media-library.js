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
                if (top.basefrm && top.basefrm.OverlayInstance) {
                    top.basefrm.OverlayInstance.showOverlay(context);
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
