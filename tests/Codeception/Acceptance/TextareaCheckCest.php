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
    public function productDescriptionTextAreaModified(AcceptanceTester $I): void
    {
        $I->wantToTest('Module improves the product description textarea');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openProducts();
        $I->selectEditFrame();

        $I->seeElementInDOM("#ddoew #editor_oxarticles__oxlongdesc");
    }

    public function contentIsFiltered(AcceptanceTester $I): void
    {
        $loadId = 'test_content';
        $template = "<p>par 1</p><script>var filterTest = 'test';</script><p>par 2</p>";

        $I->haveInDatabase('oxcontents', [
            'OXID' => md5($loadId),
            'OXLOADID' => $loadId,
            'OXCONTENT' => $template,
            'OXCONTENT_1' => $template,
            'OXCONTENT_2' => $template,
            'OXCONTENT_3' => $template,
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

        $isVarDefined = $I->executeJS("return typeof filterTest !== 'undefined'");
        $I->assertFalse($isVarDefined);
    }
}
