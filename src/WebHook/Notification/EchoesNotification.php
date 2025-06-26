<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification;

abstract class EchoesNotification extends Notification
{
    protected ?Support\Customer $customer = null;
    protected string $to;

    public function __construct(string $id, Support\Business $business, string $to, string $received_at_timestamp)
    {
        parent::__construct($id, $business, $received_at_timestamp);
        $this->to = $to;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function customer(): ?Support\Customer
    {
        return $this->customer;
    }

    public function withCustomer(Support\Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }
}
