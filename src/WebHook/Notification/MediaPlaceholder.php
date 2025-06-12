<?php
namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

final class MediaPlaceholder extends MessageNotification
{
    public function __construct(
        string $id,
        Support\Business $business,
        string $received_at
    ) {
        parent::__construct($id, $business, $received_at);
    }

    public function type(): string
    {
        return 'media_placeholder';
    }

    public function isMediaPlaceholder(): bool
    {
        return true;
    }

    /**
     * Media placeholders have no content - actual media content comes in separate webhook
     */
    public function message(): string
    {
        return '[Media content - see separate webhook for details]';
    }

    public function hasContent(): bool
    {
        return false;
    }
}
