<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Management\Unit;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'gpao:tree:recover', description: 'Corrige la structure intervallaire en base de données')]
class TreeRecoverCommand extends Command {
    private const CLASSES = [ComponentFamily::class, ProductFamily::class, Unit::class];

    public function __construct(private readonly EntityManagerInterface $em, ?string $name = null) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        foreach (self::CLASSES as $class) {
            $this->em->getRepository($class)->recover();
        }
        $this->em->flush();
        return self::SUCCESS;
    }
}
