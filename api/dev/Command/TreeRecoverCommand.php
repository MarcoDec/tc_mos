<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Management\Unit;
use App\Entity\Project\Product\Family;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'gpao:tree:recover', description: 'Corrige la structure intervallaire en base de données')]
class TreeRecoverCommand extends Command {
    public function __construct(private readonly EntityManagerInterface $em, ?string $name = null) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->em->getRepository(Family::class)->recover();
        $this->em->getRepository(Unit::class)->recover();
        $this->em->flush();
        return self::SUCCESS;
    }
}
