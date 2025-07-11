<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification\Echoes;

use Netflie\WhatsAppCloudApi\WebHook\Notification\MessageNotification;

final class Message
{
    private string $id;
    private string $from;
    private string $to;
    private string $timestamp;
    private string $type;
    private MessageNotification $message_notification;

    public function __construct(
        string $id,
        string $from,
        string $to,
        string $timestamp,
        string $type,
        MessageNotification $message_notification
    ) {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->message_notification = $message_notification;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function from(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function timestamp(): string
    {
        return $this->timestamp;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function messageNotification(): MessageNotification
    {
        return $this->message_notification;
    }

    /**
     * Check if message is of specific type
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    public function isText(): bool
    {
        return $this->isType('text');
    }

    public function isMedia(): bool
    {
        return in_array($this->type, ['image', 'video', 'audio', 'document', 'sticker', 'voice']);
    }

    public function isLocation(): bool
    {
        return $this->isType('location');
    }

    public function isContact(): bool
    {
        return $this->isType('contacts');
    }

    public function isInteractive(): bool
    {
        return $this->isType('interactive');
    }

    public function isButton(): bool
    {
        return $this->isType('button');
    }

    public function isReaction(): bool
    {
        return $this->isType('reaction');
    }

    /**
     * Get message content based on type
     */
    public function getContent(): mixed
    {
        return match($this->type) {
            'text' => $this->message_notification->text(),
            'image', 'video', 'audio', 'document', 'sticker', 'voice' => $this->message_notification->media(),
            'location' => $this->message_notification->location(),
            'contacts' => $this->message_notification->contact(),
            'interactive' => $this->message_notification->interactive(),
            'button' => $this->message_notification->button(),
            'reaction' => $this->message_notification->reaction(),
            'order' => $this->message_notification->order(),
            'system' => $this->message_notification->system(),
            default => null
        };
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'timestamp' => $this->timestamp,
            'type' => $this->type,
        ];
    }
}
