<?php
namespace Netflie\WhatsAppCloudApi\WebHook\Notification\History;

final class Phase
{
    private int $phase;
    private int $chunk_order;
    private int $progress;

    public function __construct(int $phase, int $chunk_order, int $progress)
    {
        $this->phase = $phase;
        $this->chunk_order = $chunk_order;
        $this->progress = $progress;
    }

    public function phase(): int
    {
        return $this->phase;
    }

    public function chunkOrder(): int
    {
        return $this->chunk_order;
    }

    public function progress(): int
    {
        return $this->progress;
    }

    public function isComplete(): bool
    {
        return $this->phase === 2;
    }

    public function getPhaseDescription(): string
    {
        return match($this->phase) {
            0 => 'Day 0 through day 1',
            1 => 'Day 1 through day 90', 
            2 => 'Day 90 through day 180',
            default => 'Unknown phase'
        };
    }

    public function toArray(): array
    {
        return [
            'phase' => $this->phase,
            'chunk_order' => $this->chunk_order,
            'progress' => $this->progress,
            'description' => $this->getPhaseDescription()
        ];
    }
}
