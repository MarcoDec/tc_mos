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
^FO290,10^GB2,1160,2^FS
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
^FO60,80^BY2
^BCR,100,N,N,N,
^FD<CODEBARRE>^FS
^FO240,50^A0R,40,50^FDREFERENCE PRODUIT:^FS
^FO160,60^A0R,70,90^FD<REFERENCE PRODUIT>^FS
^FO240,850^A0R,40,50^FDQUANTITE:^FS
^FO80,900^A0R,100,100^FD<QUANTITE>^FS
<LOGO>
^FO30,1080^A0R,20,25^FD<MATRICULE>^FS
^FO30,20^A0R,20,25^FD<DATE>^FS

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

        $matricule = $carton->getOperator();
        $zpl = str_replace('<MATRICULE>', $matricule, $zpl);
        $date = $carton->getDate();
        //on formatte la date en jjmmaaaahhmm
        $formatedDate = $date->format('dmYHi');
        $zpl = str_replace('<DATE>', $formatedDate, $zpl);

        $logoType = $carton->getLogoType();
        $zplLogo = '';
        switch ($logoType) {
            case 0:
                $zplLogo = '';
                break;
            case 1:
                $zplLogo = '^FO300,700,^GD75,45,9,B,R^FS
                ^FO375,700^GB5,90,6^FS
                ^FO300,745,^GD75,45,9,B,L^FS
                ^FO300,690,^GE110,110,4,B,^FS
                ^FO320,800^ADR,25,15^FDR^FS';
                break;
            case 2:
                $zplLogo = '^FO300,700,^GD75,45,9,B,R^FS
                ^FO375,700^GB5,90,6^FS
                ^FO300,745,^GD75,45,9,B,L^FS
                ^FO300,690,^GE110,110,4,B,^FS
                ^FO320,800^ADR,25,15^FDR^FS
                ^FO370,800^ADR,25,15^FDS^FS';
        }
        $zpl = str_replace('<LOGO>', $zplLogo, $zpl);

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
^LH0,0
^FO20,20^GB550,1630,2^FS
^FO20,1080^GB550,2,2,^FS
^FO230,20^GB2,1630,2^FS
^FO420,20^GB2,1630,2^FS                               
^FO520,50^A0R,40,50^FDDESTINATAIRE:^FS                  
^FO440,50^A0R,60,60^FD<DESTINATAIRE>^FS       
^FO520,1100^A0R,40,50^FDEXPEDITEUR:^FS                  
^FO440,1250^A0R,60,60^FD<EXPEDITEUR>^FS                    
^FO370,50^A0R,40,50^FDDESIGNATION:^FS                   
^FO320,50^A0R,35,50^FD<DESIGNATION>^FS     
^FO280,50^A0R,35,50^FDRef Client:^FS                    
^FO280,270^A0R,35,50^FD<Ref Client>^FS                    
^FO240,50^A0R,35,50^FDIndice:^FS                        
^FO240,200^A0R,35,50^FD<Indice>^FS                            
^FO370,1100^A0R,40,50^FDLOT:^FS                         
^FO260,1250^A0R,100,80^FD<LOT>^FS                      
^FO60,65^BY2                                            
^BCR,50,N,N,N                                         
^FD<CODEBARRE>^FS                      
^FO180,50^A0R,40,50^FDREFERENCE PRODUIT:^FS             
^FO110,50^A0R,70,90^FD<REFERENCE PRODUIT>^FS          
^FO180,1100^A0R,40,50^FDQUANTITE:^FS                     
^FO60,1300^A0R,100,100^FD<QUANTITE>^FS
<LOGO>
^FO25,1560^A0R,20,25^FD<MATRICULE>^FS
^FO25,25^A0R,20,25^FD<DATE>^FS

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

        $matricule = $carton->getOperator();
        $zpl = str_replace('<MATRICULE>', $matricule, $zpl);
        $date = $carton->getDate();
        //on formatte la date en jjmmaaaahhmm
        $formatedDate = $date->format('dmYHi');
        $zpl = str_replace('<DATE>', $formatedDate, $zpl);

        $logoType = $carton->getLogoType();
        $zplLogo = '';
        switch ($logoType) {
            case 0:
                $zplLogo = '';
                break;
            case 1:
                $zplLogo = '^FO250,950,^GD75,45,9,B,R^FS
                ^FO325,950^GB5,90,6^FS
                ^FO250,995,^GD75,45,9,B,L^FS
                ^FO250,940,^GE110,110,4,B,^FS
                ^FO270,1050^ADR,25,15^FDR^FS';
                break;
            case 2:
                $zplLogo = '^FO250,950,^GD75,45,9,B,R^FS
                ^FO325,950^GB5,90,6^FS
                ^FO250,995,^GD75,45,9,B,L^FS
                ^FO250,940,^GE110,110,4,B,^FS
                ^FO270,1050^ADR,25,15^FDR^FS
                ^FO320,1050^ADR,25,15^FDS^FS';
        }
        $zpl = str_replace('<LOGO>', $zplLogo, $zpl);

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