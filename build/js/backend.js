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

$.noConflict();

+function( $ )
{
    'use strict';

    var tooltipPlugin = $.fn.tooltip;

    $.fn.tooltip = function( options )
    {
        if( typeof options !== 'undefined' )
        {
            options.container = '#ddoew';
        }

        return tooltipPlugin.call( this, options );
    };

    $( function()
        {
            // Summernote WYSIWYG Editor
            if ( typeof $().summernote === 'function' )
            {
                $( '.ddoe-wysiwyg-editor > textarea' ).each( function ()
                    {
                        var dataRte = $( this ).attr('data-rte');

                        if (typeof dataRte === 'undefined')
                        {
                            $( this ).attr('data-rte', '1');

                            $( this ).closest( 'td' ).find( '.messagebox' ).remove();

                            var iHeight = $( this ).height();

                            var $editor = $( this ).summernote(
                                {
                                    lang: 'de-DE',
                                    minHeight: iHeight || 100,

                                    toolbar: [

                                        [ 'style', [ 'style' ] ],
                                        [ 'formatting', [ 'bold', 'italic', 'underline', 'strikethrough', 'clear' ] ],
                                        //[ 'fontname', [ 'fontname' ] ],
                                        [ 'fontsize', [ 'fontsize' ] ],
                                        [ 'color', [ 'color' ] ],
                                        [ 'layout', [ 'ul', 'ol', 'paragraph' ] ],
                                        [ 'height', [ 'height' ] ],
                                        [ 'table', [ 'table' ] ],
                                        [ 'insert', [ 'link', 'video', 'hr' ] ],
                                        [ 'misc', [ 'codeview' ] ]

                                    ],

                                    dialogsInBody: false,

                                    buttons: {
                                        ddmedia: 'ddmedia'
                                    },

                                    disableDragAndDrop: true

                                }
                            );

                            if ('disabled' === $(this).attr('disabled')) {
                                $(this).summernote('disable');
                            }

                            var editorContext = $editor.data( 'summernote' );

                            editorContext.invoke( 'codeview.activate' );
                            editorContext.invoke( 'codeview.deactivate' );
                        }
                    }
                );

                var $form = $( '.ddoe-wysiwyg-editor' ).first().closest( 'form' );

                $form.find( '*[type="submit"]' ).first().on( 'click', function()
                    {
                        $( '.ddoe-wysiwyg-editor > textarea', $form ).each( function ()
                           {
                               var context = $( this ).data( 'summernote' );

                               // deactivate codeview before getting value
                               if( context.invoke( 'codeview.isActivated' ) )
                               {
                                   context.invoke( 'codeview.deactivate' );
                               }

                               context.invoke( 'codeview.activate' );
                           }
                        );
                    }
                );
            }
        }
    );

}( jQuery );
