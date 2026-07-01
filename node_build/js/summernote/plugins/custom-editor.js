export function overrideEditorMethods() {
    const EditorClass = $.summernote.options.modules.editor;

    if (EditorClass && !EditorClass.prototype._seoUrlPatched) {
        const originalCheckLinkUrl = EditorClass.prototype.checkLinkUrl;

        EditorClass.prototype.checkLinkUrl = function(linkUrl) {
            // check if linkUrl matches with the pattern {{ seo_url({ident: 'someWord'}) }} with an ident for a cms snippet
            const seoUrlPattern = /^\{{ seo_url\(\{ident: '[^']*'\}\) \}}$/;
            if (seoUrlPattern.test(linkUrl)) {
                return linkUrl;
            }
            return originalCheckLinkUrl.call(this, linkUrl);
        };

        EditorClass.prototype._seoUrlPatched = true;
    }
}
