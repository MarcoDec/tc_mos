<?php

namespace App\Controller\Purchase\SupplierOrder;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SupplierOrderController.
 *
 * @package App\Controller\Purchase\SupplierOrder
 *
 *
 * @Route("/supplierOrder/", name="supplierOrder.")
 */
class SupplierOrderController extends AbstractController
{
    /**
     * @Route("previewPDF", name="previewPDF", methods="GET")
     *
     *
     * @return Response
     */
    public function previewPDF(): Response {
        $society = [
            'name' => 'TCONCEPT',
            'address' => '5 rue Alfred NobelZA La charrière 70190 RIOZ',
            'address2' => '',
            'zip' => '',
            'city' => 'France',
            'phone' => '+33 3 84 91 99 84',
            'fax' => '',
            'email' => 'sales@tconcept.fr
',
        ];
        $orderSupplier = [
            'ref' => 'TCONCEPT',
            'address' => '5 rue Alfred NobelZA La charrière 70190 RIOZ',
            'address2' => '',
            'zip' => '',
            'city' => 'France',
            'phone' => '+33 3 84 91 99 84',
            'fax' => '',
            'email' => 'sales@tconcept.fr',
            'dateValidation'=>'12/02/2022',
            'infoPublic'=>'sss',
            'deliveryCompany' => [
                'invoiceTimeDue'=>'zzzz',
                'society'=>'zzzz',
                'name'=>'ccc',
                'address'=>'zzzz',
                'address2'=>'zzzz',
                'zip'=>'zzzz',
                'city'=>'zzzz',
                'phone'=>'zzzz',
            ],
            'supplier' => [
                'invoiceTimeDue'=>'zzzz',
                'society'=>'zzzz',
                'name'=>'www',
                'address'=>'zzzz',
                'address2'=>'zzzz',
                'zip'=>'zzzz',
                'city'=>'zzzz',
                'phone'=>'zzzz',
                'incoterm'=>['label'=> 'ddd'],
            ],
            'orderSupplierComponents' => [
                'texte'=>'aaaa',
                'dateSouhaitee'=>'12/03/2022',
                'dateLivraison'=>'12/03/2022',

                'loop'=>['index'=> '1'],
            ],
        ];
        $societySupplier =[

        ];
        //$supplier = $FreeSupplierOrder->getSupplier();
      /*  $societySupplier = $societySupplierRepository->findBy([
            'society' => $society,
            'supplier' => $supplier,
        ]);*/
        dump($orderSupplier);
        return $this->render('supplier/order/printable.html.twig', [
            'society' => $society,
            'user' => ['lastname'=> 'admin', 'firstName'=>'super' ],
            'orderSupplier' => $orderSupplier,
            'societySupplier' => $societySupplier,
            'basePath' => '',
           // 'withLinks' => false,
           'viewprint_souhaitee' => true,
        ]);
    }


}
