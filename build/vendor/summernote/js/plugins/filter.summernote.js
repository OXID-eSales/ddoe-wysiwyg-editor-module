/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law.
 *
 * Any unauthorized use of this software will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2020
 * @version   OXID eSales Visual CMS
 */

;( function( $ )
{
    'use strict';

    var isTextarea = function ( node )
    {
        return node && node.nodeName.toUpperCase() === 'TEXTAREA';
    };

    $.extend( $.summernote.dom,
        {
            value: function( $node )
            {
                var val;

                if( isTextarea( $node[0] ) )
                {
                    val = $node.val();

                    // filter cross-site scripts from code view
                    val = val.replace(/<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*?>/gim, '');
                }
                else
                {
                    val = $node.html();
                }

                return val;
            },

            html: function ( $node )
            {
                var markup = this.value( $node );

                // filter cross-site scripts from code view
                markup = markup.replace(/<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*?>/gim, '');

                markup = $.trim( markup );

                return markup;
            }

        }
    );

} )( jQuery );