<?php

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
 * @since      Class available since version zalt-util 1.0
 */
trait MessageTrait
{
    /**
     * A session based message store.
     *
     * Standard the flash messenger for storing messages
     *
     * @var MessengerInterface
     *
     */
    protected MessengerInterface $messenger;

    /**
     * Adds one or more messages to the session based message store.
     *
     * @param mixed $message Can be an array or multiple argemuents. Each sub element is a single message string
     */
    public function addMessage(mixed $message): void
    {
        if (is_array($message)) {
            foreach ($message as $msg) {
                $this->messenger->addMessage($msg);
            }
        } else {
            $this->messenger->addMessage($message);
        }
    }

    /**
     * Returns a session based message store for adding messages to.
     *
     * @return MessengerInterface
     */
    public function getMessenger(): MessengerInterface
    {
        return $this->messenger;
    }
    
    /**
     * Set a session based message store for adding messages to.
     * 
     * @param \Zalt\Message\MessengerInterface $messenger
     * @return void
     */
    public function setMessenger(MessengerInterface $messenger): void
    {
        $this->messenger = $messenger;
    }
}