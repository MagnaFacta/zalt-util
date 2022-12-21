<?php

namespace Zalt\Message;

interface StatusMessengerInterface extends MessengerInterface
{
    public function addDanger(string $message): void;

    public function addDangers(array $messages): void;

    public function addError(string $message): void;

    public function addErrors(array $messages): void;

    public function addInfo(string $message): void;

    public function addInfos(array $messages): void;

    /**
     * Add a message with a specific status
     *
     * @param string $message
     * @param MessageStatus $status
     * @param bool $now
     * @return void
     */
    public function addMessage(string $message, MessageStatus $status=MessageStatus::Info): void;

    /**
     * Add multiple messages with a specific status
     *
     * @param array $messages
     * @param MessageStatus $status
     * @param bool $now
     * @return void
     */
    public function addMessages(array $messages, MessageStatus $status=MessageStatus::Info): void;

    public function addSuccess(string $message): void;

    public function addSuccesses(array $messages): void;

    public function addWarning(string $message): void;

    public function addWarnings(array $messages): void;

    /**
     * Clear the current messages
     *
     * @param bool $now Should the messages also be cleared for the current request
     * @return void
     */
    public function clearMessages(): void;

    /**
     * Get all messages
     *
     * @param MessageStatus|null $status
     * @return array
     */
    public function getMessages(?MessageStatus $status = null, bool $keep = false): array;

    /**
     * Keep ALL flash messages for an additional hop
     *
     * @return void
     */
    public function prolong(): void;
}