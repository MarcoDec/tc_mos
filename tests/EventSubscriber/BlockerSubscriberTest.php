<?php

namespace App\Tests\EventSubscriber;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\EventSubscriber\BlockerSubscriber;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Selling\Customer\State;

class BlockerSubscriberTest extends KernelTestCase
{
    use WorkflowSubscriberTestTrait;
    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->setEntity(new Customer());
        $this->setWorkflowName('blocker');
        $this->traitSetUp();
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->subscriber = new BlockerSubscriber($this->workflowRegistry, $this->getLoggerMock(), $entityManager);
    }

    /**
     *  Teste la méthode onWorkflowBlockerTransition de la classe BlockerSubscriber
     * @return void
     */
    public function testOnWorkflowBlockerTransition(): void
    {
//        echo "\nBlockerSubscriberTest::testOnWorkflowBlockerTransition()\n";
        $this->setTransitionName('disable');

        // Create a dummy Customer object
        $customerMock = new Customer();
        $customerMock->setEmbBlocker(new Blocker('enabled')); // Initial blocker state
        $customerMock->setEmbState(new State('agreed')); // Initial customer state

        /** @var BlockerSubscriber $blockerSubscriber */
        $blockerSubscriber = $this->getSubscriber();

        // Call the method under test
        $this->getEventMock()
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn($customerMock);
        $this->getEventMock()
            ->expects($this->once())
            ->method('getWorkflowName')
            ->willReturn('blocker');
        $this->getTransitionMock()
            ->expects($this->once())
            ->method('getFroms')
            ->willReturn(['enabled']);
        $this->getTransitionMock()
            ->expects($this->once())
            ->method('getTos')
            ->willReturn(['disabled']);
        $this->getEventMock()
            ->expects($this->once())
            ->method('getTransition')
            ->willReturn($this->getTransitionMock());
        $blockerSubscriber->onWorkflowBlockerTransition($this->getEventMock());
        $blockerSubscriber->applyTransitionToWorkflow($customerMock, 'blocker', 'disable', $this->getWorkflowRegistry());


        $this->assertEquals('closed', $customerMock->getEmbState()->getState());
    }
}
