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

    options: {
        compress: true,
        plugins: [
            new ( require( 'less-plugin-autoprefix') )( { browsers: [ "last 2 versions" ] } )
        ]
    },

    wysiwyg: {
        files: {
            "out/src/css/backend.min.css": [
                "build/less/backend.less"
            ],

            "out/src/css/medialibrary.min.css": [
                "build/less/base/medialibrary.less"
            ],

            "out/src/css/overlay.min.css": [
                "build/less/overlay.less"
            ]
        }
    },

    bootstrap: {
        files: {
            "out/src/css/bootstrap.min.css": "build/vendor/bootstrap/less/bootstrap.less"
        }
    },

    fontawesome: {
        files: {
            "out/src/css/font-awesome.min.css": "build/vendor/font-awesome/less/font-awesome.less"
        }
    }

};
