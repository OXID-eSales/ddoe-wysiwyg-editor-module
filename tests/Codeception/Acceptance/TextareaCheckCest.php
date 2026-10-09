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
        $this->haveCmsContent($I, $loadId, "<p>par 1</p><script>var filterTest = 'test';</script><p>par 2</p>");

        $this->openCmsContentInEditor($I, $loadId);

        $isVarDefined = $I->executeJS("return typeof filterTest !== 'undefined'");
        $I->assertFalse($isVarDefined);
    }

    public function editorPreservesLeadingStyleTag(AcceptanceTester $I): void
    {
        $I->wantToTest('Leading style tag is kept on save');

        $loadId = uniqid('style_preserve_');
        $styleTag = $this->createStyleTag();
        $this->haveCmsContent($I, $loadId, $styleTag . '<p>styled content</p>');

        $this->openCmsContentInEditor($I, $loadId);
        $I->click("//input[@type='submit']");

        $savedContent = $I->grabFromDatabase('oxcontents', 'OXCONTENT', ['OXID' => md5($loadId)]);
        $I->assertStringContainsString($styleTag, $savedContent);
    }

    public function codeviewPreservesStyleTag(AcceptanceTester $I): void
    {
        $I->wantToTest('Style tag entered in code view is kept on save');

        $loadId = uniqid('style_codeview_');
        $styleTag = $this->createStyleTag();
        $this->haveCmsContent($I, $loadId, '<p>initial content</p>');

        $this->openCmsContentInEditor($I, $loadId);

        $codeviewButton = '.note-toolbar .btn-codeview';
        $I->waitForElementClickable($codeviewButton);
        $I->click($codeviewButton);
        $I->waitForElement('.note-codable');

        $I->fillField('.note-codable', '<p>intro</p>' . $styleTag . '<p>outro</p>');

        $I->click($codeviewButton);
        $I->waitForElementNotVisible('.note-codable');

        $I->click("//input[@type='submit']");

        $savedContent = $I->grabFromDatabase('oxcontents', 'OXCONTENT', ['OXID' => md5($loadId)]);
        $I->assertStringContainsString($styleTag, $savedContent);
    }

    public function cmsIdentTwigExpressionIsPreservedUnencoded(AcceptanceTester $I): void
    {
        $I->wantToTest('CMS-Ident seo_url expression survives the editor filter unencoded');

        $loadId = 'twig_preserve_test';
        $content = '<p><a href="{{ seo_url({type: \'oxcontent\', ident: \'oxnewstlerinfo\'}) }}">news</a></p>';
        $this->haveCmsContent($I, $loadId, $content);

        $this->openCmsContentInEditor($I, $loadId);

        $editorHtml = $I->executeJS("return document.querySelector('.note-editable').innerHTML;");
        $I->assertStringContainsString('seo_url', $editorHtml);
        $I->assertStringNotContainsString('%7B', $editorHtml);
    }

    private function createStyleTag(): string
    {
        return '<style>.' . uniqid('class_') . ' { color: red; }</style>';
    }

    private function haveCmsContent(AcceptanceTester $I, string $loadId, string $content): void
    {
        $I->haveInDatabase('oxcontents', [
            'OXID' => md5($loadId),
            'OXLOADID' => $loadId,
            'OXCONTENT' => $content,
            'OXCONTENT_1' => $content,
            'OXCONTENT_2' => $content,
            'OXCONTENT_3' => $content,
        ]);
    }

    private function openCmsContentInEditor(AcceptanceTester $I, string $loadId): void
    {
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
    }
}
