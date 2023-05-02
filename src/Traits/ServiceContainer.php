<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Traits;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;

/**
 * Convenience trait to fetch services from DI container.
 * Use for example in classes where it's not possible to inject services in
 * the constructor because constructor is inherited from a shop core class.
 * Example: see module controllers
 */
trait ServiceContainer
{
    /**
     * @template T
     * @psalm-param class-string<T> $serviceName
     *
     * @return T
     */
    public function getServiceFromContainer(string $serviceName)
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get($serviceName);
    }
}
