/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law.
 *
 * Any unauthorized use of this software will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
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
            value: function( $node, stripLinebreaks )
                {
                    var val;

                    if( isTextarea( $node[0] ) )
                    {
                        val = $node.val();
                        val = val.replace( /(=\s*")([^">]*)(\[\{([^\}\]]|\}[^\]]|[^\}]\])*\}\])([^">]*)(")/gi, function( text, start, attr_before, smarty, smarty_inner, attr_after, end )
                           {
                               smarty = smarty.replace( /\\"/g, '\'' ).replace( /"/g, '\'' );
                               return ( start + attr_before + smarty + attr_after + end );
                           }
                        );
                    }
                    else
                    {
                        val = $node.html();
                    }

                    if ( stripLinebreaks )
                    {
                        return val.replace( /[\n\r]/g, '' );
                    }

                    return val;
                }

        }
    );

} )( jQuery );