/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$.noConflict();

+function( $ )
{
    'use strict';

    var tooltipPlugin = $.fn.tooltip;

    $.fn.tooltip = function( options )
    {
        if (Object.prototype.toString.call(options) === "[object Object]") {
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
                                        [ 'formatting', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'clear' ] ],
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

                                // replace incorrectly encoded html lace bracket in smarty tags
                                this.value = this.value.replace( /\[\{(([^\}\]]|\}[^\]]|[^\}]\])*)\}\]/gi, function( smarty ){
                                    return smarty.replace(/-\&gt;/g, "->");
                                });
                           }
                        );
                    }
                );
            }
        }
    );

}( jQuery );
