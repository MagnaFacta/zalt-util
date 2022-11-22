<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Message
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Message;

/**
 *
 * @package    Zalt
 * @subpackage Message
 * @since      Class available since version 1.0
 */
interface MessengerInterface
{
    /**
     * Adds one message to the session based message store.
     *
     * @param string $message
     */
    public function addMessage(string $message): void;

    /**
     * Adds multiple messages to the session based message store.
     *
     * @param array $messages
     */
    public function addMessages(array $messages): void;
}