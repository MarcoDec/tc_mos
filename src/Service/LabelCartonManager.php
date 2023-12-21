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
        switch (strtoupper($carton->getLabelKind())) {
            case 'TCONCEPT':
                $zpl = $this->generateTConceptZPLstr($carton);
                $carton->setZpl($zpl);
                break;
            case 'ETI9':
                $zpl = $this->generateETI9ZPLstr($carton);
                $carton->setZpl($zpl);
                break;
            default:
                $carton->setZpl('');
        }
        $this->em->persist($carton);
        $this->em->flush();
        return $carton->getZpl();
    }

    public function generateTConceptZPLstr(Carton $carton) {
        $zpl = <<<ZPL
\${^XA
^FO30,30^GB735,1130,2^FS
^FO30,830^GB735,2,2,^FS
^FO280,30^GB2,1130,2^FS
^FO530,30^GB2,1130,2^FS
^FO710,50^ADR,30,20^FDDESTINATAIRE:^FS
^FO580,50^ADR,50,20^FD<DESTINATAIRE>^FS
^FO710,850^ADR,30,20^FDEXPEDITEUR:^FS
^FO580,880^ADR,50,20^FD<EXPEDITEUR>^FS
^FO480,50^ADR,30,20^FDDESIGNATION:^FS
^FO400,50^ADR,30,20^FD<DESIGNATION>^FS
^FO350,50^ADR,30,20^FDRef Client:^FS
^FO350,320^ADR,30,20^FD<Ref Client>^FS
^FO300,50^ADR,30,20^FDIndice:^FS
^FO300,220^ADR,30,20^FD<Indice>^FS
^FO480,850^ADR,30,20^FDLOT:^FS
^FO300,880^ADR,80,40^FD<LOT>^FS
^FO50,65^BY2
^BCR,100,N,N,N
^FD<CODEBARRE>^FS
^FO230,50^ADR,30,20^FDREFERENCE PRODUIT:^FS
^FO155,120^ADR,60,40^FD<REFERENCE PRODUIT>^FS
^FO230,850^ADR,30,20^FDQUANTITE:^FS
^FO60,920^ADR,100,60^FD<QUANTITE>^FS
^XZ}$
ZPL;
    $zpl = str_replace('<DESTINATAIRE>', $carton->getCustomerAddressName(), $zpl);
    $zpl = str_replace('<EXPEDITEUR>', $carton->getManufacturer(), $zpl);
    $zpl = str_replace('<DESIGNATION>', $carton->getProductDescription(), $zpl);
    $zpl = str_replace('<Ref Client>', $carton->getProductReference(), $zpl);
    $zpl = str_replace('<Indice>', $carton->getProductIndice(), $zpl);
    $zpl = str_replace('<LOT>', $carton->getBatchnumber(), $zpl);
    $zpl = str_replace('<REFERENCE PRODUIT>', $carton->getProductReference().'/'.$carton->getProductIndice(), $zpl);
    $zpl = str_replace('<QUANTITE>', $carton->getQuantity(), $zpl);
    $zpl = str_replace('<CODEBARRE>', $carton->getProductReference().'/'.$carton->getProductIndice().'/'.$carton->getBatchnumber(), $zpl);

    return $zpl;
    }
    /**
     * @param Carton $carton
     * @return string
     */
    public function generateETI9ZPLstr(Carton $carton): string {
        $zpl = <<<ZPL
\${^XA
^FO20,20^GB550,1630,2^FS
^FO20,1080^GB550,2,2,^FS
^FO210,20^GB2,1630,2^FS
^FO400,20^GB2,1630,2^FS
^FO520,50^ADR,30,20^FDDESTINATAIRE:^FS
^FO420,50^ADR,50,20^FD<DESTINATAIRE>^FS
^FO520,1100^ADR,30,20^FDEXPEDITEUR:^FS
^FO420,1250^ADR,50,20^FD<EXPEDITEUR>^FS
^FO350,50^ADR,30,20^FDDESIGNATION:^FS
^FO300,50^ADR,30,20^FD<DESIGNATION>^FS
^FO260,50^ADR,30,20^FDRef Client:^FS
^FO260,320^ADR,30,20^FD<Ref Client>^FS
^FO220,50^ADR,30,20^FDIndice:^FS
^FO220,220^ADR,30,20^FD<Indice>^FS
^FO350,1100^ADR,30,20^FDLOT:^FS
^FO240,1250^ADR,80,40^FD<LOT>^FS
^FO40,65^BY2
^BCR,50,N,N,N
^FD<CODEBARRE>^FS
^FO160,50^ADR,30,20^FDREFERENCE PRODUIT:^FS
^FO90,120^ADR,60,40^FD<REFERENCE PRODUIT>^FS
^FO160,1100^ADR,30,20^FDQUANTITE:^FS
^FO40,1300^ADR,100,60^FD<QUANTITE>^FS
^XZ}$
ZPL;
    $zpl = str_replace('<DESTINATAIRE>', $carton->getCustomerAddressName(), $zpl);
    $zpl = str_replace('<EXPEDITEUR>', $carton->getManufacturer(), $zpl);
    $zpl = str_replace('<DESIGNATION>', $carton->getProductDescription(), $zpl);
    $zpl = str_replace('<Ref Client>', $carton->getProductReference(), $zpl);
    $zpl = str_replace('<Indice>', $carton->getProductIndice(), $zpl);
    $zpl = str_replace('<LOT>', $carton->getBatchnumber(), $zpl);
    $zpl = str_replace('<REFERENCE PRODUIT>', $carton->getProductReference().'/'.$carton->getProductIndice(), $zpl);
    $zpl = str_replace('<QUANTITE>', $carton->getQuantity(), $zpl);
    $zpl = str_replace('<CODEBARRE>', $carton->getProductReference().'/'.$carton->getProductIndice().'/'.$carton->getBatchnumber(), $zpl);
        return zpl;
    }

    public function removeLabel(Carton $carton) : void {
        $carton->setDeleted(true);
        $this->em->persist($carton);
        $this->em->flush();
    }
}