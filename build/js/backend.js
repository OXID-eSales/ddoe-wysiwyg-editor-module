/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law.
 *
 * Any unauthorized use of this software will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales Summernote
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
            if ( typeof $().summernote == 'function' )
            {
                $( '.ddoe-wysiwyg-editor textarea' ).each( function ()
                    {
                        $( this ).closest( 'td' ).find( '.messagebox' ).remove();

                        var iHeight = $( this ).height();

                        $( this ).summernote(
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
                    }
                );
            }
        }
    );

}( jQuery );
