<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\WysiwygModule\Tests\Codeception\Support\AcceptanceTester;

#[Group('ddoewysiwyg')]
final class TextareaCheckCest
{
    public function testCmsTextAreaModified(AcceptanceTester $I): void
    {
        $I->wantToTest('Module improves the cms pages textarea');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openCMSPages();
        $I->selectEditFrame();

        $I->seeElementInDOM("#ddoew #editor_oxcontents__oxcontent");
    }
}
