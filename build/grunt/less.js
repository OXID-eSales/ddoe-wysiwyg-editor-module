/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
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