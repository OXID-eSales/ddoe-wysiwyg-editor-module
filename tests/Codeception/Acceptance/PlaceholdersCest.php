<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\WysiwygModule\Tests\Codeception\Support\AcceptanceTester;

#[Group('ddoewysiwyg')]
final class PlaceholdersCest
{
    public function imageDataIdIsUsedForCreatingTheMediaPlaceholders(AcceptanceTester $I): void
    {
        $loadId = 'test_content';

        $imageId = uniqid();
        $contentValue = '<img src="hardcodedurl" data-source="media" data-id="' . $imageId . '" class="dd-wysiwyg-media-image">';
        $expectedContent = '<img src="{{oeMediaUrl(\'' . $imageId . '\')}}" data-source="media" data-id="' . $imageId . '" class="dd-wysiwyg-media-image">';

        $I->haveInDatabase('oxcontents', [
            'OXID' => md5($loadId),
            'OXLOADID' => $loadId,
            'OXCONTENT' => $contentValue,
            'OXCONTENT_1' => $contentValue,
            'OXCONTENT_2' => $contentValue,
            'OXCONTENT_' => $contentValue,
        ]);

        $adminPanel = $I->loginAdmin();
        $adminPanel->openCMSPages();

        $I->selectListFrame();
        $I->fillField("//input[@name='where[oxcontents][oxloadid]']", $loadId);
        $I->submitForm('#search', []);

        $I->selectListFrame();
        $I->click($loadId);

        $I->selectEditFrame();
        $I->waitForDocumentReadyState();

        $I->click("//input[@type='submit']");

        $I->wait(30);

        $I->seeInDatabase('oxcontents', [
            'OXID' => md5($loadId),
            'OXCONTENT' => $expectedContent,
        ]);
    }
}
