<?php
namespace Netflie\WhatsAppCloudApi\WebHook\Notification;

use Netflie\WhatsAppCloudApi\WebHook\Notification;

final class AppStateNotification extends Notification
{
    /** @var AppState\Contact[] */
    private array $contacts = [];

    public function __construct(
        string $id,
        Support\Business $business,
        string $received_at
    ) {
        parent::__construct($id, $business, $received_at);
    }

    public function addContact(AppState\Contact $contact): self
    {
        $this->contacts[] = $contact;
        return $this;
    }

    /**
     * @return AppState\Contact[]
     */
    public function getContacts(): array
    {
        return $this->contacts;
    }

    public function hasContacts(): bool
    {
        return !empty($this->contacts);
    }

    public function getContactsCount(): int
    {
        return count($this->contacts);
    }

    /**
     * Get contacts by action type
     * @param string $action 'add' or 'remove'
     * @return AppState\Contact[]
     */
    public function getContactsByAction(string $action): array
    {
        return array_filter($this->contacts, function(AppState\Contact $contact) use ($action) {
            return $contact->action() === $action;
        });
    }

    /**
     * Get added contacts
     * @return AppState\Contact[]
     */
    public function getAddedContacts(): array
    {
        return $this->getContactsByAction('add');
    }

    /**
     * Get removed contacts
     * @return AppState\Contact[]
     */
    public function getRemovedContacts(): array
    {
        return $this->getContactsByAction('remove');
    }
}
