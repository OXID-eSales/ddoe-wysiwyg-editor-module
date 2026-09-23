/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Extends the summernote autoSync module, which writes the editor content into the textarea the
 * editor was created from. That textarea is the actual field that is sent with the form.
 */
export function createNoteSyncModule(buildContentValueCallback) {
    // The class is created on call because the module to extend is only known once summernote loaded.
    return class NoteSync extends $.summernote.options.modules.autoSync {
        constructor(context) {
            super(context);

            const originalSync = this.events['summernote.change'];
            const syncNote = () => {
                originalSync();

                const contentValue = buildContentValueCallback(this.$note.val());

                this.$note.val(contentValue);
            };

            this.events = {
                'summernote.init': syncNote,
                'summernote.change': syncNote,
                'summernote.change.codeview': syncNote, // yes, there is another event there
            };
        }
    };
}
