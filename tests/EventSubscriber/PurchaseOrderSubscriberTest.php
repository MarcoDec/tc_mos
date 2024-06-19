<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Embeddable\Measure;
use Exception;
use App\Entity\Purchase\Order\ComponentItem;
use App\EventSubscriber\PurchaseOrderSubscriber;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use App\Tests\EventSubscriber\AbstractWorkFlowSubscriberTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Ce fichier à pour but de tester le bon fonctionnement de la méthode
 * onWorkflowPurchaseOrderTransition de la classe PurchaseOrderSubscriber
 */
class PurchaseOrderSubscriberTest extends KernelTestCase
{
    use WorkflowSubscriberTestTrait;

    /**
     * @test
     * @testdox  Test de la méthode **`onWorkflowPurchaseOrderTransition`** de
     *           la classe **`PurchaseOrderSubscriber`** dans le cas de la
     *           transition **`validate`**
     * @return void
     */
    public function testApplyValidateTransitionToWorkflow(): void
    {
//        echo "\nPurchaseOrderSubscriberTest::testApplyValidateTransitionToWorkflow()\n";
        $this->setTransitionName('validate');
        //region 1. On crée un purchaseOrder bon pour la transition validate
        $purchaseOrder1a = $this->generatePurchaseOrderFromArray([
            'state' => 'cart',
            'blocker' => 'enabled',
            'items' => [
                ['state' => 'draft', 'blocker' => 'enabled'],
                ['state' => 'draft', 'blocker' => 'enabled'],
            ]
        ]);
        $purchaseOrder1b = $this->generatePurchaseOrderFromArray([
            'state' => 'draft',
            'blocker' => 'enabled',
            'items' => [
                ['state' => 'draft', 'blocker' => 'enabled'],
                ['state' => 'draft', 'blocker' => 'enabled'],
            ]
        ]);
        //endregion
        //region 2. On crée un purchaseOrder pas bon pour la transition validate
        $purchaseOrder2 = $this->generatePurchaseOrderFromArray([
            'state' => 'initial',
            'blocker' => 'enabled',
            'items' => [
                ['state' => 'draft', 'blocker' => 'enabled'],
                ['state' => 'draft', 'blocker' => 'disabled'],
            ]
        ]);
        //endregion
        /** @var PurchaseOrderSubscriber $subscriber */
        $subscriber = $this->getSubscriber();
        $subscriber->applyTransition(
            $purchaseOrder1a,
            $this->getWorkflowName(),
            $this->getTransitionName(),
            $this->getWorkflowRegistry());
        $this->assertEquals(
            'agreed',
            $purchaseOrder1a->getState(),
            "T1: Un purchaseOrder à l'état initial cart devrait être dans l'état agreed après l'application de la transition validate");
        //Tous les items enfants du purchaseOrder1a sont dans l'état agreed
        foreach ($purchaseOrder1a->getItems() as $item) {
            $this->assertEquals(
                'agreed',
                $item->getState(),
                "T1: Un purchaseOrderItem à l'état initial draft devrait être dans l'état agreed après l'application de la transition validate sur le purchaseOrder parent");
        }

        $subscriber->applyTransition(
            $purchaseOrder1b,
            $this->getWorkflowName(),
            $this->getTransitionName(),
            $this->getWorkflowRegistry());
        $this->assertEquals(
            'agreed',
            $purchaseOrder1b->getState(),
            "T2: Un purchaseOrder à l'état initial draft devrait être dans l'état agreed après l'application de la transition validate");
        //Tous les items enfants du purchaseOrder1b sont dans l'état agreed
        foreach ($purchaseOrder1b->getItems() as $item) {
            $this->assertEquals(
                'agreed',
                $item->getState(),
                "T2: Un purchaseOrderItem à l'état initial draft devrait être dans l'état agreed après l'application de la transition validate sur le purchaseOrder parent");
        }
        //endregion
        //region 4. On teste la transtion validation dans un cas non valide
        $subscriber->applyTransition(
            $purchaseOrder2,
            $this->getWorkflowName(),
            $this->getTransitionName(),
            $this->getWorkflowRegistry());
        $this->assertEquals(
            'initial',
            $purchaseOrder2->getState(),
            "T3: Un purchaseOrder à l'état initial initial devrait rester dans son état initial après l'application de la transition validate");
        //Aucun des items enfants du purchaseOrder2 n'est dans l'état agreed
        foreach ($purchaseOrder2->getItems() as $item) {
            $this->assertNotEquals(
                'agreed',
                $item->getState(),
                "T3: Un purchaseOrderItem à l'état initial draft ne devrait pas être dans l'état agreed après l'application de la transition validate sur le purchaseOrder parent");
        }
        //endregion
    }

    /**
     * Genère une entité PurchaseOrder et ses items de type PurchaseOrderITem à
     * partir d'un tableau
     ** Les informations pour générer un purchaseOrder est son etat State courant et son état Blocker courant
     ** Les informations pour générer un purchaseOrderItem est son etat State courant, son état Blocker courant
     * @param array $data
     * @return PurchaseOrder
     */
    private function generatePurchaseOrderFromArray(array $data): PurchaseOrder
    {
        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->setState($data['state'])
            ->setBlocker($data['blocker']);
        foreach ($data['items'] as $itemData) {
            $item = new ComponentItem();
            $item->setState($itemData['state'])
                ->setBlocker($itemData['blocker']);
            $purchaseOrder->addItem($item);
        }
        return $purchaseOrder;
    }

    /**
     * Test de la méthode **`onWorkflowPurchaseOrderTransition`** de la classe
     * **`PurchaseOrderSubscriber`** dans le cas de la transition **`pay`**
     * @return void
     */
    public function testApplyPayTransitionToWorkflow(): void
    {
//        echo "\nPurchaseOrderSubscriberTest::testApplyPayTransitionToWorkflow()\n";
        $this->setTransitionName('pay');
        //region 1. On crée un purchaseOrder bon pour la transition pay
        $purchaseOrder3 = $this->generatePurchaseOrderFromArray([
            'state' => 'received',
            'blocker' => 'enabled',
            'items' => [
                ['state' => 'received', 'blocker' => 'enabled'],
                ['state' => 'received', 'blocker' => 'enabled'],
            ]
        ]);
        //endregion
        //region 2. On crée un purchaseOrder pas bon pour la transition pay
        $purchaseOrder4 = $this->generatePurchaseOrderFromArray([
            'state' => 'partially_received',
            'blocker' => 'enabled',
            'items' => [
                ['state' => 'received', 'blocker' => 'enabled'],
                ['state' => 'partially_received', 'blocker' => 'enabled'],
            ]
        ]);
        //endregion
        /** @var PurchaseOrderSubscriber $subscriber */
        $subscriber = $this->getSubscriber();
        $subscriber->applyTransition(
            $purchaseOrder3,
            $this->getWorkflowName(),
            $this->getTransitionName(),
            $this->getWorkflowRegistry());
        $this->assertEquals('paid',
            $purchaseOrder3->getState(),
            'Le purchaseOrder3 devrait être dans l\'état paid');
        // Les items enfants du purchaseOrder3 sont dans l'état paid
        foreach ($purchaseOrder3->getItems() as $item) {
            $this->assertEquals(
                'paid',
                $item->getState(),
                'Un purchaseOrderItem à l\'état received devrait être dans l\'état paid après l\'application de la transition pay sur le purchaseOrder parent');
        }
        $purchaseOrder4b = clone $purchaseOrder4;
        $subscriber->applyTransition(
            $purchaseOrder4,
            $this->getWorkflowName(),
            $this->getTransitionName(),
            $this->getWorkflowRegistry());
        $this->assertEquals(
            'partially_received',
            $purchaseOrder4->getState(),
            'Le purchaseOrder4 devrait être dans l\'état partially_received');
        // Les items enfants du purchaseOrder4 n'ont pas changé d'état
        foreach ($purchaseOrder4->getItems() as $key => $item) {
            $initialState = $purchaseOrder4b->getItems()->get($key)->getState();
            $finalState = $item->getState();
            $this->assertEquals(
                $initialState,
                $finalState,
                'Les items de commande ne devrait pas changer d\'état lors d\'une transition pay lorsque sont parent est dans l\'état partially_received');
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->setEntity(new PurchaseOrder());
        $this->setWorkflowName('purchase_order');
        $this->traitSetUp();
        $this->setSubscriber(PurchaseOrderSubscriber::class);
    }
}
