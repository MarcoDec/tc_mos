<?php

namespace App\Service;

use App\Entity\Logistics\Label\Carton;
use Doctrine\ORM\EntityManagerInterface;

class LabelCartonManager
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function generateZPL(Carton $carton) : string {
        switch ($carton->getLabelType()) {
            case 'TCONCEPT':
                $carton->setZpl($this->generateTConceptZPLstr($carton));
            case 'ETI9':
                $carton->setZpl($this->generateETI9ZPLstr($carton));
            default:
                $carton->setZpl('');
        }
        $this->em->persist($carton);
        $this->em->flush();
        return $carton->getZpl();
    }

    /**
     * @return string
     */
    public function generateTConceptZPLstr(Carton $carton): string {
        return '^XA
            ^FO30,30^GB735,1130,2^FS
            ^FO30,830^GB735,2,2,^FS
            ^FO280,30^GB2,1130,2^FS
            ^FO530,30^GB2,1130,2^FS
            ^FO710,50^ADR,30,20^FDDESTINATAIRE:^FS
            ^FO580,50^ADR,50,20^FD'.$carton->getCustomerAddressName().'^FS
            ^FO710,850^ADR,30,20^FDEXPEDITEUR:^FS
            ^FO580,880^ADR,50,20^FD'.$carton->getManufacturer().'^FS
            ^FO480,50^ADR,30,20^FDDESIGNATION:^FS
            ^FO400,50^ADR,30,20^FD'.$carton->getProductDescription().'^FS
            ^FO350,50^ADR,30,20^FDRef Client:^FS
            ^FO350,320^ADR,30,20^FD'.$carton->getProductReference().'^FS
            ^FO300,50^ADR,30,20^FDIndice:^FS
            ^FO300,220^ADR,30,20^FD'.$carton->getProductIndice().'^FS
            ^FO480,850^ADR,30,20^FDLOT:^FS
            ^FO300,880^ADR,80,40^FD'.$carton->getBatchnumber().'^FS
            ^FO50,65^BY3
            ^BCR,100,N,N,N
            ^FD'.$carton->getProductReference().'/'.$carton->getProductIndice().'/'.$carton->getBatchnumber().'^FS
            ^FO230,50^ADR,30,20^FDREFERENCE PRODUIT:^FS
            ^FO155,120^ADR,60,40^FD'.$carton->getProductReference().'/'.$carton->getProductIndice().'^FS
            ^FO230,850^ADR,30,20^FDQUANTITE:^FS
            ^FO60,920^ADR,100,60^FD'.$carton->getQuantity().'^FS
            ^XZ';
    }

    /**
     * @return string
     */
    public function generateETI9ZPLstr(): string {
        return '^XA
            ^FO20,20^GB550,1630,2^FS
            ^FO20,1080^GB550,2,2,^FS
            ^FO210,20^GB2,1630,2^FS
            ^FO400,20^GB2,1630,2^FS
            ^FO520,50^ADR,30,20^FDDESTINATAIRE:^FS
            ^FO420,50^ADR,50,20^FD'.$this->customerAddressName.'^FS
            ^FO520,1100^ADR,30,20^FDEXPEDITEUR:^FS
            ^FO420,1250^ADR,50,20^FD'.$this->manufacturer.'^FS
            ^FO350,50^ADR,30,20^FDDESIGNATION:^FS
            ^FO300,50^ADR,30,20^FD'.$this->productDescription.'^FS
            ^FO260,50^ADR,30,20^FDRef Client:^FS
            ^FO260,320^ADR,30,20^FD'.$this->productReference.'^FS
            ^FO220,50^ADR,30,20^FDIndice:^FS
            ^FO220,220^ADR,30,20^FD'.$this->productIndice.'^FS
            ^FO350,1100^ADR,30,20^FDLOT:^FS
            ^FO240,1250^ADR,80,40^FD'.$this->batchnumber.'^FS
            ^FO40,65^BY3
            ^BCR,50,N,N,N
            ^FD'.$this->productReference.'/'.$this->productIndice.'/'.$this->batchnumber.'^FS
            ^FO160,50^ADR,30,20^FDREFERENCE PRODUIT:^FS
            ^FO90,120^ADR,60,40^FD'.$this->productReference.'/'.$this->productIndice.'^FS
            ^FO160,1100^ADR,30,20^FDQUANTITE:^FS
            ^FO40,1300^ADR,100,60^FD'.$this->quantity.'^FS
            ^XZ';
    }
}