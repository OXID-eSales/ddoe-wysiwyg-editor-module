<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Core;

/**
 * Class defines what module does on Shop events.
 */
class Events
{
    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        // Migrations are no longer executed on activation, use oe:database:migrate
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
    }
}
