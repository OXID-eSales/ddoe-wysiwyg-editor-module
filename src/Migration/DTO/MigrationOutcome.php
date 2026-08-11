<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

enum MigrationOutcome: string
{
    case Converted = 'converted';
    case Failed = 'failed';
}
