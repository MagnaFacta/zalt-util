<?php

namespace Zalt\Base;

use Symfony\Component\Translation\Translator;

class SymfonyTranslator implements TranslatorInterface, \Laminas\Validator\Translator\TranslatorInterface
{
    public function __construct(protected readonly Translator $translator)
    {}

    public function _(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
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

    public function setLocale(string $locale): void
    {
        $this->translator->setLocale($locale);
    }

    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function translate($message, $textDomain = 'default', $locale = null)
    {
        if ($textDomain === 'default') {
            return $this->translator->trans($message, [], null, $locale);
        }

        return $this->translator->trans($message, [], $textDomain, $locale);
    }
}