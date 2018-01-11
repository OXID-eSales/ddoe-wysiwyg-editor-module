/**
 * This file is part of OXID eSales WYSIWYG module.
 *
 * OXID eSales WYSIWYG module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales WYSIWYG module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales WYSIWYG module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales WYSIWYG
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
                                            context.invoke('editor.insertImage', fullpath, function( $image )
                                                {
                                                    $image.css( 'max-width', '100%' );
                                                    $image.attr( 'data-filename', file );
                                                    $image.attr( 'data-filepath', fullpath );
                                                    $image.attr( 'data-source', 'media' );
                                                    $image.addClass( 'dd-wysiwyg-media-image' );
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
