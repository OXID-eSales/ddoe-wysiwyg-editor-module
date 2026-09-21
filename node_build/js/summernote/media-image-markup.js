/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

function getTagAttributeValue(tag, attributeName) {
    var match = tag.match(new RegExp('\\s' + attributeName + '\\s*=\\s*"([^"]*)"', 'i'));

    return match ? match[1] : null;
}

function checkTagHasMediaImageClass(tag) {
    var classAttribute = getTagAttributeValue(tag, 'class') || '';

    return classAttribute.split(/\s+/).indexOf('dd-wysiwyg-media-image') !== -1;
}

export function replaceMediaImageSrcValues(markup, buildSrcValueCallback) {
    return markup.replace(/<img\s[^>]*>/gi, function (tag) {
        var id = getTagAttributeValue(tag, 'data-id');

        if (!id || !checkTagHasMediaImageClass(tag)) {
            return tag;
        }

        var newSrcValue = buildSrcValueCallback(id);

        // replaces to new value in the src attribute of the specific tag string
        return tag.replace(/(\ssrc\s*=\s*")[^"]*(")/i, function (match, start, end) {
            return start + newSrcValue + end;
        });
    });
}

export function anchorMediaUrlsInMarkup(markup) {
    return replaceMediaImageSrcValues(markup, function (id) {
        return "{{oeMediaUrl('" + id + "')}}";
    });
}
