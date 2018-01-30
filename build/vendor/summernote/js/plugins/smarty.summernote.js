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

                    // fix smarty tags within attributes
                    val = val.replace( /(=\s*")([^">]*)(\[\{([^\}\]]|\}[^\]]|[^\}]\])*\}\])([^">]*)(")/gi, function( text, start, attr_before, smarty, smarty_inner, attr_after, end )
                        {
                            smarty = smarty.replace( /\\"/g, '\'' ).replace( /"/g, '\'' );
                            return ( start + attr_before + smarty + attr_after + end );
                        }
                    );

                    // switch smarty function call with media url
                    val = val.replace( /<img[^>]*src=\s*"(\[\{\$oViewConf\-\>getMediaUrl\(\)\}\][^">]+)"[^>]*data-filepath=\s*"([^">]+)"[^>]*class=\s*"[^">]*dd-wysiwyg-media-image[^">]*"[^>]*>/gi, function( text, src, filepath )
                        {
                            text = text.replace( src, filepath );
                            return text;
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
            },

            html: function ( $node, isNewlineOnBlock )
            {
                var markup = this.value( $node );

                if ( isNewlineOnBlock )
                {
                    var regexTag = /<(\/?)(\b(?!!)[^>\s]*)(.*?)(\s*\/?>)/g;

                    markup = markup.replace( regexTag, function ( match, endSlash, name )
                        {
                            name = name.toUpperCase();

                            var isEndOfInlineContainer = /^DIV|^TD|^TH|^P|^LI|^H[1-7]/.test( name ) && !!endSlash;
                            var isBlockNode            = /^BLOCKQUOTE|^TABLE|^TBODY|^TR|^HR|^UL|^OL/.test( name );

                            return match + ((isEndOfInlineContainer || isBlockNode) ? '\n' : '');
                        }
                    );

                    markup = $.trim( markup );
                }

                // set media smarty tags
                markup = markup.replace( /<img[^>]*src=\s*"([^"]+)"[^>]*data-filename=\s*"([^">]+)"[^>]*class=\s*"[^">]*dd-wysiwyg-media-image[^">]*"[^>]*>/gi, function( tag, src, filename )
                    {
                        return tag.replace( src, '[{$oViewConf->getMediaUrl()}]' + filename );
                    }
                );

                // replace html entities in smarty tags
                markup = markup.replace( /\[\{(([^\}\]]|\}[^\]]|[^\}]\])*)\}\]/gi, function( tag, smarty, char )
                    {
                        return $( '<textarea />' ).html( tag ).text();
                    }
                );


                return markup;
            }

        }
    );

} )( jQuery );