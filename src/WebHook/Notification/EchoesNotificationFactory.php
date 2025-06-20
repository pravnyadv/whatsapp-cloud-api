<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

class EchoesNotificationFactory
{
    public function buildFromPayload(array $payload, ?string $id, ?string $field = null): EchoesNotification
    {
        $business = new Support\Business(
            $payload['metadata']['phone_number_id'] ?? '',
            $payload['metadata']['display_phone_number'] ?? ''
        );

        $notification = new EchoesNotification(
            $id ?? '',
            $business,
        );

        if (isset($payload['message_echoes']) && is_array($payload['message_echoes'])) {
            foreach ($payload['message_echoes'] as $messageData) {
                $message = $this->buildMessageFromEcho($business, $messageData);
                $notification->addMessage($message);
            }
        }

        return $notification;
    }

    private function buildMessageFromEcho(Support\Business $business, array $messageData): Echoes\Message
    {
        // Extract basic message info
        $messageId = $messageData['id'] ?? '';
        $from = $messageData['from'] ?? '';
        $to = $messageData['to'] ?? '';
        $timestamp = $messageData['timestamp'] ?? '';
        $type = $messageData['type'] ?? 'unknown';

        // Build the actual message notification using existing logic
        $messageNotification = $this->buildMessageNotification($business, $messageData);

        return new Echoes\Message(
            $messageId,
            $from,
            $to,
            $timestamp,
            $type,
            $messageNotification
        );
    }

    private function buildMessageNotification(Support\Business $business, array $message): MessageNotification
    {
        switch ($message['type']) {
            case 'text':
                return new Text(
                    $message['id'],
                    $business,
                    $message['text']['body'],
                    $message['timestamp']
                );
            case 'reaction':
                return new Reaction(
                    $message['id'],
                    $business,
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
                    $business,
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
                    $business,
                    $message['location']['latitude'],
                    $message['location']['longitude'],
                    $message['location']['name'] ?? '',
                    $message['location']['address'] ?? '',
                    $message['timestamp']
                );
            case 'contacts':
                return new Contact(
                    $message['id'],
                    $business,
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
                    $business,
                    $message['button']['text'],
                    $message['button']['payload'],
                    $message['timestamp']
                );
            case 'interactive':
                if (isset($message['interactive']['nfm_reply'])) {
                    $nfmReply = $message['interactive']['nfm_reply'];

                    return new Flow(
                        $message['id'],
                        $business,
                        $nfmReply['name'],
                        $nfmReply['body'],
                        $nfmReply['response_json'] ?? '',
                        $message['timestamp'],
                    );
                }

                return new Interactive(
                    $message['id'],
                    $business,
                    $message['interactive']['list_reply']['id'] ?? $message['interactive']['button_reply']['id'],
                    $message['interactive']['list_reply']['title'] ?? $message['interactive']['button_reply']['title'],
                    $message['interactive']['list_reply']['description'] ?? '',
                    $message['timestamp']
                );
            case 'order':
                return new Order(
                    $message['id'],
                    $business,
                    $message['order']['catalog_id'],
                    $message['order']['text'] ?? '',
                    new Support\Products($message['order']['product_items']),
                    $message['timestamp']
                );
            case 'system':
                return new System(
                    $message['id'],
                    $business,
                    new Support\Business($message['system']['customer'], ''),
                    $message['system']['body'],
                    $message['timestamp']
                );
            case 'unknown':
            default:
                return new Unknown(
                    $message['id'],
                    $business,
                    $message['timestamp']
                );
        }
    }
}
