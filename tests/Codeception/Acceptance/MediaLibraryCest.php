<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Codeception\Acceptance;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\WysiwygModule\Tests\Codeception\AcceptanceTester;

/**
 * @group ddoe_wysiwyg
 */
final class MediaLibraryCest
{
    public function testMediaLibraryAvailable(AcceptanceTester $I): void
    {
        $I->wantToTest('Media library available and accessible');

        $I->loginAdmin();

        $I->selectNavigationFrame();
        $I->retryClick(Translator::translate('mxcustnews'));
        $I->retryClick(Translator::translate('DD_MEDIA_DIALOG'));

        $I->selectBaseFrame();

        $I->see(Translator::translate('DD_MEDIA_DIALOG'));
        $I->see(Translator::translate('DD_MEDIA_LIST'));
        $I->see(Translator::translate('DD_MEDIA_UPLOAD'));
    }
}
