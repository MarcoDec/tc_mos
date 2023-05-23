<?php

declare(strict_types=1);

namespace App\Controller\Hr\Employee\Notification;

use ApiPlatform\Exception\InvalidArgumentException;
use App\Doctrine\Type\Hr\Employee\EnumNotificationType;
use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryRemoveAction {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    public function __invoke(Request $request): void {
        /** @var null|string $category */
        $category = $request->attributes->get('category');
        if (empty($category)) {
            throw new InvalidArgumentException('Missing category');
        }
        $this->em->getRepository(Notification::class)->removeBy(EnumNotificationType::from($category));
    }
}
