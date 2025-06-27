/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

export function injectOxidBridge() {

    var isTextarea = function (node) {
        return node && node.nodeName.toUpperCase() === 'TEXTAREA';
    };

    $.extend($.summernote.dom, {
        value: function($node, stripLinebreaks) {
            var val;

            if (isTextarea($node[0])) {
                val = $node.val();

                // fix tags double quotes within attributes
                var regex = new RegExp(/(=\s*")([^">]*)(\{\{([^\}\}]|\}[^\}]|[^\}]\})*\}\})([^">]*)(")/gi);
                val = val.replace(regex, function(text, start, attr_before, smarty, smarty_inner, attr_after, end) {
                    smarty = smarty.replace(/\\"/g, '\'').replace(/"/g, '\'');
                    return (start + attr_before + smarty + attr_after + end);
                });

                // switch twig function call with media url
                var regexMediaUrl = new RegExp(/<img[^>]*src=\s*"(.*?[^"])"[^>]*data-id=\s*"([^">]+)"[^>]*class=\s*"[^">]*dd-wysiwyg-media-image[^">]*"[^>]*>/gi);
                val = val.replace(regexMediaUrl, function(text, src, id) {
                    text = text.replace(src, top.basefrm.mediaUrls[id]);
                    return text;
                });
            } else {
                val = $node.html();
            }

            if (stripLinebreaks) {
                return val.replace(/[\n\r]/g, '');
            }

            return val;
        },

        html: function ($node, isNewlineOnBlock) {
            var markup = this.value( $node );

            if (isNewlineOnBlock) {
                var regexTag = /<(\/?)(\b(?!!)[^>\s]*)(.*?)(\s*\/?>)/g;

                markup = markup.replace(regexTag, function (match, endSlash, name) {
                    name = name.toUpperCase();

                    var isEndOfInlineContainer = /^DIV|^TD|^TH|^P|^LI|^H[1-7]/.test( name ) && !!endSlash;
                    var isBlockNode            = /^BLOCKQUOTE|^TABLE|^TBODY|^TR|^HR|^UL|^OL/.test( name );

                    return match + ((isEndOfInlineContainer || isBlockNode) ? '\n' : '');
                });

                markup = $.trim( markup );
            }

            // set media smarty or twig tags
            markup = markup.replace(
                /<img[^>]*src=\s*"([^"]+)"[^>]*data-id=\s*"([^">]+)"[^>]*class=\s*"[^">]*dd-wysiwyg-media-image[^">]*"[^>]*>/gi,
                function(tag, src, id) {
                    return tag.replace( src, "{{oeMediaUrl('" + id + "')}}" );
                }
            );

            return markup;
        }
    });
}
