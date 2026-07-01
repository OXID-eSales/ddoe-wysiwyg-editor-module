import LinkDialog from "./LinkDialog.js";

export function configureLinkDialogModule() {
    $.extend(true, $.summernote.lang, {
        'en-US': {
            link_extend: {
                or: 'or',
                cms: 'CMS-Ident'
            },
        },
        'de-DE': {
            link_extend: {
                or: 'oder',
                cms: 'CMS-Ident'
            },
        },
    });

    $.summernote.options.modules.linkDialog = LinkDialog;
}
