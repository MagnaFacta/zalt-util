<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

/**
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class MockTranslator implements \Zalt\Base\TranslatorInterface
{
    /**
     * @inheritDoc
     */
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $id;
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return 'en';
    }

    public function _(?string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $id ?? '';
    }

    public function plural(string $singular, string $plural, int $number, ?string $locale = null): string
    {
        return $singular;
    }
}