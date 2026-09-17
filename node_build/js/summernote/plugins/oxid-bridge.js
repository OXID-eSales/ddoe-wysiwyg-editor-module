/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

var MEDIA_IMAGE_CLASS = 'dd-wysiwyg-media-image';

function isTextarea(node) {
    return node && node.nodeName.toUpperCase() === 'TEXTAREA';
}

function getTagAttributeValue(tag, attributeName) {
    var match = tag.match(new RegExp('\\s' + attributeName + '\\s*=\\s*"([^"]*)"', 'i'));

    return match ? match[1] : null;
}

function checkTagHasMediaImageClass(tag) {
    var classAttribute = getTagAttributeValue(tag, 'class') || '';

    return classAttribute.split(/\s+/).indexOf(MEDIA_IMAGE_CLASS) !== -1;
}

function rewriteMediaImageSourceInMarkup(markup, buildSourceCallback) {
    return markup.replace(/<img\s[^>]*>/gi, function (tag) {
        var id = getTagAttributeValue(tag, 'data-id');

        if (!id || !checkTagHasMediaImageClass(tag)) {
            return tag;
        }

        var newSourceValue = buildSourceCallback(id);

        // replaces to new source value in the src attribute of the specific tag string
        return tag.replace(/(\ssrc\s*=\s*")[^"]*(")/i, function (match, start, end) {
            return start + newSourceValue + end;
        });
    });
}

export function injectOxidBridge(mediaModule) {
    $.extend($.summernote.dom, {
        value: function ($node, stripLinebreaks) {
            var val;

            if (isTextarea($node[0])) {
                val = $node.val();

                // fix tags double quotes within attributes
                var regex = new RegExp(/(=\s*")([^">]*)(\{\{([^\}\}]|\}[^\}]|[^\}]\})*\}\})([^">]*)(")/gi);
                val = val.replace(regex, function (text, start, attr_before, smarty, smarty_inner, attr_after, end) {
                    smarty = smarty.replace(/\\"/g, '\'').replace(/"/g, '\'');
                    return (start + attr_before + smarty + attr_after + end);
                });

                // switch twig function call with media url
                val = rewriteMediaImageSourceInMarkup(val, function (id) {
                    return mediaModule.getMediaUrl(id);
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
            var markup = this.value($node);

            if (isNewlineOnBlock) {
                var regexTag = /<(\/?)(\b(?!!)[^>\s]*)(.*?)(\s*\/?>)/g;

                markup = markup.replace(regexTag, function (match, endSlash, name) {
                    name = name.toUpperCase();

                    var isEndOfInlineContainer = /^DIV|^TD|^TH|^P|^LI|^H[1-7]/.test(name) && !!endSlash;
                    var isBlockNode = /^BLOCKQUOTE|^TABLE|^TBODY|^TR|^HR|^UL|^OL/.test(name);

                    return match + ((isEndOfInlineContainer || isBlockNode) ? '\n' : '');
                });

                markup = $.trim(markup);
            }

            // set media smarty or twig tags
            markup = rewriteMediaImageSourceInMarkup(markup, function (id) {
                return "{{oeMediaUrl('" + id + "')}}";
            });

            return markup;
        }
    });
}
