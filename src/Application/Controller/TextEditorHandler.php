<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
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

        $oConfig = $this->getConfig();
        $oLang = Registry::getLang();

        $iDynInterfaceLanguage = $oConfig->getConfigParam('iDynInterfaceLanguage');
        $sLangAbbr = $oLang->getLanguageAbbr(
            (isset($iDynInterfaceLanguage) ? $iDynInterfaceLanguage : $oLang->getTplLanguage())
        );

        /** @var TemplateRendererInterface $templateRenderer */
        $templateRenderer = ContainerFactory::getInstance()
            ->getContainer()
            ->get(TemplateRendererInterface::class);

        return $templateRenderer->renderTemplate('@ddoewysiwyg/ddoewysiwyg', [
            'oView' => $this->getView(),
            'oViewConf' => $this->getViewConfig(),
            'sEditorField' => $fieldName,
            'sEditorValue' => $objectValue,
            'iEditorHeight' => $height,
            'iEditorWidth' => $width,
            'blTextEditorDisabled' => $this->isTextEditorDisabled(),
            'langabbr' => $sLangAbbr
        ]);
    }

    /**
     * Config instance getter
     *
     * @return Config
     */
    public function getConfig()
    {
        return Registry::getConfig();
    }

    /**
     * Get active view
     *
     * @return object
     */
    public function getView()
    {
        return $this->getConfig()->getTopActiveView();
    }

    /**
     * Gets viewConfig object
     *
     * @return object
     */
    public function getViewConfig()
    {
        return $this->getView()->getViewConfig();
    }
}
