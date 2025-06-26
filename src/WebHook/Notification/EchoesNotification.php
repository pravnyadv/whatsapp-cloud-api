<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

class EchoesNotification extends MessageNotification
{
    protected string $to;
    protected string $message_type;
    protected array $message_data;

    public function __construct(
        string $id,
        Support\Business $business,
        string $to,
        string $message_type,
        array $message_data,
        string $timestamp
    ) {
        parent::__construct($id, $business, $timestamp);
        $this->to = $to;
        $this->message_type = $message_type;
        $this->message_data = $message_data;
        
        // Set customer automatically
        $this->customer = new Support\Customer($to, '', $to);
    }

    public function to(): string
    {
        return $this->to;
    }

    // Main message method - works for all types
    public function message(): string
    {
        switch ($this->message_type) {
            case 'text':
                return $this->message_data['text']['body'] ?? '';
            case 'button':
                return $this->message_data['button']['text'] ?? '';
            case 'interactive':
                return $this->message_data['interactive']['nfm_reply']['body'] ?? '';
            default:
                return $this->message_data[$this->message_type]['caption'] ?? '';
        }
    }

    // Text methods
    public function text(): string
    {
        return $this->message_data['text']['body'] ?? '';
    }

    // Media methods
    public function mediaId(): string
    {
        return $this->message_data[$this->message_type]['id'] ?? '';
    }

    public function mimeType(): string
    {
        return $this->message_data[$this->message_type]['mime_type'] ?? '';
    }

    public function sha256(): string
    {
        return $this->message_data[$this->message_type]['sha256'] ?? '';
    }

    public function filename(): string
    {
        return $this->message_data[$this->message_type]['filename'] ?? '';
    }

    public function caption(): string
    {
        return $this->message_data[$this->message_type]['caption'] ?? '';
    }

    // Button methods
    public function buttonText(): string
    {
        return $this->message_data['button']['text'] ?? '';
    }

    public function payload(): string
    {
        return $this->message_data['button']['payload'] ?? '';
    }

    // Flow methods
    public function name(): string
    {
        return $this->message_data['interactive']['nfm_reply']['name'] ?? '';
    }

    public function body(): string
    {
        return $this->message_data['interactive']['nfm_reply']['body'] ?? '';
    }

    public function responseJson(): string
    {
        return $this->message_data['interactive']['nfm_reply']['response_json'] ?? '';
    }

    // Override parent methods since echo messages don't have context
    public function replyingToMessageId(): ?string
    {
        return null;
    }

    public function isForwarded(): bool
    {
        return false;
    }

    public function context(): ?Support\Context
    {
        return null;
    }

    // Type checking methods for instanceof checks
    public function isText(): bool
    {
        return $this->message_type === 'text';
    }

    public function isMedia(): bool
    {
        return in_array($this->message_type, ['image', 'video', 'audio', 'document', 'voice', 'sticker']);
    }

    public function isButton(): bool
    {
        return $this->message_type === 'button';
    }

    public function isFlow(): bool
    {
        return $this->message_type === 'interactive' && isset($this->message_data['interactive']['nfm_reply']);
    }

    public function isInteractive(): bool
    {
        return $this->message_type === 'interactive';
    }
}
