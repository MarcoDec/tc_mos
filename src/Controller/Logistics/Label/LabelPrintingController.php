<?php

namespace App\Controller\Logistics\Label;


use App\Entity\Logistics\Label\Carton;
use App\Entity\Logistics\Label\SinglePrinterMobileUnit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class LabelPrintingController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public function __invoke(Request $request)
    {
        // On récupère l'id de l'étiquette carton
        $idLabel=$request->get('id');
        // On récupère l'étiquette carton
        $label=$this->entityManager->getRepository(Carton::class)->find($idLabel);
        // On récupère l'ip de la requête
        $ipAdresseClient = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        // On récupère l'unité mobile
        $mobileUnit = $this->entityManager->getRepository(SinglePrinterMobileUnit::class)->findOneBy([
            'mobileUnitIp' => $ipAdresseClient,
            'deleted' => false
        ]);
        // On récupère l'imprimante
        $printer = $mobileUnit->getPrinter();
        $printerName = $printer->getName();
        $filePath = "label.zpl";
        // On envoie la commande d'impression à l'imprimante
        file_put_contents("label.zpl", $label->getZpl());
        exec("lp -d $printerName $filePath");
        unlink("label.zpl");
        return $label->getZpl();
    }
}