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
class MezzioFlashMessenger implements MessengerInterface
{
    public function __construct(
        protected FlashMessagesInterface $messenger
    )
    { }
    
    public function addMessage(mixed $message) : void
    {
        $this->messenger->flash($message, $message);
    }
}