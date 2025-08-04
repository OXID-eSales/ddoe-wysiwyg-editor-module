export function overrideEditorMethods(summernote) {
    const context = summernote.data('summernote');

    const originalCheckLinkUrl = context.modules.editor.checkLinkUrl;

    context.modules.editor.checkLinkUrl = function(linkUrl) {
        // check if linkUrl matches with the pattern {{ seo_url({ident: 'someWord'}) }} with an ident for a cms snippet
        const seoUrlPattern = /^\{{ seo_url\(\{ident: '[^']*'\}\) \}}$/;
        if ( seoUrlPattern.test( linkUrl ) )
        {
            return linkUrl;
        }

        return originalCheckLinkUrl(linkUrl);
    };

    return summernote;
}
