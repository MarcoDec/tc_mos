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
            case 'TCONCEPT': //100x150 mm => 4x6 inches
                $zpl = $this->generateTConceptZPLstr($carton);
                $carton->setZpl($zpl);
                break;
            case 'ETI9': //74x210 mm => 3x8.3 inches
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
        $description = $carton->getProductDescription();
        //$maxLength = 50; // Exemple de longueur maximale
        //$truncatedDescription = $this->truncateStringByWord($description, $maxLength);
        $truncatedDescription = $description;
        $zpl = <<<ZPL
\${^XA

^PR6
^LH0,10
^FO30,10^GB735,1160,2^FS
^FO30,830^GB735,2,2,^FS
^FO280,10^GB2,1160,2^FS
^FO530,10^GB2,1160,2^FS
^FO710,50^A0R,40,50^FDDESTINATAIRE:^FS
^FO580,50^A0R,60,60^FD<DESTINATAIRE>^FS
^FO710,850^A0R,40,50^FDEXPEDITEUR:^FS
^FO580,870^A0R,60,60^FD<EXPEDITEUR>^FS
^FO480,50^A0R,40,50^FDDESIGNATION:^FS
^FO400,50^A0R,32,32^FD<DESIGNATION>^FS
^FO350,50^A0R,35,50^FDRef Client:^FS
^FO350,280^A0R,35,50^FD<Ref Client>^FS
^FO300,50^A0R,35,50^FDIndice:^FS
^FO300,200^A0R,35,50^FD<Indice>^FS
^FO480,850^A0R,40,50^FDLOT:^FS
^FO330,880^A0R,100,80^FD<LOT>^FS
^FO50,80^BY2
^BCR,100,N,N,N,
^FD<CODEBARRE>^FS
^FO230,50^A0R,40,50^FDREFERENCE PRODUIT:^FS
^FO150,60^A0R,70,90^FD<REFERENCE PRODUIT>^FS
^FO230,850^A0R,40,50^FDQUANTITE:^FS
^FO80,900^A0R,100,100^FD<QUANTITE>^FS

^XZ}$
ZPL;
    $zpl = str_replace('<DESTINATAIRE>', $carton->getCustomerAddressName(), $zpl);
    $zpl = str_replace('<EXPEDITEUR>', $carton->getManufacturer(), $zpl);
    $zpl = str_replace('<DESIGNATION>', $truncatedDescription, $zpl);
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

^PR6
^LH0,10
^FO20,20^GB550,1630,2^FS
^FO20,1080^GB550,2,2,^FS
^FO210,20^GB2,1630,2^FS
^FO400,20^GB2,1630,2^FS                               
^FO520,50^A0R,40,50^FDDESTINATAIRE:^FS                  
^FO420,50^A0R,60,60^FD<DESTINATAIRE>^FS       
^FO520,1100^A0R,40,50^FDEXPEDITEUR:^FS                  
^FO420,1250^A0R,60,60^FD<EXPEDITEUR>^FS                    
^FO350,50^A0R,40,50^FDDESIGNATION:^FS                   
^FO300,50^A0R,35,50^FD<DESIGNATION>^FS     
^FO260,50^A0R,35,50^FDRef Client:^FS                    
^FO260,270^A0R,35,50^FD<Ref Client>^FS                    
^FO220,50^A0R,35,50^FDIndice:^FS                        
^FO220,200^A0R,35,50^FD<Indice>^FS                            
^FO350,1100^A0R,40,50^FDLOT:^FS                         
^FO240,1250^A0R,100,80^FD<LOT>^FS                      
^FO40,65^BY2                                            
^BCR,50,N,N,N                                         
^FD<CODEBARRE>^FS                      
^FO160,50^A0R,40,50^FDREFERENCE PRODUIT:^FS             
^FO90,120^A0R,70,90^FD<REFERENCE PRODUIT>^FS          
^FO160,1100^A0R,40,50^FDQUANTITE:^FS                     
^FO40,1300^A0R,100,100^FD<QUANTITE>^FS

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

    public function removeLabel(Carton $carton) : void {
        $carton->setDeleted(true);
        $this->em->persist($carton);
        $this->em->flush();
    }
    function truncateStringByWord($string, $maxLength) {
        $words = explode(' ', $string);
        $truncated = '';

        foreach ($words as $word) {
            if (strlen($truncated) + strlen($word) + 1 > $maxLength) {
                break;
            }
            $truncated .= ($truncated === '' ? '' : ' ') . $word;
        }

        return $truncated;
    }
}