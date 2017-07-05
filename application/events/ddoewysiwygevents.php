<?php
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

/**
 * Class defines what module does on Shop events.
 */
class ddoewysiwygevents extends oxUBase
{

    /**
     * SQL statement, that will be executed only at the first time of module installation.
     *
     * @var array
     */
    private static $_sCreateDdMediaSql =
        "CREATE TABLE IF NOT EXISTS `ddmedia` (
           `OXID` char(32) NOT NULL,
           `DDFILENAME` varchar(255) NOT NULL,
           `DDFILESIZE` int(10) unsigned NOT NULL,
           `DDFILETYPE` varchar(50) NOT NULL,
           `DDTHUMB` varchar(255) NOT NULL,
           `OXTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           PRIMARY KEY (`OXID`)
         ) ENGINE=InnoDB; ";

    /**
     * An array of SQL statements, that will be executed only at the first time of module installation.
     *
     * @var array
     */
    private static $_aSetupDdMediaSQLs = array(
        "DDIMAGESIZE" => "ALTER TABLE  `ddmedia` ADD  `DDIMAGESIZE` VARCHAR( 100 ) AFTER  `DDTHUMB`; ",
        "OXSHOPID"    => "ALTER TABLE  `ddmedia` ADD `OXSHOPID` INT(10) UNSIGNED NOT NULL AFTER `OXID`; ",
    );

    /**
     * An array of SQL statements, that will be executed only at the update of module.
     *
     * @var array
     */
    private static $__aUpdateSQLs = array(
    );


    /**
     * An array of SQL statements, that will be executed on module activation.
     *
     * @var array
     */
    private static $__aActivateSQLs = array(
    );


    /**
     * An array of SQL statements, that will be executed on module deactivation.
     *
     * @var array
     */
    private static $__aDeactivateSQLs = array(
        // '',
    );


    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        self::setupModule();

        self::updateModule();

        self::activateModule();

        self::regenerateViews();

        self::clearCache();
    }


    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
        self::executeSQLs( self::$__aDeactivateSQLs );

        /** @var oxConfig $oConfig */
        $oConfig = oxNew( \OxidEsales\Eshop\Core\Config::class );

        // Cache leeren
        /** @var oxUtilsView $oUtilsView */
        $oUtilsView = \OxidEsales\Eshop\Core\Registry::get( 'oxUtilsView' );
        $sSmartyDir = $oUtilsView->getSmartyDir();

        if( $sSmartyDir && is_readable( $sSmartyDir ) )
        {
            foreach( glob( $sSmartyDir . '*' ) as $sFile )
            {
                if ( !is_dir( $sFile ) )
                {
                    @unlink( $sFile );
                }
            }
        }

    }

    /**
     * Execute the sql at the first time of the module installation.
     */
    private static function setupModule()
    {
        // Check if ddmedia table was already created, if not create it.
        if (!self::tableExists('ddmedia')) {
            self::executeSQL(self::$_sCreateDdMediaSql);
        }
        // Check if ddmedia table has all needed fields, if not add them to the table.
        foreach (self::$_aSetupDdMediaSQLs as $sField => $sSql) {
            if (!self::fieldExists($sField, 'ddmedia')) {
                self::executeSQL($sSql);
            }
        }
        /** @var oxConfig $oConfig */
        $oConfig = oxNew( \OxidEsales\Eshop\Core\Config::class );
        $oConfig->saveShopConfVar( 'bool', 'blModuleWasEnabled', 'true', $oConfig->getShopId(), 'module:ddoewysiwyg' );

    }

    /**
     * Activate module after installation.
     */
    private static function activateModule()
    {
        self::executeSQLs( self::$__aActivateSQLs );
    }

    /**
     * Updates module if it was already installed.
     */
    private static function updateModule()
    {
        /** @var oxConfig $oConfig */
        $oConfig = oxNew( \OxidEsales\Eshop\Core\Config::class );

        /** @var oxModule $oModule */
        $oModule = oxNew( \OxidEsales\Eshop\Core\Module\Module::class );
        $oModule->load( 'ddoewysiwyg' );

        $sCurrentVersion   = $oModule->getInfo( 'version' );
        $sInstalledVersion = $oConfig->getShopConfVar( 'iInstallledVersion', $oConfig->getShopId(), 'module:ddoewysiwyg' );

        if( !$sInstalledVersion || version_compare( $sInstalledVersion, $sCurrentVersion, '<' ) )
        {
            if( self::$__aUpdateSQLs )
            {
                foreach( self::$__aUpdateSQLs as $sUpdateVersion => $aSQLs )
                {
                    if( !$sInstalledVersion || version_compare( $sUpdateVersion, $sInstalledVersion, '>' ) )
                    {
                        self::executeSQLs( $aSQLs );
                    }
                }
            }

            $oConfig->saveShopConfVar( 'str', 'iInstallledVersion', $sCurrentVersion, $oConfig->getShopId(), 'module:ddoewysiwyg' );
        }

    }

    /**
     * Regenerate views for changed tables
     */
    protected static function regenerateViews()
    {
        $oDbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class );
        $oDbMetaDataHandler->updateViews();
    }

    /**
     * Empty cache
     */
    private static function clearCache()
    {
        /** @var oxUtilsView $oUtilsView */
        $oUtilsView = \OxidEsales\Eshop\Core\Registry::get( 'oxUtilsView' );
        $sSmartyDir = $oUtilsView->getSmartyDir();

        if( $sSmartyDir && is_readable( $sSmartyDir ) )
        {
            foreach( glob( $sSmartyDir . '*' ) as $sFile )
            {
                if ( !is_dir( $sFile ) )
                {
                    @unlink( $sFile );
                }
            }
        }

        // Initialise Smarty
        $oUtilsView->getSmarty( true );
    }

    /**
     * Check if table exists
     *
     * @param string $sTableName table name
     *
     * @return bool
     */
    protected static function tableExists($sTableName)
    {
        $oDbMetaDataHandler = oxNew( \OxidEsales\Eshop\Core\DbMetaDataHandler::class );
        return $oDbMetaDataHandler->tableExists($sTableName);
    }

    /**
     * Check if field exists in table
     *
     * @param string $sFieldName field name
     * @param string $sTableName table name
     *
     * @return bool
     */
    protected static function fieldExists($sFieldName, $sTableName)
    {
        $oDbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class );
        return $oDbMetaDataHandler->fieldExists($sFieldName, $sTableName);
    }

    /**
     * Executes given sql statements.
     *
     * @param $aSQLs array
     */
    private static function executeSQLs( $aSQLs )
    {
        if( count( $aSQLs ) > 0 )
        {
            foreach( $aSQLs as $sSQL )
            {
                self::executeSQL( $sSQL );
            }
        }
    }

    /**
     * Executes given sql statement.
     *
     * @param string $sSQL Sql to execute.
     */
    private static function executeSQL( $sSQL )
    {
        @oxDb::getDb()->execute( $sSQL );
    }
}
