<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Message
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Message;

use Mezzio\Flash\FlashMessagesInterface;

/**
 *
 * @package    Zalt
 * @subpackage Message
 * @since      Class available since version 1.0
 */
class MezzioFlashMessenger implements StatusMessengerInterface
{
    public const FLASH_KEY = 'status-messages';

    public function __construct(
        protected FlashMessagesInterface $messenger
    )
    { }

    public function addMessage(string $message, MessageStatus $status = MessageStatus::Info, bool $now = false): void
    {
        $this->addMessages([$message], $status, $now);
    }

    public function addMessages(array $messages, MessageStatus $status=MessageStatus::Info, bool $now = false): void
    {
        $storedMessages = $this->messenger->getFlash(static::FLASH_KEY, []);

        $storedMessages[$status->value] = array_merge($storedMessages[$status->value] ?? [], $messages);

        if ($now) {
            $this->messenger->flashNow(static::FLASH_KEY, $storedMessages);
            return;
        }
        $this->messenger->flash(static::FLASH_KEY, $storedMessages);
    }

    public function addDanger(string $message, bool $now = false): void
    {
        $this->addMessages([$message], MessageStatus::Danger, $now);
    }

    public function addDangers(array $messages, bool $now = false): void
    {
        $this->addMessages($messages, MessageStatus::Danger, $now);
    }

    public function addError(string $message, bool $now = false): void
    {
        $this->addMessages([$message], MessageStatus::Error, $now);
    }

    public function addErrors(array $messages, bool $now = false): void
    {
        $this->addMessages($messages, MessageStatus::Error, $now);
    }

    public function addInfo(string $message, bool $now = false): void
    {
        $this->addMessages([$message], MessageStatus::Info, $now);
    }

    public function addInfos(array $messages, bool $now = false): void
    {
        $this->addMessages($messages, MessageStatus::Info, $now);
    }

    public function addSuccess(string $message, bool $now = false): void
    {
        $this->addMessages([$message], MessageStatus::Success, $now);
    }

    public function addSuccesses(array $messages, bool $now = false): void
    {
        $this->addMessages($messages, MessageStatus::Success, $now);
    }

    public function addWarning(string $message, bool $now = false): void
    {
        $this->addMessages([$message], MessageStatus::Warning, $now);
    }

    public function addWarnings(array $messages, bool $now = false): void
    {
        $this->addMessages($messages, MessageStatus::Warning, $now);
    }

    public function clearMessages(bool $now = true): void
    {
        if (!$now) {
            $this->messenger->flash(static::FLASH_KEY, []);
            return;
        }
        $this->messenger->flashNow(static::FLASH_KEY, []);
    }

    public function getMessages(?MessageStatus $status = null): array
    {
        $storedMessages = $this->messenger->getFlash(static::FLASH_KEY, []);
        if ($status === null) {
            return $storedMessages;
        }

        if (isset($storedMessages[$status->value])) {
            return $storedMessages[$status->value];
        }

        return [];
    }

    public function prolong(): void
    {
        $this->messenger->prolongFlash();
    }
}