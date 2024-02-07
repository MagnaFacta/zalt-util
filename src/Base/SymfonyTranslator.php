<?php

namespace Zalt\Base;

use Symfony\Component\Translation\Translator;

class SymfonyTranslator implements TranslatorInterface
{
    public function __construct(protected readonly Translator $translator)
    {}

    public function _(?string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    public function plural(string $singular, string $plural, int $number, ?string $locale = null): string
    {
        $message = $singular . '|' . $plural;
        $parameters = [
            '%count%' => $number,
        ];
        return $this->translator->trans($message, $parameters, null, $locale);
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}