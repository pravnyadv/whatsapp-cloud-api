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
        
        $notification = $this->buildEchoesNotification($metadata, $message);

        return $this->decorateNotification($notification, $message, $value);
    }

    private function buildEchoesNotification(array $metadata, array $message): EchoesNotification
    {
        switch ($message['type']) {
            case 'text':
                return new Text(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['text']['body'],
                    $message['timestamp']
                );
            case 'reaction':
                return new Reaction(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['reaction']['message_id'],
                    $message['reaction']['emoji'] ?? '',
                    $message['timestamp']
                );
            case 'sticker':
            case 'image':
            case 'document':
            case 'audio':
            case 'video':
            case 'voice':
                return new Media(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message[$message['type']]['id'],
                    $message[$message['type']]['mime_type'],
                    $message[$message['type']]['sha256'],
                    $message[$message['type']]['filename'] ?? '',
                    $message[$message['type']]['caption'] ?? '',
                    $message['timestamp']
                );
            case 'location':
                return new Location(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['location']['latitude'],
                    $message['location']['longitude'],
                    $message['location']['name'] ?? '',
                    $message['location']['address'] ?? '',
                    $message['timestamp']
                );
            case 'contacts':
                return new Contact(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['contacts'][0]['addresses'] ?? [],
                    $message['contacts'][0]['emails'] ?? [],
                    $message['contacts'][0]['name'],
                    $message['contacts'][0]['org'] ?? [],
                    $message['contacts'][0]['phones'],
                    $message['contacts'][0]['urls'] ?? [],
                    $message['contacts'][0]['birthday'] ?? null,
                    $message['timestamp']
                );
            case 'button':
                return new Button(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['button']['text'],
                    $message['button']['payload'],
                    $message['timestamp']
                );
            case 'interactive':
                if (isset($message['interactive']['nfm_reply'])) {
                    $nfmReply = $message['interactive']['nfm_reply'];

                    return new Flow(
                        $message['id'],
                        new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                        $nfmReply['name'],
                        $nfmReply['body'],
                        $nfmReply['response_json'] ?? '',
                        $message['timestamp'],
                    );
                }

                return new Interactive(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['interactive']['list_reply']['id'] ?? $message['interactive']['button_reply']['id'],
                    $message['interactive']['list_reply']['title'] ?? $message['interactive']['button_reply']['title'],
                    $message['interactive']['list_reply']['description'] ?? '',
                    $message['timestamp']
                );
            case 'order':
                return new Order(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['order']['catalog_id'],
                    $message['order']['text'] ?? '',
                    new Support\Products($message['order']['product_items']),
                    $message['timestamp']
                );
            case 'system':
                return new System(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    new Support\Business($message['system']['customer'], ''),
                    $message['system']['body'],
                    $message['timestamp']
                );
            case 'unknown':
            default:
                return new Unknown(
                    $message['id'],
                    new Support\Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['timestamp']
                );
        }
    }

    private function decorateNotification(EchoesNotification $notification, array $message, array $value): EchoesNotification
    {
        // For echoes, we can create a customer object from the 'to' field
        if (isset($message['to'])) {
            $notification->withCustomer(new Support\Customer(
                $message['to'],
                '', // Name is not available in echoes
                $message['to']
            ));
        }

        return $notification;
    }
}
