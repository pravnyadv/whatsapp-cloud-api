<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification;

class HistoryNotification extends Notification
{
    private array $historyData;
    private array $metadata;
    private array $threads;
    private array $allMessages = [];

    public function __construct(string $id, $business, string $received_at_timestamp, array $historyData)
    {
        parent::__construct($id, $business, $received_at_timestamp);
        
        $this->historyData = $historyData;
        $this->metadata = $historyData['metadata'] ?? [];
        $this->threads = $historyData['threads'] ?? [];
        
        // Flatten all messages for easy access
        $this->flattenMessages();
    }

    /**
     * Flatten all messages from all threads into a single array
     */
    private function flattenMessages(): void
    {
        foreach ($this->threads as $thread) {
            if (isset($thread['messages']) && is_array($thread['messages'])) {
                foreach ($thread['messages'] as $message) {
                    $message['status'] = null;
                    $message['chatId'] = $thread['id'];
                    $message['fromMe'] = $message['history_context']['from_me'] ?? false;
                    if($message['fromMe']) {
                        $message['status'] = $message['history_context']['status'] ?? null;
                    }
                    $this->allMessages[] = $message;
                }
            }
        }
    }

    /**
     * Get the current phase (0, 1, or 2)
     */
    public function getPhase(): int
    {
        return (int)($this->metadata['phase'] ?? 0);
    }

    /**
     * Get the chunk order
     */
    public function getChunkOrder(): int
    {
        return (int)($this->metadata['chunk_order'] ?? 1);
    }

    /**
     * Get the sync progress (0-100)
     */
    public function getProgress(): int
    {
        return (int)($this->metadata['progress'] ?? 0);
    }

    /**
     * Check if sync is completed (progress = 100)
     */
    public function isSyncCompleted(): bool
    {
        return $this->getProgress() === 100;
    }

    /**
     * Check if current phase is completed (phase = 2)
     */
    public function isPhaseCompleted(): bool
    {
        return $this->getPhase() === 2;
    }

    /**
     * Get all threads
     */
    public function getThreads(): array
    {
        return $this->threads;
    }

    /**
     * Get all messages from all threads
     */
    public function getAllMessages(): array
    {
        return $this->allMessages;
    }

    /**
     * Get messages by thread ID
     */
    public function getMessagesByThreadId(string $threadId): array
    {
        foreach ($this->threads as $thread) {
            if ($thread['id'] === $threadId) {
                return $thread['messages'] ?? [];
            }
        }
        return [];
    }

    /**
     * Get message type for a specific message (by index in flattened array)
     */
    public function getType(int $messageIndex = 0): ?string
    {
        return $this->allMessages[$messageIndex]['type'] ?? null;
    }

    /**
     * Get message body/content for a specific message (by index in flattened array)
     */
    public function getBody(int $messageIndex = 0): ?string
    {
        $message = $this->allMessages[$messageIndex] ?? null;
        if (!$message) {
            return null;
        }

        // Handle different message types
        switch ($message['type'] ?? '') {
            case 'text':
                return $message['text']['body'] ?? null;
            case 'errors':
                return $message['errors'][0]['message'] ?? null;
            default:
                return null;
        }
    }

    /**
     * Get message ID for a specific message (by index in flattened array)
     */
    public function getId(int $messageIndex = 0): ?string
    {
        return $this->allMessages[$messageIndex]['id'] ?? null;
    }

    /**
     * Get message sender for a specific message (by index in flattened array)
     */
    public function getFrom(int $messageIndex = 0): ?string
    {
        return $this->allMessages[$messageIndex]['from'] ?? null;
    }

    /**
     * Get message timestamp for a specific message (by index in flattened array)
     */
    public function getMessageTimestamp(int $messageIndex = 0): ?string
    {
        return $this->allMessages[$messageIndex]['timestamp'] ?? null;
    }

    /**
     * Get total number of messages across all threads
     */
    public function getTotalMessagesCount(): int
    {
        return count($this->allMessages);
    }

    /**
     * Get total number of threads
     */
    public function getThreadsCount(): int
    {
        return count($this->threads);
    }

    /**
     * Get specific message by index
     */
    public function getMessage(int $index): ?array
    {
        return $this->allMessages[$index] ?? null;
    }

    /**
     * Get all text messages only
     */
    public function getTextMessages(): array
    {
        return array_filter($this->allMessages, function($message) {
            return ($message['type'] ?? '') === 'text';
        });
    }

    /**
     * Get all error messages only
     */
    public function getErrorMessages(): array
    {
        return array_filter($this->allMessages, function($message) {
            return ($message['type'] ?? '') === 'errors';
        });
    }

    /**
     * Check if there are any errors in the messages
     */
    public function hasErrors(): bool
    {
        return count($this->getErrorMessages()) > 0;
    }

    /**
     * Get the raw history data
     */
    public function getRawHistoryData(): array
    {
        return $this->historyData;
    }

    /**
     * Get metadata for database storage
     */
    public function getMetadataForStorage(): array
    {
        return [
            'phase' => $this->getPhase(),
            'chunk_order' => $this->getChunkOrder(),
            'progress' => $this->getProgress(),
            'sync_completed' => $this->isSyncCompleted(),
            'threads_count' => $this->getThreadsCount(),
            'messages_count' => $this->getTotalMessagesCount(),
            'has_errors' => $this->hasErrors()
        ];
    }
}
