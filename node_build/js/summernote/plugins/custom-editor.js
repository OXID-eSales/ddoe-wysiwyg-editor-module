export function overrideEditorMethods() {
    const EditorClass = $.summernote.options.modules.editor;

    if (EditorClass && !EditorClass.prototype._seoUrlPatched) {
        const originalCheckLinkUrl = EditorClass.prototype.checkLinkUrl;

        EditorClass.prototype.checkLinkUrl = function(linkUrl) {
            const seoUrlPattern = /^\{\{ seo_url\(\{(?:type: '[^']*', )?ident: '[^']*'\}\) \}\}$/;
            if (seoUrlPattern.test(linkUrl)) {
                return linkUrl;
            }
            return originalCheckLinkUrl.call(this, linkUrl);
        };

        EditorClass.prototype._seoUrlPatched = true;
    }
}
