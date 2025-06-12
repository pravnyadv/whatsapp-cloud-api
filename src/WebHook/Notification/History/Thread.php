<?php
namespace Netflie\WhatsAppCloudApi\WebHook\Notification\History;

final class Thread
{
    private string $id;
    /** @var Message[] */
    private array $messages = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addMessage(Message $message): self
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getMessagesCount(): int
    {
        return count($this->messages);
    }

    /**
     * Get messages by type
     * @param string $type
     * @return Message[]
     */
    public function getMessagesByType(string $type): array
    {
        return array_filter($this->messages, function(Message $message) use ($type) {
            return $message->type() === $type;
        });
    }

    /**
     * Get messages by status
     * @param string $status
     * @return Message[]
     */
    public function getMessagesByStatus(string $status): array
    {
        return array_filter($this->messages, function(Message $message) use ($status) {
            return $message->status() === $status;
        });
    }

    /**
     * Get messages sent by business (has 'to' field)
     * @return Message[]
     */
    public function getBusinessMessages(): array
    {
        return array_filter($this->messages, function(Message $message) {
            return $message->isFromBusiness();
        });
    }

    /**
     * Get messages sent by user (no 'to' field)
     * @return Message[]
     */
    public function getUserMessages(): array
    {
        return array_filter($this->messages, function(Message $message) {
            return $message->isFromUser();
        });
    }

    public function getLastMessage(): ?Message
    {
        return end($this->messages) ?: null;
    }

    public function getFirstMessage(): ?Message
    {
        return $this->messages[0] ?? null;
    }
}
