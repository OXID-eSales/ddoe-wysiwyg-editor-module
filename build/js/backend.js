/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

$.noConflict();

+function( $ )
{
    'use strict';

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