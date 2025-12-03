<?php

declare(strict_types=1);

namespace App\Support\AlertSenders;

use InvalidArgumentException;

class AlertSenderFactory
{
    private array $senders = [];

    public function __construct()
    {
        $this->registerSender(new EmailSender());
        $this->registerSender(new SlackSender());
        $this->registerSender(new WebhookSender());
        $this->registerSender(new DiscordSender());
    }

    public function registerSender(AlertSenderInterface $sender): void
    {
        $this->senders[] = $sender;
    }

    public function getSender(string $type): AlertSenderInterface
    {
        foreach ($this->senders as $sender) {
            if ($sender->supports($type)) {
                return $sender;
            }
        }

        throw new InvalidArgumentException("No alert sender available for type: {$type}");
    }

    public function hasSender(string $type): bool
    {
        foreach ($this->senders as $sender) {
            if ($sender->supports($type)) {
                return true;
            }
        }

        return false;
    }
}
