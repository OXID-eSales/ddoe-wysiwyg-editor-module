/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
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
                            className: 'btn-default',
                            contents: '<i class="fa fa-file-image-o"></i>',
                            click: function ( e )
                            {
                                if( typeof MediaLibrary === 'undefined' )
                                {
                                    if( top.basefrm && top.basefrm.OverlayInstance )
                                    {
                                        top.basefrm.OverlayInstance.showOverlay( context );
                                    }
                                }
                                else
                                {
                                    MediaLibrary.open( /image\/.*/i, function( id, file, fullpath )
                                        {
                                            context.invoke('editor.insertImage', fullpath, file);
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