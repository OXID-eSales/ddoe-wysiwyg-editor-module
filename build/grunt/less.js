/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
            "assets/out/src/css/wysiwyg.min.css": [
                "build/less/backend.less"
            ],

            "assets/out/src/css/medialibrary.min.css": [
                "build/less/base/medialibrary.less"
            ],

            "assets/out/src/css/overlay.min.css": [
                "build/less/overlay.less"
            ]
        }
    },

    bootstrap: {
        files: {
            "assets/out/src/css/bootstrap.min.css": "build/vendor/bootstrap/less/bootstrap.less"
        }
    },

    fontawesome: {
        files: {
            "assets/out/src/css/font-awesome.min.css": "build/vendor/font-awesome/less/font-awesome.less"
        }
    }

};
