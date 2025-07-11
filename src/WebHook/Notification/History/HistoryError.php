<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification\History;

final class HistoryError
{
    private int $code;
    private string $title;
    private string $message;
    private string $details;

    public function __construct(int $code, string $title, string $message, string $details)
    {
        $this->code = $code;
        $this->title = $title;
        $this->message = $message;
        $this->details = $details;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function details(): string
    {
        return $this->details;
    }

    public function isHistoryDeclined(): bool
    {
        return $this->code === 2593109;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
            'message' => $this->message,
            'details' => $this->details,
        ];
    }
}
