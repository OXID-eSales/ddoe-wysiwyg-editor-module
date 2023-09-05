/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

;( function( $ )
{
    $.extend( $.summernote.plugins,
        {
            ddmedia: function( context )
            {
                var layoutInfo = context.layoutInfo;
                var $toolbar   = layoutInfo.toolbar;

                var ui = $.summernote.ui;

                this.initialize = function ()
                {
                    // create button
                    var button = ui.button(
                        {
                            className: 'btn-info',
                            contents: '<i class="fa fa-file-image-o"></i>',
                            click: function (e) {
                                if (typeof MediaLibrary === 'undefined') {
                                    if (top.basefrm && top.basefrm.OverlayInstance) {
                                        top.basefrm.OverlayInstance.showOverlay(context);
                                    }
                                } else {
                                    MediaLibrary.open(/image\/.*/i, function (id, file, fullpath) {
                                            context.invoke('editor.insertImage', fullpath, function ($image) {
                                                    $image.css('max-width', '100%');
                                                    $image.attr('data-filename', file);
                                                    $image.attr('data-filepath', fullpath);
                                                    $image.attr('data-source', 'media');
                                                    $image.addClass('dd-wysiwyg-media-image');
                                                }
                                            );
                                        }
                                    );
                                }
                            }
                        }
                    );

                    // generate jQuery element from button instance.
                    this.$button = button.render();

                    if( $toolbar.find( '.note-btn-group.note-insert' ).length )
                    {
                        $toolbar.find( '.note-btn-group.note-insert' ).append( this.$button );
                    }
                    else
                    {
                        $toolbar.append( $( '<div class="note-btn-group btn-group" />' ).append( this.$button ) );
                    }
                };

                this.destroy = function ()
                {
                    this.$button.remove();
                    this.$button = null;
                };
            }
        }
    );

} )( jQuery );
