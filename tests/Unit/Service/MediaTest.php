<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\VisualCmsModule\Tests\Unit\Service;

use Doctrine\DBAL\Connection;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use OxidEsales\WysiwygModule\Service\Media;
use OxidEsales\WysiwygModule\Service\ModuleSettings;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    private const FIXTURE_FILE = 'file.jpg';
    private const FIXTURE_FOLDER = 'some_folder';

    /**
     * @dataProvider getImagePathDataProvider
     */
    public function testGetMediaPathNoAlternativeUrl($file)
    {
        $moduleSettingsMock = $this->createConfiguredMock(ModuleSettings::class, [
            'getAlternativeImageDirectory' => '',
        ]);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->with('sShopDir')
            ->willReturn('someShopDir/');

        $sut = $this->getSut($moduleSettingsMock, $shopConfigMock);
        $mediaPath = $sut->getMediaPath($file);

        $this->assertSame('someShopDir' . Media::MEDIA_PATH . $file, $mediaPath);
    }

    /**
     * @dataProvider getImagePathDataProvider
     */
    public function testGetMediaPathWithAlternativeUrl($file)
    {
        $externalUrl = 'https://some-cdn-url.com';
        $moduleSettingsMock = $this->createConfiguredMock(ModuleSettings::class, [
            'getAlternativeImageDirectory' => $externalUrl,
        ]);

        $sut = $this->getSut($moduleSettingsMock);
        $mediaPath = $sut->getMediaPath($file);

        $this->assertSame($externalUrl . Media::MEDIA_PATH_SHORT . $file, $mediaPath);
    }

    /**
     * @dataProvider getImagePathDataProvider
     */
    public function testGetMediaUrlNoAlternativeUrl($file)
    {
        $externalUrl = 'https://some-cdn-url.com';
        $aFilepath = explode('/', $file);
        if (count($aFilepath) > 1) {
            $filename = [
                $aFilepath[0] => [
                    $aFilepath[1] => 'file content',
                ],
            ];
        } else {
            $filename = [
                $aFilepath[0] => 'file content',
            ];
        }
        $structure = [
            'out' => [
                'pictures' => [
                    'ddmedia' => $filename,
                ],
            ],
        ];
        $directory = vfsStream::setup('root', 0777, $structure);

        $moduleSettingsMock = $this->createConfiguredMock(ModuleSettings::class, [
            'getAlternativeImageDirectory' => '',
        ]);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam', 'getSslShopUrl']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, $directory->url()],
            ]);
        $shopConfigMock->expects($this->any())
            ->method('getSslShopUrl')
            ->willReturn($externalUrl);

        $sut = $this->getSut($moduleSettingsMock, $shopConfigMock);

        $this->assertSame($externalUrl . Media::MEDIA_PATH . $file, $sut->getMediaUrl($file));
    }

    /**
     * @dataProvider getImagePathDataProvider
     */
    public function testGetMediaUrlNotExistingFile($file)
    {
        $moduleSettingsMock = $this->createConfiguredMock(ModuleSettings::class, [
            'getAlternativeImageDirectory' => '',
        ]);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, ''],
            ]);

        $sut = $this->getSut($moduleSettingsMock, $shopConfigMock);

        $this->assertSame(false, $sut->getMediaUrl($file));
    }

    /**
     * @dataProvider getImagePathDataProvider
     */
    public function testGetMediaUrlWithAlternativeUrl($file)
    {
        $externalUrl = 'https://some-cdn-url.com';
        $moduleSettingsMock = $this->createConfiguredMock(ModuleSettings::class, [
            'getAlternativeImageDirectory' => $externalUrl,
        ]);

        $sut = $this->getSut($moduleSettingsMock);
        $mediaPath = $sut->getMediaUrl($file);

        $this->assertSame($externalUrl . Media::MEDIA_PATH_SHORT . $file, $mediaPath);
    }

    /**
     * @dataProvider getThumbnailPathDataProvider
     */
    public function testGetThumbnailPath($file, $expectedPath)
    {
        $path = "somePath";

        $sut = $this->createPartialMock(Media::class, ["getMediaPath"]);
        $sut->expects($this->any())->method("getMediaPath")->willReturn($path);

        $this->assertSame($expectedPath, $sut->getThumbnailPath($file));
    }

    public function testCreateFolderWithNewName()
    {
        $structure = [
            'out' => [
                'pictures' => [
                    'ddmedia' => [],
                ],
            ],
        ];
        $directory = vfsStream::setup('root', 0777, $structure);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, $directory->url()],
            ]);

        $connectionMock = $this->createPartialMock(Connection::class, ['executeQuery']);
        $connectionMock->expects($this->once())
            ->method('executeQuery');
        $connectionProviderStub = $this->createConfiguredMock(ConnectionProviderInterface::class, [
            'get' => $connectionMock,
        ]);

        $sId = md5('FolderTest');
        $utilsObjectMock = $this->createPartialMock(UtilsObject::class, ['generateUId']);
        $utilsObjectMock->expects($this->once())
            ->method('generateUId')
            ->willReturn($sId);

        $sut = $this->getSut(null, $shopConfigMock, $connectionProviderStub, $utilsObjectMock);
        $aCustomDir = $sut->createCustomDir('FolderTest', '');

        $aExpected = ['id' => $sId, 'dir' => 'FolderTest'];

        $this->assertSame($aExpected, $aCustomDir);
    }

    public function testCreateFolderWithExistingName()
    {
        $structure = [
            'out' => [
                'pictures' => [
                    'ddmedia' => [
                        'FolderTest' => [],
                    ],
                ],
            ],
        ];
        $directory = vfsStream::setup('root', 0777, $structure);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, $directory->url()],
            ]);

        $connectionMock = $this->createPartialMock(Connection::class, ['executeQuery']);
        $connectionMock->expects($this->once())
            ->method('executeQuery');
        $connectionProviderStub = $this->createConfiguredMock(ConnectionProviderInterface::class, [
            'get' => $connectionMock,
        ]);

        $sId = md5('FolderTest_1');
        $utilsObjectMock = $this->createPartialMock(UtilsObject::class, ['generateUId']);
        $utilsObjectMock->expects($this->once())
            ->method('generateUId')
            ->willReturn($sId);

        $sut = $this->getSut(null, $shopConfigMock, $connectionProviderStub, $utilsObjectMock);
        $aCustomDir = $sut->createCustomDir('FolderTest', '');

        $aExpected = ['id' => $sId, 'dir' => 'FolderTest_1'];

        $this->assertSame($aExpected, $aCustomDir);
    }

    /**
     * @dataProvider getRenameDataProvider
     */
    public function testRename($structure, $oldName, $newName, $structureExpected, $folder)
    {
        $directory = vfsStream::setup('root', 0777, $structure);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, $directory->url()],
            ]);

        $connectionMock = $this->createPartialMock(Connection::class, ['executeQuery']);
        $connectionMock->expects($this->once())
            ->method('executeQuery');
        $connectionProviderStub = $this->createConfiguredMock(ConnectionProviderInterface::class, [
            'get' => $connectionMock,
        ]);

        $sut = $this->getSut(null, $shopConfigMock, $connectionProviderStub);
        if ($folder) {
            $sut->setFolderName($folder);
        }
        $success = $sut->rename($oldName, $newName, '');

        $this->assertEquals(true, $success);

        $this->assertEquals(
            $structureExpected,
            vfsStream::inspect(new vfsStreamStructureVisitor(), $directory)->getStructure()
        );
    }

    public function testMoveFile()
    {
        $sTargetFolderName = 'new_folder';
        $sTargetFolderID = '9999';
        $sSourceFileName = self::FIXTURE_FILE;
        $sSourceFileID = '111';
        $sThumbName = '111_thumb_' . $this->getSut()->getDefaultThumbnailSize() . '.jpg';

        $structure['out']['pictures']['ddmedia'][$sSourceFileName] = 'some file';
        $structure['out']['pictures']['ddmedia']['thumbs'][$sThumbName] = 'some file';
        $structure['out']['pictures']['ddmedia'][$sTargetFolderName] = [];
        $directory = vfsStream::setup('root', 0777, $structure);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, $directory->url()],
            ]);

        $sSelect = "SELECT DDFILENAME FROM ddmedia WHERE OXID = ?";
        $connectionMock = $this->createPartialMock(Connection::class, ['fetchOne', 'fetchAllAssociative', 'executeQuery']);
        $connectionMock->expects($this->exactly(1))
            ->method('fetchOne')
            ->willReturn($sTargetFolderName);

        $connectionMock->expects($this->exactly(1))
            ->method('fetchAllAssociative')
            ->willReturn([
                0 => [
                    'DDFILENAME' => $sSourceFileName,
                    'DDTHUMB' => $sThumbName,
                ]
            ]);

        $connectionMock->expects($this->once())
            ->method('executeQuery');

        $connectionProviderStub = $this->createConfiguredMock(ConnectionProviderInterface::class, [
            'get' => $connectionMock,
        ]);

        $sut = $this->getSut(null, $shopConfigMock, $connectionProviderStub);

        $sut->moveFileToFolder($sSourceFileID, $sTargetFolderID, $sThumbName);

        $structureExpected['root'] = $structure;
        unset($structureExpected['root']['out']['pictures']['ddmedia'][$sSourceFileName]);
        unset($structureExpected['root']['out']['pictures']['ddmedia']['thumbs'][$sThumbName]);
        $structureExpected['root']['out']['pictures']['ddmedia'][$sTargetFolderName] = [
            $sSourceFileName => 'some file',
            'thumbs'         => [
                $sThumbName => 'some file',
            ],
        ];

        $this->assertEquals(
            $structureExpected,
            vfsStream::inspect(new vfsStreamStructureVisitor(), $directory)->getStructure()
        );
    }

    /**
     * @dataProvider getDeleteDataProvider
     * @return void
     */
    public function testDelete($structure, $structureExpected, $aIds, $aDBData, $startFolder)
    {
        $directory = vfsStream::setup('root', 0777, $structure);

        $shopConfigMock = $this->createPartialMock(Config::class, ['getConfigParam']);
        $shopConfigMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturnMap([
                ['sShopDir', null, $directory->url()],
            ]);

        $connectionMock = $this->createPartialMock(Connection::class, [
                'fetchAllAssociative',
                'executeQuery',
                'quote',
            ]
        );
        $connectionMock->expects($this->once())
            ->method('fetchAllAssociative')
            ->willReturn($aDBData);

        $connectionMock->expects($this->any())
            ->method('executeQuery');

        $connectionMock->expects($this->any())
            ->method('quote');

        $connectionProviderStub = $this->createConfiguredMock(ConnectionProviderInterface::class, [
            'get' => $connectionMock,
        ]);

        $sut = $this->getSut(null, $shopConfigMock, $connectionProviderStub);
        if ($startFolder) {
            $sut->setFolderName($startFolder);
        }
        $sut->delete($aIds);

        $this->assertEquals(
            $structureExpected,
            vfsStream::inspect(new vfsStreamStructureVisitor(), $directory)->getStructure()
        );
    }

    /*public function testDeleteFolderWithImages()
    {
    }*/


    public function getThumbnailPathDataProvider(): array
    {
        return [
            [
                'file'         => 'somefile.jpg',
                'expectedPath' => 'somePath/thumbs/somefile.jpg',
            ],
            [
                'file'         => '',
                'expectedPath' => 'somePath/thumbs',
            ],
        ];
    }

    public function getImagePathDataProvider(): array
    {
        return [
            [
                'file' => self::FIXTURE_FILE,
            ],
            [
                'file' => self::FIXTURE_FOLDER . '/' . self::FIXTURE_FILE,
            ],
        ];
    }

    public function getRenameDataProvider(): array
    {
        $structure['out']['pictures']['ddmedia'][self::FIXTURE_FILE] = 'some file';
        $structureExpected['root']['out']['pictures']['ddmedia']['new.jpg'] = 'some file';

        $structure1['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER][self::FIXTURE_FILE] = 'some file';
        $structureExpected1['root']['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER]['new.jpg'] = 'some file';

        $structure2['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER] = [];
        $structureExpected2['root']['out']['pictures']['ddmedia']['folderNew'] = [];

        // todo: check if renaming is stopped if images in folder are already in use

        return [
            [
                'structure'         => $structure,
                'oldName'           => self::FIXTURE_FILE,
                'newName'           => 'new.jpg',
                'structureExpected' => $structureExpected,
                'folder'            => '',
            ],
            [
                'structure'         => $structure1,
                'oldName'           => self::FIXTURE_FILE,
                'newName'           => 'new.jpg',
                'structureExpected' => $structureExpected1,
                'folder'            => self::FIXTURE_FOLDER,
            ],
            [
                'structure'         => $structure2,
                'oldName'           => self::FIXTURE_FOLDER,
                'newName'           => 'folderNew',
                'structureExpected' => $structureExpected2,
                'folder'            => '',
            ],
        ];
    }

    public function getDeleteDataProvider()
    {
        $oMedia = new Media(
            $this->createStub(ModuleSettings::class),
            $this->createStub(Config::class),
            $this->createStub(ConnectionProviderInterface::class),
            $this->createStub(UtilsObject::class)
        );
        $sThumbName = '111_thumb_' . $oMedia->getDefaultThumbnailSize() . '.jpg';

        // scenario 1 - file in media root
        $structure['out']['pictures']['ddmedia'][self::FIXTURE_FILE] = 'some file';
        $structure['out']['pictures']['ddmedia']['thumbs'][$sThumbName] = 'some file';
        $structureExpected['root']['out']['pictures']['ddmedia']['thumbs'] = [];
        $aIds = ['111'];
        $aDBData[] = [
            'OXID'       => '111',
            'DDFILENAME' => self::FIXTURE_FILE,
            'DDTHUMB'    => $sThumbName,
            'DDFILETYPE' => 'image/jpeg',
            'DDFOLDERID' => '',
        ];

        // scenario 2 - file in a folder
        $structure1['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER][self::FIXTURE_FILE] = 'some file';
        $structure1['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER]['thumbs'][$sThumbName] = 'some file';
        $structureExpected1['root']['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER]['thumbs'] = [];
        $aIds1 = ['111'];
        $aDBData1[] = [
            'OXID'       => '111',
            'DDFILENAME' => self::FIXTURE_FILE,
            'DDTHUMB'    => $sThumbName,
            'DDFILETYPE' => 'image/jpeg',
            'DDFOLDERID' => '2222',
        ];

        // scenario 3 - empty folder
        $structure2['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER] = [];
        $structure2['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER]['thumbs'] = [];
        $structureExpected2['root']['out']['pictures']['ddmedia'] = [];
        $aIds2 = ['111'];
        $aDBData2[] = [
            'OXID'       => '111',
            'DDFILENAME' => self::FIXTURE_FOLDER,
            'DDTHUMB'    => '',
            'DDFILETYPE' => 'directory',
            'DDFOLDERID' => '',
        ];

        // scenario 4 - folder with files
        $structure3['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER][self::FIXTURE_FILE] = 'some file';
        $structure3['out']['pictures']['ddmedia'][self::FIXTURE_FOLDER]['thumbs'][$sThumbName] = 'some file';
        $structureExpected3['root']['out']['pictures']['ddmedia'] = [];
        $aIds3 = ['111'];
        $aDBData3[] = [
            'OXID'       => '111',
            'DDFILENAME' => self::FIXTURE_FOLDER,
            'DDTHUMB'    => '',
            'DDFILETYPE' => 'directory',
            'DDFOLDERID' => '',
        ];
        $aDBData3[] = [
            'OXID'       => '222',
            'DDFILENAME' => self::FIXTURE_FILE,
            'DDTHUMB'    => $sThumbName,
            'DDFILETYPE' => 'image/jpeg',
            'DDFOLDERID' => '111',
        ];

        return [
            [
                'structure'         => $structure,
                'structureExpected' => $structureExpected,
                'aIds'              => $aIds,
                'aDBData'           => $aDBData,
                'startFolder'       => '',
            ],
            [
                'structure'         => $structure1,
                'structureExpected' => $structureExpected1,
                'aIds'              => $aIds1,
                'aDBData'           => $aDBData1,
                'startFolder'       => self::FIXTURE_FOLDER,
            ],
            [
                'structure'         => $structure2,
                'structureExpected' => $structureExpected2,
                'aIds'              => $aIds2,
                'aDBData'           => $aDBData2,
                'startFolder'       => '',
            ],
            [
                'structure'         => $structure3,
                'structureExpected' => $structureExpected3,
                'aIds'              => $aIds3,
                'aDBData'           => $aDBData3,
                'startFolder'       => '',
            ],
        ];
    }

    protected function getSut(
        ?ModuleSettings $moduleSettings = null,
        ?Config $shopConfig = null,
        ?ConnectionProviderInterface $connectionProvider = null,
        UtilsObject $utilsObject = null
    ) {
        return new Media(
            $moduleSettings ?: $this->createStub(ModuleSettings::class),
            $shopConfig ?: $this->createStub(Config::class),
            $connectionProvider ?: $this->createStub(ConnectionProviderInterface::class),
            $utilsObject ?: $this->createStub(UtilsObject::class)
        );
    }
}
