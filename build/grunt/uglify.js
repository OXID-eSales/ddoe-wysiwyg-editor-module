/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law.
 *
 * Any unauthorized use of this software will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales Summernote
 */

module.exports = {

    wysiwyg: {

        options: {
            sourceMap: true
        },

        files: {
            "out/src/js/backend.min.js": [
                "build/vendor/summernote/js/summernote.js",
                "build/vendor/summernote/js/plugins/ddmedia.summernote.js",
                "build/vendor/summernote/js/plugins/smarty.summernote.js",
                "build/js/backend.js"
            ],

            "out/src/js/medialibrary.min.js": [
                "build/vendor/dropzone/js/dropzone.js",
                "build/js/base/helper.js",
                "build/js/base/medialibrary.js"
            ],

            "out/src/js/overlay.min.js": [
                "build/vendor/jquery/js/jquery-1.12.0.js",
                "build/js/overlay.js"
            ]

        }
    },

    wysiwyglang: {

        files: {
            "out/src/js/lang/summernote-de.min.js": "build/vendor/summernote/js/lang/summernote-de-DE.js"
        }

    },

    bootstrap: {
        files: {
            "out/src/js/bootstrap.min.js": [
                "build/vendor/bootstrap/js/bootstrap.js"
            ]
        }
    },

    jquery: {
        files: {
            "out/src/js/jquery.min.js": [
                "build/vendor/jquery/js/jquery-1.12.0.js"
            ]
        }
    }

};
