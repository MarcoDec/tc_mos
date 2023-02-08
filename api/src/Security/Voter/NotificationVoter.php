<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Hr\Employee\Notification;
use App\Security\SecurityTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NotificationVoter extends Voter {
    use SecurityTrait;

    /** @var string */
    final public const GRANTED_NOTIFICATION_READ = 'is_granted(\''.self::NOTIFICATION_READ.'\', object)';

    /** @var string */
    final public const NOTIFICATION_READ = 'NOTIFICATION_READ';

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::NOTIFICATION_READ && $subject instanceof Notification;
    }

    /** @param Notification $subject */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        return $subject->getUser() === $this->getUser();
    }
}
