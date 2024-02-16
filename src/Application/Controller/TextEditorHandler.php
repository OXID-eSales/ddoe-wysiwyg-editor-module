<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

/**
 * Class TextEditorHandler
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\TextEditorHandler
 */
class TextEditorHandler extends TextEditorHandler_parent
{
    /**
     * Render text editor.
     *
     * @param int $width The editor width
     * @param int $height The editor height
     * @param object $objectValue The object value passed to editor
     * @param string $fieldName The name of object field which content is passed to editor
     *
     * @return string The Editor output
     */
    public function renderRichTextEditor($width, $height, $objectValue, $fieldName)
    {
        if (strpos($width, '%') === false) {
            $width .= 'px';
        }

        if (strpos($height, '%') === false) {
            $height .= 'px';
        }

        $oConfig = Registry::getConfig();
        $oLang = Registry::getLang();

        $iDynInterfaceLanguage = $oConfig->getConfigParam('iDynInterfaceLanguage');
        $sLangAbbr = $oLang->getLanguageAbbr(
            (isset($iDynInterfaceLanguage) ? $iDynInterfaceLanguage : $oLang->getTplLanguage())
        );

        $templateRenderer = ContainerFacade::get(TemplateRendererInterface::class);
        return $templateRenderer->renderTemplate('@ddoewysiwyg/ddoewysiwyg', [
            'oViewConf' => $oConfig->getTopActiveView()->getViewConfig(),
            'sEditorField' => $fieldName,
            'sEditorValue' => $objectValue,
            'iEditorHeight' => $height,
            'iEditorWidth' => $width,
            'blTextEditorDisabled' => $this->isTextEditorDisabled(),
            'langabbr' => $sLangAbbr,
            'isSSL' => ($oConfig->getConfigParam('sSSLShopURL') || $oConfig->getConfigParam('sMallSSLShopURL')) ? 1 : 0,
        ]);
    }
}
