<?php

namespace Zalt\Message;

interface StatusMessengerInterface extends MessengerInterface
{
    public function addDanger(string $message, bool $now = false): void;

    public function addDangers(array $messages, bool $now = false): void;

    public function addError(string $message, bool $now = false): void;

    public function addErrors(array $messages, bool $now = false): void;

    public function addInfo(string $message, bool $now = false): void;

    public function addInfos(array $messages, bool $now = false): void;

    public function addMessage(string $message, MessageStatus $status=MessageStatus::Info, bool $now = false): void;

    public function addMessages(array $messages, MessageStatus $status=MessageStatus::Info, bool $now = false): void;

    public function addSuccess(string $message, bool $now = false): void;

    public function addSuccesses(array $messages, bool $now = false): void;

    public function addWarning(string $message, bool $now = false): void;

    public function addWarnings(array $messages, bool $now = false): void;
}