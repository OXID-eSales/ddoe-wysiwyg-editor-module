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

class MediaTest extends IntegrationTestCase
{
    private const FIXTURE_FILE = 'file.jpg';
    private const FIXTURE_FOLDER = 'some_folder';

    public function setUp(): void
    {
        parent::setUp();

        copy(
            dirname(__FILE__) . '/../../fixtures/img/image.jpg',
            Registry::getConfig()->getConfigParam('sShopDir') .
            '/tmp/image.jpg'
        );
    }

    public function testUploadMedia()
    {
        $sut = $this->getSut();

        $sut->setFolder();

        $sSourcePath = Registry::getConfig()->getConfigParam('sShopDir') . '/tmp/image.jpg';
        $sDestPath = $sut->getMediaPath() . self::FIXTURE_FILE;
        $sFileSize = filesize($sSourcePath);
        $sFileType = 'image/jpeg';
        $blCreateThumbs = true;
        $sut->uploadMedia($sSourcePath, $sDestPath, $sFileSize, $sFileType, $blCreateThumbs);

        $this->assertTrue(file_exists($sDestPath));
    }

    public function testGenerateThumbnails()
    {
        $sut = $this->getSut();

        $sut->setFolder();

        $sut->generateThumbnails(500);

        echo $sut->getMediaPath() . 'thumbs/*500.jpg' . PHP_EOL;

        $this->assertGreaterThan(0, count(glob($sut->getMediaPath() . 'thumbs/*500.jpg')));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $sMediaPath = Registry::getConfig()->getConfigParam('sShopDir') . '/out/pictures/ddmedia/';
        $sMediaThumbPath = $sMediaPath . '/thumbs/';
        foreach (glob($sMediaPath . '*.jpg') as $file) {
            unlink($file);
        }
        foreach (glob($sMediaThumbPath . '*.jpg') as $file) {
            unlink($file);
        }
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