<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            // Legacy MUtil Framework aliases
            'aliases'    => [
                '\MUtil\View\Helper\BaseUrl' => BaseUrl::class,
            ],
            'invokables' => [
                BaseUrl::class => BaseUrlFactory::class,
                RedirectorInterface::class => BasicRedirectorFactory::class
            ],
        ];
    }

}