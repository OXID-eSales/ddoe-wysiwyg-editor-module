<?php

/**
 *
 *     |o     o    |          |
 * ,---|.,---..,---|,---.,---.|__/
 * |   |||   |||   ||---'`---.|  \
 * `---'``---|``---'`---'`---'`   `
 *       `---'    [media solutions]
 *
 * @copyright   (c) digidesk - media solutions
 * @link            https://www.digidesk.de
 */

namespace OxidEsales\WysiwygModule\Tests\Integration\Service;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;
use OxidEsales\WysiwygModule\Service\Media;
use OxidEsales\WysiwygModule\Service\ModuleSettings;
use OxidEsales\WysiwygModule\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Tests\Integration\Service\MediaMock;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class MediaTest extends IntegrationTestCase
{
    private const FIXTURE_FILE = 'file.jpg';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $connection = self::getServiceFromContainer(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();
        $connection->executeStatement(
            'TRUNCATE ddmedia;'
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        $sFixturesImgPath = dirname(__FILE__) . '/../../fixtures/img/';
        $sTargetPath = Registry::getConfig()->getConfigParam('sShopDir') . '/tmp/';

        copy($sFixturesImgPath . 'image.jpg', $sTargetPath . 'image.jpg');
        copy($sFixturesImgPath . 'image.png', $sTargetPath . 'image.png');
        copy($sFixturesImgPath . 'image.gif', $sTargetPath . 'image.gif');
        copy($sFixturesImgPath . 'favicon.ico', $sTargetPath . 'favicon.ico');
    }

    /**
     * @dataProvider getUploadMediaDataProvider
     */
    public function testUploadMedia($imageName, $destFileName, $fileType)
    {
        $sut = $this->getSut();

        $sut->setFolder();

        $sSourcePath = Registry::getConfig()->getConfigParam('sShopDir') . 'tmp/' . $imageName;
        $sDestPath = $sut->getMediaPath() . $destFileName;
        $sFileSize = filesize($sSourcePath);
        $sFileType = $fileType;
        $blCreateThumbs = true;

        $sut->uploadMedia($sSourcePath, $sDestPath, $sFileSize, $sFileType, $blCreateThumbs);
        $this->assertTrue(file_exists($sDestPath));
    }

    /**
     * This test depends on the test testUploadMedia
     *
     * @return void
     */
    public function testFilesCount()
    {
        $sut = $this->getSut();
        $sut->setFolder();
        $this->assertEquals(5, $sut->getFileCount()); // 4 uploads and 1 folder
    }

    /**
     * This test depends on the test testUploadMedia
     *
     * @return void
     */
    public function testGetFiles()
    {
        $sut = $this->getSut();
        $sut->setFolder();
        $aFiles = $sut->getFiles();

        $this->assertGreaterThan(0, $this->count($aFiles));

        foreach ($aFiles as $aRow) {
            $aFilesResult[] = $aRow['DDFILENAME'];
        }

        $this->assertContains('file.jpg', $aFilesResult);
    }

    /**
     * This test depends on the test testUploadMedia
     *
     * @return void
     */
    public function testGenerateThumbnails()
    {
        $sut = $this->getSut();

        $sut->setFolder('');

        foreach (glob($sut->getMediaPath() . 'thumbs/*') as $file) {
            unlink($file);
        }
        $this->assertEquals(0, count(glob($sut->getMediaPath() . 'thumbs/*')));

        $sut->generateThumbnails();
        $this->assertGreaterThan(0, count(glob($sut->getMediaPath() . 'thumbs/*')));

        $sut->generateThumbnails(500);
        $this->assertGreaterThan(0, count(glob($sut->getMediaPath() . 'thumbs/*500.jpg')));
    }

    public function testCreateThumbnailException()
    {
        $sut = $this->getSut();

        $sut->setFolder('');

        $sSourcePath = Registry::getConfig()->getConfigParam('sShopDir') . 'tmp/favicon.ico';
        $sDestPath = $sut->getMediaPath() . 'favicon.ico';
        copy($sSourcePath, $sDestPath);

        $this->expectException(\Exception::class);
        $sut->createThumbnail('favicon.ico');
    }

    public function testFolder()
    {
        $sut = $this->getSut();

        $sut->setFolder('f256df3c2343b7e24ef5273c15f11e1b');
        $this->assertEquals('Folder1', $sut->getFolderName());
    }

    public function getUploadMediaDataProvider()
    {
        return [
            [
                'imageName'    => 'image.jpg',
                'destFileName' => self::FIXTURE_FILE,
                'fileType'     => 'image/jpeg',
            ],
            [
                'imageName'    => 'image.png',
                'destFileName' => 'image.png',
                'fileType'     => 'image/png',
            ],
            [
                'imageName'    => 'image.gif',
                'destFileName' => 'image.gif',
                'fileType'     => 'image/gif',
            ],
            [
                'imageName'    => 'image.gif',
                'destFileName' => 'image.gif',
                'fileType'     => 'image/gif',
            ],
        ];
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $sMediaPath = Registry::getConfig()->getConfigParam('sShopDir') . '/out/pictures/ddmedia/';
        $sMediaThumbPath = $sMediaPath . '/thumbs/';
        foreach (glob($sMediaPath . '*') as $file) {
            unlink($file);
        }
        foreach (glob($sMediaThumbPath . '*') as $file) {
            unlink($file);
        }

        $connection = self::getServiceFromContainer(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();
        $connection->executeStatement(
            'TRUNCATE ddmedia;'
        );
    }

    protected function getSut(
        ?ModuleSettings $moduleSettings = null,
        ?Config $shopConfig = null,
        ?ConnectionProviderInterface $connectionProvider = null,
        UtilsObject $utilsObject = null
    ) {
        return new \OxidEsales\WysiwygModule\Tests\Integration\Service\MediaMock(
            $moduleSettings ?: $this->getServiceFromContainer(ModuleSettings::class),
            $shopConfig ?: Registry::getConfig(),
            $connectionProvider ?: new ConnectionProvider(),
            $utilsObject ?: Registry::getUtilsObject()
        );
    }
}