/**
 * This file is part of OXID eSales WYSIWYG module.
 *
 * OXID eSales WYSIWYG module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales WYSIWYG module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales WYSIWYG module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales WYSIWYG
 */

module.exports = {

    wysiwyg: {

        options: {
            sourceMap: true,
            preserveComments: 'some'
        },

        files: {
            "out/src/js/backend.min.js": [
                "node_modules/summernote/dist/summernote.js",
                "build/vendor/summernote/js/plugins/ddmedia.summernote.js",
                "build/vendor/summernote/js/plugins/smarty.summernote.js",
                "build/js/backend.js"
            ],

            "out/src/js/medialibrary.min.js": [
                "node_modules/dropzone/dist/dropzone.js",
                "build/js/base/helper.js",
                "build/js/base/medialibrary.js"
            ],

            "out/src/js/overlay.min.js": [
                "node_modules/jquery/dist/jquery.js",
                "build/js/overlay.js"
            ]

        }
    },

    wysiwyglang: {

        files: {
            "out/src/js/lang/summernote-de.min.js": "node_modules/summernote/js/lang/summernote-de-DE.js"
        }

    },

    bootstrap: {
        files: {
            "out/src/js/bootstrap.min.js": [
                "node_modules/bootstrap/dist/js/bootstrap.js"
            ]
        }
    },

    jquery: {
        files: {
            "out/src/js/jquery.min.js": [
                "node_modules/jquery/dist/jquery.js"
            ]
        }
    }

};
