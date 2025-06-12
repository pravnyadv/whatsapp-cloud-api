<?php

namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

class HistoryNotificationFactory
{
    public function buildFromPayload(array $payload, ?string $timestamp, ?string $id, ?string $field = null): HistoryNotification
    {
        $business = new Support\Business(
            $payload['metadata']['phone_number_id'] ?? '',
            $payload['metadata']['display_phone_number'] ?? ''
        );

        $notification = new HistoryNotification(
            $id ?? '',
            $business,
            $timestamp ?? ''
        );

        if (isset($payload['history']) && is_array($payload['history'])) {
            foreach ($payload['history'] as $historyData) {
                // Check if this is an error (history sharing declined)
                if (isset($historyData['errors'])) {
                    foreach ($historyData['errors'] as $errorData) {
                        $error = new History\HistoryError(
                            $errorData['code'] ?? 0,
                            $errorData['title'] ?? '',
                            $errorData['message'] ?? '',
                            $errorData['error_data']['details'] ?? ''
                        );
                        $notification->addError($error);
                    }
                } else {
                    // This is actual history data
                    $metadata = $historyData['metadata'] ?? [];
                    $phase = new History\Phase(
                        $metadata['phase'] ?? 0,
                        $metadata['chunk_order'] ?? 0,
                        $metadata['progress'] ?? 0
                    );
                    $notification->setPhase($phase);

                    // Process threads
                    if (isset($historyData['threads']) && is_array($historyData['threads'])) {
                        foreach ($historyData['threads'] as $threadData) {
                            $thread = $this->buildThread($business, $threadData);
                            $notification->addThread($thread);
                        }
                    }
                }
            }
        }

        return $notification;
    }

    private function buildThread(Support\Business $business, array $threadData): History\Thread
    {
        $threadId = $threadData['id'] ?? '';
        $thread = new History\Thread($threadId);

        if (isset($threadData['messages']) && is_array($threadData['messages'])) {
            foreach ($threadData['messages'] as $messageData) {
                $message = $this->buildHistoryMessage($business, $messageData);
                $thread->addMessage($message);
            }
        }

        return $thread;
    }

    private function buildHistoryMessage(Support\Business $business, array $messageData): History\Message
    {
        $messageId = $messageData['id'] ?? '';
        $from = $messageData['from'] ?? '';
        $to = $messageData['to'] ?? null; // Only present for SMB message echoes
        $timestamp = $messageData['timestamp'] ?? '';
        $type = $messageData['type'] ?? 'unknown';
        
        // Get message status from history_context
        $status = $messageData['history_context']['status'] ?? '';

        // Build the actual message notification using existing logic
        $messageNotification = $this->buildMessageNotification($business, $messageData);

        return new History\Message(
            $messageId,
            $from,
            $to,
            $timestamp,
            $type,
            $status,
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
            case 'media_placeholder':
                return new MediaPlaceholder(
                    $message['id'],
                    $business,
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
