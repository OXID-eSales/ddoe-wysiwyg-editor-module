/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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

                    var regex = new RegExp(/(=\s*")([^">]*)(\{\{([^\}\}]|\}[^\}]|[^\}]\})*\}\})([^">]*)(")/gi);
                    var regexMediaUrl = new RegExp(/<img[^>]*src=\s*"(\{\{oViewConf.getMediaUrl\(\)\}\}[^">]+)"[^>]*data-filepath=\s*"([^">]+)"[^>]*class=\s*"[^">]*dd-wysiwyg-media-image[^">]*"[^>]*>/gi);

                    // fix smarty or twig tags double quotes within attributes
                    val = val.replace(regex, function( text, start, attr_before, smarty, smarty_inner, attr_after, end )
                        {
                            smarty = smarty.replace( /\\"/g, '\'' ).replace( /"/g, '\'' );
                            return ( start + attr_before + smarty + attr_after + end );
                        }
                    );

                    // switch smarty or twig function call with media url
                    val = val.replace(regexMediaUrl, function( text, src, filepath )
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
                var mediaTag = '{{oViewConf.getMediaUrl()}}';

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

                // set media smarty or twig tags
                markup = markup.replace( /<img[^>]*src=\s*"([^"]+)"[^>]*data-filename=\s*"([^">]+)"[^>]*class=\s*"[^">]*dd-wysiwyg-media-image[^">]*"[^>]*>/gi, function( tag, src, filename )
                    {
                        return tag.replace( src, mediaTag + '/' + filename );
                    }
                );

                // replace html entities in smarty tags
                markup = markup.replace(/\[\{(([^\}\]]|\}[^\]]|[^\}]\])*)\}\]/gi, function( tag, smarty, char )
                    {
                        return $( '<textarea />' ).html( tag ).text();
                    }
                );

                return markup;
            }

        }
    );

} )( jQuery );
