<?php

namespace Zalt\Message;

trait StatusMessengerTrait
{
    public function addDanger(string $message): void
    {
        $this->addMessages([$message], MessageStatus::Danger);
    }

    public function addDangers(array $messages): void
    {
        $this->addMessages($messages, MessageStatus::Danger);
    }

    public function addError(string $message): void
    {
        $this->addMessages([$message], MessageStatus::Danger);
    }

    public function addErrors(array $messages): void
    {
        $this->addMessages($messages, MessageStatus::Danger);
    }

    public function addInfo(string $message): void
    {
        $this->addMessages([$message], MessageStatus::Info);
    }

    public function addInfos(array $messages): void
    {
        $this->addMessages($messages, MessageStatus::Info);
    }

    public function addSuccess(string $message): void
    {
        $this->addMessages([$message], MessageStatus::Success);
    }

    public function addSuccesses(array $messages): void
    {
        $this->addMessages($messages, MessageStatus::Success);
    }

    public function addWarning(string $message): void
    {
        $this->addMessages([$message], MessageStatus::Warning);
    }

    public function addWarnings(array $messages): void
    {
        $this->addMessages($messages, MessageStatus::Warning);
    }
}