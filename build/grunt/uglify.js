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
            "assets/out/src/js/backend.min.js": [
                "build/vendor/summernote/js/summernote.js",
                "build/vendor/summernote/js/plugins/ddmedia.summernote.js",
                "build/vendor/summernote/js/plugins/smarty.summernote.js",
                "build/js/backend.js"
            ],

            "assets/out/src/js/base.min.js": [
                "build/vendor/dropzone/js/dropzone.js",
                "build/js/base/helper.js",
                "build/js/base/medialibrary.js"
            ],

            "assets/out/src/js/overlay.min.js": [
                "build/vendor/jquery/js/jquery-1.12.0.js",
                "build/js/overlay.js"
            ]

        }
    },

    wysiwyglang: {

        files: {
            "assets/out/src/js/lang/summernote-de.min.js": "build/vendor/summernote/js/lang/summernote-de-DE.js"
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
