<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification;

final class EchoesNotification extends Notification
{
    /**
     * @var Echoes\Message[]
     */
    private array $messages = [];

    public function __construct(
        string $id,
        Support\Business $business
    ) {
        parent::__construct($id, $business);
    }

    /**
     * Add a message echo to this notification
     */
    public function addMessage(Echoes\Message $message): self
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * Get all echoed messages
     * 
     * @return Echoes\Message[]
     */
    public function messages(): array
    {
        return $this->messages;
    }

    /**
     * Get the first echoed message (convenience method)
     */
    public function firstMessage(): ?Echoes\Message
    {
        return $this->messages[0] ?? null;
    }

    /**
     * Get the last echoed message (convenience method)
     */
    public function lastMessage(): ?Echoes\Message
    {
        return end($this->messages) ?: null;
    }

    /**
     * Get count of echoed messages
     */
    public function messageCount(): int
    {
        return count($this->messages);
    }

    /**
     * Check if notification has any messages
     */
    public function hasMessages(): bool
    {
        return !empty($this->messages);
    }

    /**
     * Get messages of a specific type
     * 
     * @return Echoes\Message[]
     */
    public function getMessagesOfType(string $type): array
    {
        return array_filter($this->messages, fn($message) => $message->isType($type));
    }

    /**
     * Get all text messages
     * 
     * @return Echoes\Message[]
     */
    public function getTextMessages(): array
    {
        return $this->getMessagesOfType('text');
    }

    /**
     * Get all media messages
     * 
     * @return Echoes\Message[]
     */
    public function getMediaMessages(): array
    {
        return array_filter($this->messages, fn($message) => $message->isMedia());
    }

    /**
     * Find a message by its ID
     */
    public function findMessageById(string $messageId): ?Echoes\Message
    {
        foreach ($this->messages as $message) {
            if ($message->id() === $messageId) {
                return $message;
            }
        }
        return null;
    }

    /**
     * Get messages sent to a specific recipient
     * 
     * @return Echoes\Message[]
     */
    public function getMessagesTo(string $to): array
    {
        return array_filter($this->messages, fn($message) => $message->to() === $to);
    }

    /**
     * Get messages sent from a specific sender
     * 
     * @return Echoes\Message[]
     */
    public function getMessagesFrom(string $from): array
    {
        return array_filter($this->messages, fn($message) => $message->from() === $from);
    }

    /**
     * Convert to array representation
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'business' => [
                'phone_number_id' => $this->business()->phoneNumberId(),
                'display_phone_number' => $this->business()->displayPhoneNumber(),
            ],
            'message_count' => $this->messageCount(),
            'messages' => array_map(fn($message) => $message->toArray(), $this->messages),
        ];
    }
}
