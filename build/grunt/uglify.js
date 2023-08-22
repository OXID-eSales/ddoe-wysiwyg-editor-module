/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

module.exports = {

    wysiwyg: {

        options: {
            sourceMap: true,
            preserveComments: 'some'
        },

        files: {
            "assets/out/src/js/ddoesummernote.min.js": [
                "build/vendor/summernote/dist/summernote.js",
                "build/vendor/summernote/plugin/ddmedia.summernote.js",
                "build/vendor/summernote/plugin/summernote.js",
                "build/js/plugins/summernote-video-responsive/summernote-video-responsive.js",
                "build/js/plugins/summernote-lang-extends/summernote-de-DE.js",
                "build/js/plugins/summernote-lang-extends/summernote-en-US.js",
                "build/js/summernote.js"
            ],
            "assets/out/src/js/backend.min.js": [
                "build/js/backend.js"
            ]

        }
    },

    wysiwyglang: {

        files: {
            "assets/out/src/js/lang/summernote-de.min.js": "build/vendor/summernote/dist/lang/summernote-de-DE.js"
        }

    },

    bootstrap: {
        files: {
            "assets/out/src/js/bootstrap.min.js": [
                "build/vendor/bootstrap/js/bootstrap.js"
            ]
        }
    },

    jquery: {
        files: {
            "assets/out/src/js/jquery.min.js": [
                "build/vendor/jquery/js/jquery-1.12.0.js"
            ]
        }
    },

    jqueryui: {
        files: {
            "assets/out/src/js/jquery-ui.min.js": [
                "build/vendor/jquery-ui/js/jquery-ui-1.11.4.js"
            ]
        }
    }

};
