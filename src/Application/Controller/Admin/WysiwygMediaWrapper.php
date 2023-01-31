<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class WysiwygMediaWrapper
 */
class WysiwygMediaWrapper extends WysiwygMedia
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = '@ddoewysiwyg/dialog/ddoewysiwygmedia_wrapper';

    public function init()
    {
        $request = Registry::getRequest();

        $this->_aViewData["oConf"] = Registry::getConfig();
        $this->_aViewData["request"]["overlay"] = $request->getRequestParameter('overlay') ?: 0;
        $this->_aViewData["request"]["popout"] = $request->getRequestParameter('popout') ?: 0;

        parent::init();
    }
}
