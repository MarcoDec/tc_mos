<?php

namespace App\Controller\Logistics\Label;

use App\Entity\Logistics\Label\SinglePrinterMobileUnit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class GetSinglePrinterMobileUnitFromHostController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public function __invoke(Request $request) : ?SinglePrinterMobileUnit
    {
        $ipAdresseClient = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        return $this->entityManager->getRepository(SinglePrinterMobileUnit::class)->findOneBy([
            'mobileUnitIp' => $ipAdresseClient,
            'deleted' => false
        ]);
    }
}