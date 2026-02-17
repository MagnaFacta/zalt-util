<?php

namespace Zalt\Message;

use Mezzio\Flash\FlashMessagesInterface;
use PHPUnit\Framework\TestCase;
use Zalt\Mock\SimpleFlashRequestFactory;

/**
 * Tests for the MezzioFlashMessenger class, focusing on the addMessages method.
 */
class MezzioFlashMessengerTest extends TestCase
{
    private FlashMessagesInterface $flashMessenger;
    private MezzioFlashMessenger $messenger;

    protected function setUp(): void
    {
        $request = SimpleFlashRequestFactory::createWithoutServiceManager('http://localhost/index.php');

        $this->flashMessenger = $request->getAttribute('flash');
        $this->messenger      = new MezzioFlashMessenger($this->flashMessenger);
    }

    /**
     * Data provider for addMessages method tests.
     */
    public static function addMessageDataProvider(): array
    {
        return [
            'single message, default status' => [
                'message' => 'Test message',
                'status' => MessageStatus::Info,
                'existingMessages' => [],
                'expectedMessages' => [
                    'Test message',
                ]
            ],
            'multiple messages, warning status' => [
                'message' => 'Warning 1',
                'status' => MessageStatus::Warning,
                'existingMessages' => [],
                'expectedMessages' => [
                    'Warning 1',
                ]
            ],
            'append to existing messages' => [
                'message' => 'New info message',
                'status' => MessageStatus::Info,
                'existingMessages' => [
                    MessageStatus::Info->value => ['Old info message']
                ],
                'expectedMessages' => [
                    'Old info message', 'New info message',
                ]
            ],
            'flashNow with messages' => [
                'message' => 'Immediate message',
                'status' => MessageStatus::Success,
                'existingMessages' => [],
                'expectedMessages' => [
                    'Immediate message',
                ]
            ],
        ];
    }

    /**
     * @dataProvider addMessageDataProvider
     */
    public function testAddMessage(string $message, MessageStatus $status, array $existingMessages, array $expectedMessages): void
    {
        $this->flashMessenger->flashNow(MezzioFlashMessenger::FLASH_KEY, $existingMessages);

        $this->messenger->addMessage($message, $status);

        $this->assertEquals($expectedMessages, $this->messenger->getMessages($status, true));

        $this->messenger->clearMessages();
        $this->assertEquals([], $this->messenger->getMessages($status, true));
    }

    /**
     * Data provider for addMessages method tests.
     */
    public static function addMessagesDataProvider(): array
    {
        return [
            'single message, default status' => [
                'messages' => ['Test message'],
                'status' => MessageStatus::Info,
                'existingMessages' => [],
                'expectedMessages' => [
                    MessageStatus::Info->value => ['Test message'],
                ]
            ],
            'multiple messages, warning status' => [
                'messages' => ['Warning 1', 'Warning 2'],
                'status' => MessageStatus::Warning,
                'existingMessages' => [],
                'expectedMessages' => [
                    MessageStatus::Warning->value => ['Warning 1', 'Warning 2'],
                ]
            ],
            'append to existing messages' => [
                'messages' => ['New info message'],
                'status' => MessageStatus::Info,
                'existingMessages' => [
                    MessageStatus::Info->value => ['Old info message']
                ],
                'expectedMessages' => [
                    MessageStatus::Info->value => ['Old info message', 'New info message'],
                ]
            ],
            'flashNow with messages' => [
                'messages' => ['Immediate message'],
                'status' => MessageStatus::Success,
                'existingMessages' => [],
                'expectedMessages' => [
                    MessageStatus::Success->value => ['Immediate message'],
                ]
            ],
        ];
    }

    /**
     * @dataProvider addMessagesDataProvider
     */
    public function testAddMessages(array $messages, MessageStatus $status, array $existingMessages, array $expectedMessages): void
    {
        $this->flashMessenger->flashNow(MezzioFlashMessenger::FLASH_KEY, $existingMessages);

        $this->messenger->addMessages($messages, $status);

        $this->assertEquals($expectedMessages, $this->messenger->getMessages());
    }

    /**
     * Data provider for empty messages test.
     */
    public static function emptyMessagesDataProvider(): array
    {
        return [
            'empty messages, default status, not now' => [
                'messages' => [],
                'status' => MessageStatus::Info,
                'existingMessages' => [],
                'expectedMessages' => [
                    MessageStatus::Info->value => [],
                ]
            ],
            'empty messages with existing data' => [
                'messages' => [],
                'status' => MessageStatus::Warning,
                'existingMessages' => [
                    MessageStatus::Info->value => ['Test Information']
                ],
                'expectedMessages' => [
                    MessageStatus::Info->value => ['Test Information'],
                    MessageStatus::Warning->value => [],
                ]
            ],
        ];
    }

    /**
     * @dataProvider emptyMessagesDataProvider
     */
    public function testAddMessagesHandlesEmptyMessages(array $messages, MessageStatus $status, array $existingMessages, array $expectedMessages): void
    {
        $this->flashMessenger->flashNow(MezzioFlashMessenger::FLASH_KEY, $existingMessages);

        $this->messenger->addMessages($messages, $status);

        $this->assertEquals($expectedMessages, $this->messenger->getMessages());
    }
}