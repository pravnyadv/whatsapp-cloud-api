<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification\Support\Business;

class HistoryNotificationFactory
{
    public function buildFromPayload(array $value, ?string $timestamp, ?string $id, string $field): HistoryNotification
    {
        $metadata = $value['metadata'] ?? [];
        $historyData = $value['history'][0] ?? [];
        
        return new HistoryNotification(
            $id ?? uniqid('history_'),
            new Business($metadata['phone_number_id'] ?? '', $metadata['display_phone_number'] ?? ''),
            $timestamp ?? (string) time(),
            $historyData
        );
    }
}
