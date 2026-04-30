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
    public function summernoteFontSizeDropdownWorks(AcceptanceTester $I): void
    {
        $I->wantToTest('Summernote font size dropdown opens when clicked');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openProducts();
        $I->selectEditFrame();

        $I->waitForElement('.note-editor', 15);
        $I->wait(3);

        $fontSizeDropdownButton = '.note-toolbar .note-fontsize button.dropdown-toggle';
        $I->waitForElementClickable($fontSizeDropdownButton, 5);

        $I->click($fontSizeDropdownButton);
        $I->wait(1);

        $I->seeElement('.note-toolbar .note-fontsize .dropdown-menu.show');
    }

    public function editorFiltersContent(AcceptanceTester $I): void
    {
        $I->wantToTest('Editor normalizes content when switching from code view to preview');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openProducts();
        $I->selectEditFrame();

        $I->waitForElement('.note-editor', 15);
        $I->wait(2);

        $codeviewButton = '.note-toolbar .btn-codeview';
        $I->waitForElementClickable($codeviewButton);
        $I->click($codeviewButton);
        $I->waitForElement('.note-codable');

        $I->fillField('.note-codable', '<img src="x" onerror="window.handlerCalled=true">');

        $I->click($codeviewButton);
        $I->wait(2);

        $handlerCalled = $I->executeJS('return window.handlerCalled === true');
        $I->assertFalse($handlerCalled);
    }

    public function productDescriptionTextAreaModified(AcceptanceTester $I): void
    {
        $I->wantToTest('Module improves the product description textarea');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openProducts();
        $I->selectEditFrame();

        $I->seeElementInDOM("#ddoew #editor_oxarticles__oxlongdesc");
    }

    public function serverFiltersContent(AcceptanceTester $I): void
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
