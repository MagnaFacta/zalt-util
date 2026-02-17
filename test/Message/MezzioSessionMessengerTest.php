<?php

namespace Zalt\Message;

use Mezzio\Session\Session;
use Mezzio\Session\SessionInterface;
use PHPUnit\Framework\TestCase;

class MezzioSessionMessengerTest extends TestCase
{
    private SessionInterface $session;
    private MezzioSessionMessenger $messenger;

    protected function setUp(): void
    {
        $this->session = new Session([]);
        $this->messenger = new MezzioSessionMessenger($this->session);
    }

    public static function addMessageDataProvider(): array
    {
        return [
            'default-status' => [
                'message' => 'Test message',
                'status' => null,
                'existingMessages' => [],
                'expectedMessages' => [
                    MessageStatus::Info->value => ['Test message']
                ]
            ],
            'specific-status' => [
                'message' => 'Test message',
                'status' => MessageStatus::Warning,
                'existingMessages' => [],
                'expectedMessages' => [
                    MessageStatus::Warning->value => ['Test message']
                ]
            ],
        ];
    }

    /**
     * @dataProvider addMessageDataProvider
     */
    public function testAddMessage(string $message, ?MessageStatus $status, array $existingMessages, array $expectedMessages): void
    {
        $this->session->set(MezzioSessionMessenger::SESSION_KEY, $existingMessages);

        if ($status) {
            $this->messenger->addMessage($message, $status);
        } else {
            $this->messenger->addMessage($message);
        }

        $this->assertEquals($expectedMessages, $this->messenger->getMessages());
    }

    public function testAddMessageAppendsToExistingMessages(): void
    {
        $existingMessages = [
            MessageStatus::Info->value => ['Existing message']
        ];
        $newMessage = 'New test message';
        $expectedMessages = [
            MessageStatus::Info->value => ['Existing message', $newMessage]
        ];

        $this->session->set(MezzioSessionMessenger::SESSION_KEY, $existingMessages);

        $this->messenger->addMessage($newMessage);

        $this->assertEquals($expectedMessages, $this->messenger->getMessages());
    }

    public function testAddMessageHandlesEmptyMessagesArray(): void
    {
        $existingMessages = [];
        $newMessage = 'Test message';
        $expectedMessages = [
            MessageStatus::Info->value => [$newMessage]
        ];

        $this->session->set(MezzioSessionMessenger::SESSION_KEY, $existingMessages);

        $this->messenger->addMessage($newMessage);

        $this->assertEquals($expectedMessages, $this->messenger->getMessages());
    }

    /**
     * Test to ensure clearMessages successfully removes all messages from the session.
     */
    public function testClearMessagesSuccessfullyClearsAllMessages(): void
    {
        $existingMessages = [
            MessageStatus::Info->value => ['Existing message'],
        ];
        $this->session->set(MezzioSessionMessenger::SESSION_KEY, $existingMessages);
        $this->assertEquals($existingMessages, $this->messenger->getMessages());

        $this->messenger->clearMessages();

        $this->assertEquals([], $this->messenger->getMessages());
    }

    /**
     * Test that clearMessages does not throw errors when the session is already empty.
     */
    public function testClearMessagesDoesNotThrowErrorOnEmptySession(): void
    {
        $this->messenger->clearMessages();

        $this->assertEquals([], $this->messenger->getMessages());
    }

    /**
     * Data provider for getMessages tests.
     *
     * Provides test cases with different combinations of stored messages,
     * $status filters, and $keep argument values.
     */
    public static function getMessagesDataProvider(): array
    {
        return [
            'all-messages, keep false' => [
                'storedMessages' => [
                    MessageStatus::Info->value => ['Info message 1', 'Info message 2'],
                    MessageStatus::Warning->value => ['Warning message']
                ],
                'status' => null,
                'keep' => false,
                'expectedMessages' => [
                    MessageStatus::Info->value => ['Info message 1', 'Info message 2'],
                    MessageStatus::Warning->value => ['Warning message']
                ]
            ],
            'specific-status, keep false' => [
                'storedMessages' => [
                    MessageStatus::Info->value => ['Info message 1', 'Info message 2'],
                    MessageStatus::Warning->value => ['Warning message']
                ],
                'status' => MessageStatus::Info,
                'keep' => false,
                'expectedMessages' => ['Info message 1', 'Info message 2']
            ],
            'all-messages, keep true' => [
                'storedMessages' => [
                    MessageStatus::Info->value => ['Info message 1'],
                ],
                'status' => null,
                'keep' => true,
                'expectedMessages' => [
                    MessageStatus::Info->value => ['Info message 1']
                ]
            ],
            'specific-status, keep true' => [
                'storedMessages' => [
                    MessageStatus::Info->value => ['Info message 1', 'Info message 2'],
                ],
                'status' => MessageStatus::Info,
                'keep' => true,
                'expectedMessages' => ['Info message 1', 'Info message 2']
            ],
        ];
    }

    /**
     * @dataProvider getMessagesDataProvider
     */
    public function testGetMessages(array $storedMessages, ?MessageStatus $status, bool $keep, array $expectedMessages): void
    {
        $this->session->set(MezzioSessionMessenger::SESSION_KEY, $storedMessages);

        $this->assertSame($expectedMessages, $this->messenger->getMessages($status, $keep));

        if ($keep) {
            $this->assertSame($expectedMessages, $this->messenger->getMessages($status, $keep));

            $this->assertSame($expectedMessages, $this->messenger->getMessages($status));
        }
        $this->assertSame([], $this->messenger->getMessages($status, $keep));
    }
}