<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification\AppState;

final class Contact
{
    private string $phone_number;
    private ?string $full_name;
    private ?string $first_name;
    private string $action;
    private string $timestamp;

    public function __construct(
        string $phone_number,
        ?string $full_name,
        ?string $first_name,
        string $action,
        string $timestamp
    ) {
        $this->phone_number = $phone_number;
        $this->full_name = $full_name;
        $this->first_name = $first_name;
        $this->action = $action;
        $this->timestamp = $timestamp;
    }

    public function phoneNumber(): string
    {
        return $this->phone_number;
    }

    public function fullName(): ?string
    {
        return $this->full_name;
    }

    public function firstName(): ?string
    {
        return $this->first_name;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function timestamp(): string
    {
        return $this->timestamp;
    }

    public function isAdded(): bool
    {
        return $this->action === 'add';
    }

    public function isRemoved(): bool
    {
        return $this->action === 'remove';
    }

    public function hasFullName(): bool
    {
        return !empty($this->full_name);
    }

    public function hasFirstName(): bool
    {
        return !empty($this->first_name);
    }

    public function toArray(): array
    {
        return [
            'phone_number' => $this->phone_number,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'action' => $this->action,
            'timestamp' => $this->timestamp,
        ];
    }
}
