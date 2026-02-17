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
    use StatusMessengerTrait;
    public const FLASH_KEY = 'status-messages';

    public function __construct(
        protected FlashMessagesInterface $messenger
    )
    { }

    public function addMessage(string $message, MessageStatus $status = MessageStatus::Info): void
    {
        $this->addMessages([$message], $status);
    }

    public function addMessages(array $messages, MessageStatus $status=MessageStatus::Info): void
    {
        $storedMessages = $this->messenger->getFlash(static::FLASH_KEY, []);

        $storedMessages[$status->value] = array_merge($storedMessages[$status->value] ?? [], $messages);

        $this->messenger->flashNow(static::FLASH_KEY, $storedMessages, 0);
        return;
    }

    public function clearMessages(): void
    {
        $this->messenger->flashNow(static::FLASH_KEY, [],0);
    }

    public function getMessages(?MessageStatus $status = null, bool $keep = false): array
    {
        $storedMessages = $this->messenger->getFlash(static::FLASH_KEY, []);
        if ($keep) {
            $this->prolong();
        }
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