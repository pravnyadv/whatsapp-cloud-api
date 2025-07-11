<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification\History;

use Netflie\WhatsAppCloudApi\WebHook\Notification\MessageNotification;

final class Message
{
    private string $id;
    private string $from;
    private ?string $to;
    private string $timestamp;
    private string $type;
    private string $status;
    private MessageNotification $message_notification;

    public function __construct(
        string $id,
        string $from,
        ?string $to,
        string $timestamp,
        string $type,
        string $status,
        MessageNotification $message_notification
    ) {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->status = $status;
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

    public function to(): ?string
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

    public function status(): string
    {
        return $this->status;
    }

    public function messageNotification(): MessageNotification
    {
        return $this->message_notification;
    }

    public function isFromBusiness(): bool
    {
        return $this->to !== null;
    }

    public function isFromUser(): bool
    {
        return $this->to === null;
    }

    public function isDelivered(): bool
    {
        return $this->status === 'DELIVERED';
    }

    public function isRead(): bool
    {
        return $this->status === 'READ';
    }

    public function isError(): bool
    {
        return $this->status === 'ERROR';
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isSent(): bool
    {
        return $this->status === 'SENT';
    }

    public function isPlayed(): bool
    {
        return $this->status === 'PLAYED';
    }

    public function isMediaPlaceholder(): bool
    {
        return $this->type === 'media_placeholder';
    }

    /**
     * Get message content based on type
     */
    public function getContent()
    {
        switch ($this->type) {
            case 'text':
                return $this->message_notification->text();
            case 'image':
            case 'video':
            case 'audio':
            case 'document':
            case 'sticker':
            case 'voice':
                return $this->message_notification->media();
            case 'location':
                return $this->message_notification->location();
            case 'contacts':
                return $this->message_notification->contact();
            case 'interactive':
                return $this->message_notification->interactive();
            case 'button':
                return $this->message_notification->button();
            case 'reaction':
                return $this->message_notification->reaction();
            case 'order':
                return $this->message_notification->order();
            case 'system':
                return $this->message_notification->system();
            case 'media_placeholder':
                return null; // No content for placeholders
            default:
                return null;
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'timestamp' => $this->timestamp,
            'type' => $this->type,
            'status' => $this->status,
            'is_from_business' => $this->isFromBusiness(),
            'is_from_user' => $this->isFromUser(),
        ];
    }
}
