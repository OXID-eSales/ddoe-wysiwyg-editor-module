/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
