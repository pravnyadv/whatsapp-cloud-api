<?php
namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification;

final class EchoesNotification extends Notification
{
    /** @var Echoes\Message[] */
    private array $messages = [];

    public function __construct(
        string $id,
        Support\Business $business,
        string $received_at
    ) {
        parent::__construct($id, $business, $received_at);
    }

    public function addMessage(Echoes\Message $message): self
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @return Echoes\Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function hasMessages(): bool
    {
        return !empty($this->messages);
    }

    public function getMessagesCount(): int
    {
        return count($this->messages);
    }

    /**
     * Get the first message (most common case)
     */
    public function getFirstMessage(): ?Echoes\Message
    {
        return $this->messages[0] ?? null;
    }

    /**
     * Get messages by type
     * @param string $type
     * @return Echoes\Message[]
     */
    public function getMessagesByType(string $type): array
    {
        return array_filter($this->messages, function(Echoes\Message $message) use ($type) {
            return $message->type() === $type;
        });
    }

    /**
     * Get messages sent to a specific recipient
     * @param string $to
     * @return Echoes\Message[]
     */
    public function getMessagesTo(string $to): array
    {
        return array_filter($this->messages, function(Echoes\Message $message) use ($to) {
            return $message->to() === $to;
        });
    }

    /**
     * Get all unique recipients
     * @return string[]
     */
    public function getRecipients(): array
    {
        $recipients = array_map(function(Echoes\Message $message) {
            return $message->to();
        }, $this->messages);
        
        return array_unique($recipients);
    }
}
