<?php

declare(strict_types=1);

/**
 *
 * 
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Mock object implementing the Symfony\Contracts\Translation\TranslatorInterface 
 * 
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.9.2
 */
class PotemkinTranslator implements TranslatorInterface
{
    /**
     * @inheritDoc
     */
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null) : string
    {
        return $id;
    }

    /**
     * @inheritDoc
     */
    public function getLocale() : string
    {
        return 'en';
    }
}