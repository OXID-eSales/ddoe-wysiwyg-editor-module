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

    public function summernoteLinkDialogShowsCmsIdentField(AcceptanceTester $I): void
    {
        $I->wantToTest('Summernote link dialog shows the custom CMS-Ident field');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openProducts();
        $I->selectEditFrame();

        $I->waitForElement('.note-editor', 15);
        $I->wait(3);

        $linkButton = "(//div[contains(@class,'note-editor')]//button[.//i[contains(@class,'note-icon-link')]])[1]";
        $I->waitForElementClickable($linkButton, 5);
        $I->click($linkButton);
        $I->wait(1);

        $I->waitForElement('.link-dialog .note-link-cms', 5);
        $I->seeElement('.link-dialog .note-link-cms');
    }

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

    public function cmsIdentTwigExpressionIsPreservedUnencoded(AcceptanceTester $I): void
    {
        $I->wantToTest('CMS-Ident seo_url expression survives the editor filter unencoded');

        $loadId = 'twig_preserve_test';
        $template = '<p><a href="{{ seo_url({type: \'oxcontent\', ident: \'oxnewstlerinfo\'}) }}">news</a></p>';

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
        $I->waitForElement('.note-editable', 15);

        $editorHtml = $I->executeJS("return document.querySelector('.note-editable').innerHTML;");
        $I->assertStringContainsString('seo_url', $editorHtml);
        $I->assertStringNotContainsString('%7B', $editorHtml);
    }
}
