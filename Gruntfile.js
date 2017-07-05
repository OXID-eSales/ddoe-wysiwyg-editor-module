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
