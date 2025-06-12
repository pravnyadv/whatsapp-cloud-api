<?php
namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification;

final class HistoryNotification extends Notification
{
    /** @var History\Thread[] */
    private array $threads = [];
    
    /** @var History\HistoryError[] */
    private array $errors = [];
    
    private ?History\Phase $phase = null;

    public function __construct(
        string $id,
        Support\Business $business,
        string $received_at
    ) {
        parent::__construct($id, $business, $received_at);
    }

    public function addThread(History\Thread $thread): self
    {
        $this->threads[] = $thread;
        return $this;
    }

    public function addError(History\HistoryError $error): self
    {
        $this->errors[] = $error;
        return $this;
    }

    public function setPhase(History\Phase $phase): self
    {
        $this->phase = $phase;
        return $this;
    }

    /**
     * @return History\Thread[]
     */
    public function getThreads(): array
    {
        return $this->threads;
    }

    /**
     * @return History\HistoryError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getPhase(): ?History\Phase
    {
        return $this->phase;
    }

    public function hasThreads(): bool
    {
        return !empty($this->threads);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function isHistoryShared(): bool
    {
        return $this->hasThreads() && !$this->hasErrors();
    }

    public function isHistoryDeclined(): bool
    {
        return $this->hasErrors() && !$this->hasThreads();
    }

    public function getThreadsCount(): int
    {
        return count($this->threads);
    }

    public function getTotalMessagesCount(): int
    {
        $total = 0;
        foreach ($this->threads as $thread) {
            $total += $thread->getMessagesCount();
        }
        return $total;
    }

    /**
     * Get thread by WhatsApp user phone number
     */
    public function getThreadById(string $threadId): ?History\Thread
    {
        foreach ($this->threads as $thread) {
            if ($thread->getId() === $threadId) {
                return $thread;
            }
        }
        return null;
    }

    /**
     * Get all messages from all threads
     * @return History\Message[]
     */
    public function getAllMessages(): array
    {
        $messages = [];
        foreach ($this->threads as $thread) {
            $messages = array_merge($messages, $thread->getMessages());
        }
        return $messages;
    }

    /**
     * Get messages by type across all threads
     * @param string $type
     * @return History\Message[]
     */
    public function getMessagesByType(string $type): array
    {
        $messages = [];
        foreach ($this->threads as $thread) {
            $messages = array_merge($messages, $thread->getMessagesByType($type));
        }
        return $messages;
    }

    /**
     * Check if this is the final chunk of the current phase
     */
    public function isPhaseComplete(): bool
    {
        return $this->phase && $this->phase->isComplete();
    }

    /**
     * Check if entire synchronization is complete
     */
    public function isSyncComplete(): bool
    {
        return $this->phase && $this->phase->progress() === 100;
    }

    /**
     * Get the history decline error if present
     */
    public function getDeclineError(): ?History\HistoryError
    {
        foreach ($this->errors as $error) {
            if ($error->code() === 2593109) {
                return $error;
            }
        }
        return null;
    }
}
