<?php

namespace App\Controller\Logistics\Label;

use App\Entity\Logistics\Label\SinglePrinterMobileUnit;
use App\Entity\Management\Printer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AddNewSinglePrinterMobileController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Request $request) : SinglePrinterMobileUnit
    {
        //dump(['request' => $request->request->all()]);
        $content = json_decode($request->getContent(), true);
        $ipAdresseClient = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $mobileUnit = new SinglePrinterMobileUnit();
        $mobileUnit->setName($content['name']);
        $mobileUnit->setMobileUnitIp($ipAdresseClient);
        $segments = explode('/', $content['printer']);
        $id = end($segments);
        $printer = $this->entityManager->getRepository(Printer::class)->find($id);
        $mobileUnit->setPrinter($printer);
        $this->entityManager->persist($mobileUnit);
        $this->entityManager->flush();
//        dump([
//           'ipAdresseClient' => $ipAdresseClient,
//            'mobileUnit' => $mobileUnit,
//            'printer' => $printer,
//            'content' => $content
//        ]);
        return $mobileUnit;
    }
}