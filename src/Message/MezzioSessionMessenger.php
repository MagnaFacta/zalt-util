<?php

namespace Zalt\Message;

use Mezzio\Session\Session;

class MezzioSessionMessenger implements StatusMessengerInterface
{
    use StatusMessengerTrait;

    public const SESSION_KEY = 'status-messages';

    public function __construct(protected Session $session)
    {}
    public function addMessage(string $message, MessageStatus $status = MessageStatus::Info): void
    {
        $this->addMessages([$message], $status);
    }

    public function addMessages(array $messages, MessageStatus $status = MessageStatus::Info): void
    {
        $storedMessages = $this->session->get(static::SESSION_KEY, []);

        $storedMessages[$status->value] = array_merge($storedMessages[$status->value] ?? [], $messages);

        $this->session->set(static::SESSION_KEY, $storedMessages);
    }

    public function clearMessages(): void
    {
        $this->session->unset(static::SESSION_KEY);
    }

    public function getMessages(?MessageStatus $status = null, bool $keep = false): array
    {
        $storedMessages = $this->session->get(static::SESSION_KEY, []);
        if (!$keep) {
            $this->clearMessages();
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
        // Do nothing
    }
}