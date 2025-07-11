<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

class AppStateNotificationFactory
{
    public function buildFromPayload(array $payload, ?string $timestamp, ?string $id, ?string $field = null): AppStateNotification
    {
        $business = new Support\Business(
            $payload['metadata']['display_phone_number'] ?? '',
            $payload['metadata']['phone_number_id'] ?? ''
        );

        $notification = new AppStateNotification(
            $id ?? '',
            $business,
            $timestamp ?? ''
        );

        if (isset($payload['state_sync']) && is_array($payload['state_sync'])) {
            foreach ($payload['state_sync'] as $stateSync) {
                if ($stateSync['type'] === 'contact' && isset($stateSync['contact'])) {
                    $contact = new AppState\Contact(
                        $stateSync['contact']['phone_number'] ?? '',
                        $stateSync['contact']['full_name'] ?? '',
                        $stateSync['contact']['first_name'] ?? '',
                        $stateSync['action'] ?? '',
                        $stateSync['metadata']['timestamp'] ?? ''
                    );
                    $notification->addContact($contact);
                }
            }
        }

        return $notification;
    }
}
