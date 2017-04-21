/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

module.exports = function( grunt ) {

    var path = require( 'path' );

    // measures the time each task takes
    require( 'time-grunt' )( grunt );

    // load grunt config
    require( 'load-grunt-config' )( grunt,
        {
            configPath: path.join( process.cwd() , 'build' + path.sep + 'grunt' )
        }
    );

};