<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

class EchoesNotificationFactory
{
    public function buildFromPayload(array $value, string $id, string $field): ?EchoesNotification
    {
        if (!isset($value['message_echoes'][0])) {
            return null;
        }

        $message = $value['message_echoes'][0];
        $metadata = $value['metadata'] ?? [];

        return new EchoesNotification(
            $message['id'],
            new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
            $message['to'],
            $message['type'],
            $message,
            $message['timestamp']
        );
    }
}
