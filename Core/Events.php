<?php
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

namespace OxidEsales\WysiwygModule\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\Module\Module;

/**
 * Class defines what module does on Shop events.
 */
class Events
{

    /**
     * SQL statement, that will be executed only at the first time of module installation.
     *
     * @var array
     */
    private static $_sCreateDdMediaSql =
        "CREATE TABLE IF NOT EXISTS `ddmedia` (
           `OXID` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
           `DDFILENAME` VARCHAR(255) NOT NULL,
           `DDFILESIZE` INT(10) UNSIGNED NOT NULL,
           `DDFILETYPE` VARCHAR(50) NOT NULL,
           `DDTHUMB` VARCHAR(255) NOT NULL,
           `OXTIMESTAMP` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    private static $__aUpdateSQLs = array();


    /**
     * An array of SQL statements, that will be executed on module activation.
     *
     * @var array
     */
    private static $__aActivateSQLs = array();


    /**
     * An array of SQL statements, that will be executed on module deactivation.
     *
     * @var array
     */
    private static $__aDeactivateSQLs = array();


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
        /** @var \OxidEsales\Eshop\Core\Config $oConfig */
        $oConfig = Registry::getConfig();
        $oConfig->saveShopConfVar('bool', 'blModuleWasEnabled', 'true', $oConfig->getShopId(), 'module:ddoewysiwyg');
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
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class );

        return $oDbMetaDataHandler->tableExists($sTableName);
    }

    /**
     * Executes given sql statement.
     *
     * @param string $sSQL Sql to execute.
     */
    private static function executeSQL($sSQL)
    {
        @DatabaseProvider::getDb()->execute($sSQL);
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
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class );

        return $oDbMetaDataHandler->fieldExists($sFieldName, $sTableName);
    }

    /**
     * Updates module if it was already installed.
     */
    private static function updateModule()
    {
        /** @var \OxidEsales\Eshop\Core\Config $oConfig */
        $oConfig = Registry::getConfig();

        /** @var Module $oModule */
        $oModule = oxNew(Module::class);
        $oModule->load('ddoewysiwyg');

        $sCurrentVersion = $oModule->getInfo('version');
        $sInstalledVersion = $oConfig->getShopConfVar('iInstallledVersion', $oConfig->getShopId(), 'module:ddoewysiwyg');

        if (!$sInstalledVersion || version_compare($sInstalledVersion, $sCurrentVersion, '<')) {
            if (self::$__aUpdateSQLs) {
                foreach (self::$__aUpdateSQLs as $sUpdateVersion => $aSQLs) {
                    if (!$sInstalledVersion || version_compare($sUpdateVersion, $sInstalledVersion, '>')) {
                        self::executeSQLs($aSQLs);
                    }
                }
            }

            $oConfig->saveShopConfVar('str', 'iInstallledVersion', $sCurrentVersion, $oConfig->getShopId(), 'module:ddoewysiwyg');
        }
    }

    /**
     * Executes given sql statements.
     *
     * @param array $aSQLs
     */
    private static function executeSQLs($aSQLs)
    {
        if (count($aSQLs) > 0) {
            foreach ($aSQLs as $sSQL) {
                self::executeSQL($sSQL);
            }
        }
    }

    /**
     * Activate module after installation.
     */
    private static function activateModule()
    {
        self::executeSQLs(self::$__aActivateSQLs);
    }

    /**
     * Regenerate views for changed tables
     */
    protected static function regenerateViews()
    {
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class );
        $oDbMetaDataHandler->updateViews();
    }

    /**
     * Empty cache
     */
    private static function clearCache()
    {
        /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
        $oUtilsView = Registry::get('oxUtilsView');
        $sSmartyDir = $oUtilsView->getSmartyDir();

        if ($sSmartyDir && is_readable($sSmartyDir)) {
            foreach (glob($sSmartyDir . '*') as $sFile) {
                if (!is_dir($sFile)) {
                    @unlink($sFile);
                }
            }
        }

        // Initialise Smarty
        $oUtilsView->getSmarty(true);
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
        self::executeSQLs(self::$__aDeactivateSQLs);

        self::clearCache();
    }
}
