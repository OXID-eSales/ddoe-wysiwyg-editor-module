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

            onCreateLink: function (linkUrl) {
                if (linkUrl.indexOf("[{") === 0) {
                    // leave urls beginning with '[{' untouched
                    return linkUrl;
                }
                if (linkUrl) {
                    // summernotes default behaviour:
                    linkUrl = /^[A-Za-z][A-Za-z0-9+-.]*\:[\/\/]?/.test(linkUrl) ? linkUrl : 'http://' + linkUrl;
                }
                return linkUrl;
            },
            codeviewFilter: true,
            codeviewIframeFilter: true

        }, options);

        return this.summernote(settings);
    };

}(jQuery));