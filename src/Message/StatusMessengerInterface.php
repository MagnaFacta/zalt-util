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

    /**
     * Add a message with a specific status
     *
     * @param string $message
     * @param MessageStatus $status
     * @param bool $now
     * @return void
     */
    public function addMessage(string $message, MessageStatus $status=MessageStatus::Info, bool $now = false): void;

    /**
     * Add multiple messages with a specific status
     *
     * @param array $messages
     * @param MessageStatus $status
     * @param bool $now
     * @return void
     */
    public function addMessages(array $messages, MessageStatus $status=MessageStatus::Info, bool $now = false): void;

    public function addSuccess(string $message, bool $now = false): void;

    public function addSuccesses(array $messages, bool $now = false): void;

    public function addWarning(string $message, bool $now = false): void;

    public function addWarnings(array $messages, bool $now = false): void;

    /**
     * Clear the current messages
     *
     * @param bool $now Should the messages also be cleared for the current request
     * @return void
     */
    public function clearMessages(bool $now = true): void;

    /**
     * Get all messages
     *
     * @param MessageStatus|null $status
     * @return array
     */
    public function getMessages(?MessageStatus $status = null): array;

    /**
     * Keep ALL flash messages for an additional hop
     *
     * @return void
     */
    public function prolong(): void;
}