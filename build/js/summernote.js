(function ($) {
    'use strict';

    $.fn.ddoeInitSummernote = function (options) {
        var settings = $.extend({
            lang: 'de-DE',
            minHeight: 100,

            toolbar: [
                ['style', ['style']],
                ['formatting', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['layout', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'videoResponsive', 'hr']],
                ['misc', ['codeview']]
            ],

            dialogsInBody: false,

            buttons: {
                ddmedia: 'ddmedia'
            },

            disableDragAndDrop: true,
            codeviewFilter: true,
            codeviewIframeFilter: true

        }, options);

        return this.summernote(settings);
    };

}(jQuery));