<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Embeddable\Measure;
use Exception;
use Symfony\Component\Workflow\WorkflowInterface;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Order\ComponentItem;
use App\EventSubscriber\PurchaseOrderSubscriber;
use App\Entity\Purchase\Order\Order as PurchaseOrder;
use App\Entity\Logistics\Order\Receipt;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Ce fichier à pour but de tester le bon fonctionnement de la méthode onWorkflowPurchaseOrderTransition de la classe PurchaseOrderSubscriber
 */
class PurchaseOrderSubscriberTest extends KernelTestCase
{
    /**
     * Mock de l'objet Registry utilisé pour instancier les Subscribers
     * @var \PHPUnit\Framework\MockObject\MockObject|(object&\PHPUnit\Framework\MockObject\MockObject)|(Registry&object&\PHPUnit\Framework\MockObject\MockObject)|(Registry&\PHPUnit\Framework\MockObject\MockObject)|Registry
     */
    private \PHPUnit\Framework\MockObject\MockObject|Registry $workflowRegistryMock;
    /**
     * Mock de l'objet LoggerInterface utilisé pour instancier les Subscribers
     * @var (object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject|LoggerInterface|(LoggerInterface&object&\PHPUnit\Framework\MockObject\MockObject)|(LoggerInterface&\PHPUnit\Framework\MockObject\MockObject)
     */
    private LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $loggerMock;

    /**
     * Mock de l'objet Event utilisé pour éxecuter les méthodes des Subscribers
     * @var (object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject|Event|(Event&object&\PHPUnit\Framework\MockObject\MockObject)|(Event&\PHPUnit\Framework\MockObject\MockObject)
     */
    private Event|\PHPUnit\Framework\MockObject\MockObject $eventMock;

    private $workflowPurchaseOrder;

    /**
     * Mock de l'objet Workflow utilisé comme retour de la méthode get du Registry
     * @var (object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject|Workflow|(Workflow&object&\PHPUnit\Framework\MockObject\MockObject)|(Workflow&\PHPUnit\Framework\MockObject\MockObject)
     */
    private \PHPUnit\Framework\MockObject\MockObject|Workflow $workflowMock;
    private $validateTransitionMock;

    private PurchaseOrderSubscriber $subscriber;
    public static function setUpBeforeClass(): void
    {
        echo "\nPurchaseOrderSubscriberTest\n";
    }
    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $registry = self::getContainer()->get(Registry::class);
        $this->workflowRegistryMock = $registry;
        /** @var WorkflowInterface $workflow**/
        $workflow = $registry->get(new PurchaseOrder(), 'purchase_order');
        $this->workflowPurchaseOrder = $workflow;
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->eventMock = $this->createMock(Event::class);

        $this->validateTransitionMock = $this->createMock(Transition::class);
        $this->validateTransitionMock->method('getName')->willReturn('validate');

        $this->subscriber = new PurchaseOrderSubscriber($this->workflowRegistryMock, $this->loggerMock);
    }

    /**
     * Genère une entité PurchaseOrder et ses items de type PurchaseOrderITem à partir d'un tableau
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
     * @test
     * @testdox  Test de la méthode **`onWorkflowPurchaseOrderTransition`** de la classe **`PurchaseOrderSubscriber`** dans le cas de la transition **`validate`**
     * @return void
     */
    public function testApplyValidateTransitionToWorkflow()
    {
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

//        $this->eventMock->method('getWorkflowName')->willReturn('purchase_order');
//        $this->validateTransitionMock->method('getName')->willReturn('validate');
//        $this->eventMock->method('getTransition')->willReturn($this->validateTransitionMock);

        //region 3. On teste la transition validate dans le cas normal
//        $this->eventMock->method('getSubject')->willReturn($purchaseOrder1a);
//        $this->subscriber->onWorkflowPurchaseOrderTransition($this->eventMock);
        $this->subscriber->applyTransitionToWorkflow($purchaseOrder1a, 'purchase_order', 'validate', $this->workflowRegistryMock);
        $this->assertEquals('agreed', $purchaseOrder1a->getState(), "T1: Un purchaseOrder à l'état initial cart devrait être dans l'état agreed après l'application de la transition validate");
        //Tous les items enfants du purchaseOrder1a sont dans l'état agreed
        foreach ($purchaseOrder1a->getItems() as $item) {
            $this->assertEquals('agreed', $item->getState(), "T1: Un purchaseOrderItem à l'état initial draft devrait être dans l'état agreed après l'application de la transition validate sur le purchaseOrder parent");
        }

        $this->subscriber->applyTransitionToWorkflow($purchaseOrder1b, 'purchase_order', 'validate', $this->workflowRegistryMock);
        $this->assertEquals('agreed', $purchaseOrder1b->getState(), "T2: Un purchaseOrder à l'état initial draft devrait être dans l'état agreed après l'application de la transition validate");
        //Tous les items enfants du purchaseOrder1b sont dans l'état agreed
        foreach ($purchaseOrder1b->getItems() as $item) {
            $this->assertEquals('agreed', $item->getState(), "T2: Un purchaseOrderItem à l'état initial draft devrait être dans l'état agreed après l'application de la transition validate sur le purchaseOrder parent");
        }
        //endregion
        //region 4. On teste la transtion validation dans un cas non valide
        $this->subscriber->applyTransitionToWorkflow($purchaseOrder2, 'purchase_order', 'validate', $this->workflowRegistryMock);
        $this->assertEquals('initial', $purchaseOrder2->getState(), "T3: Un purchaseOrder à l'état initial initial devrait rester dans son état initial après l'application de la transition validate");
        //Aucun des items enfants du purchaseOrder2 n'est dans l'état agreed
        foreach ($purchaseOrder2->getItems() as $item) {
            $this->assertNotEquals('agreed', $item->getState(), "T3: Un purchaseOrderItem à l'état initial draft ne devrait pas être dans l'état agreed après l'application de la transition validate sur le purchaseOrder parent");
        }
        //endregion
    }

    /**
     * Test de la méthode **`onWorkflowPurchaseOrderTransition`** de la classe **`PurchaseOrderSubscriber`** dans le cas de la transition **`pay`**
     * @return void
     */
    public function testApplyPayTransitionToWorkflow()
    {
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

        $this->subscriber->applyTransitionToWorkflow($purchaseOrder3, 'purchase_order', 'pay', $this->workflowRegistryMock);
        $this->assertEquals('paid', $purchaseOrder3->getState(), 'Le purchaseOrder3 devrait être dans l\'état paid');
        // Les items enfants du purchaseOrder3 sont dans l'état paid
        foreach ($purchaseOrder3->getItems() as $item) {
            $this->assertEquals('paid', $item->getState(), 'Un purchaseOrderItem à l\'état received devrait être dans l\'état paid après l\'application de la transition pay sur le purchaseOrder parent');
        }
        $purchaseOrder4b = clone $purchaseOrder4;
        $this->subscriber->applyTransitionToWorkflow($purchaseOrder4, 'purchase_order', 'pay', $this->workflowRegistryMock);
        $this->assertEquals('partially_received', $purchaseOrder4->getState(), 'Le purchaseOrder4 devrait être dans l\'état partially_received');
        // Les items enfants du purchaseOrder4 n'ont pas changé d'état
        foreach ($purchaseOrder4->getItems() as $key => $item) {
            $initialState = $purchaseOrder4b->getItems()->get($key)->getState();
            $finalState = $item->getState();
            $this->assertEquals($initialState, $finalState, 'Les items de commande ne devrait pas changer d\'état lors d\'une transition pay lorsque sont parent est dans l\'état partially_received');
        }
    }
}
