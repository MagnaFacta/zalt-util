<?php

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use Mezzio\Flash\FlashMessagesInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version zalt-util 1.0
 */
trait MessageTrait
{
    /**
     * A session based message store.
     *
     * Standard the flash messenger for storing messages
     *
     * @var \Mezzio\Flash\FlashMessagesInterface
     */
    protected $messenger;

    /**
     * @var string 
     */
    public static string $messengerKey = 'action-messages';

    /**
     * PSR-7 Request
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * Adds one or more messages to the session based message store.
     *
     * @param mixed $message_args Can be an array or multiple argemuents. Each sub element is a single message string
     * @param string|null $status Optional message status, one of: success, info, warning or danger
     */
    public function addMessage(mixed $message, string $status = 'warning'): void
    {   
        $messenger = $this->getMessenger();
        $messages  = $messenger->getFlash(static::$messengerKey, []);
        $messages[] = [$message, $status];
        $messenger->flash(static::$messengerKey, $messages);
    }

    /**
     * Returns a session based message store for adding messages to.
     *
     * @return \Mezzio\Flash\FlashMessagesInterface
     */
    public function getMessenger(): FlashMessagesInterface
    {
        if (! $this->messenger) {
            $this->messenger =  $this->request->getAttribute('flash');
        }

        return $this->messenger;
    }
}