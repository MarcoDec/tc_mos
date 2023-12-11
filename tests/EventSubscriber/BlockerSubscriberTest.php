<?php

namespace App\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\EventSubscriber\BlockerSubscriber;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Selling\Customer\State;
use Symfony\Component\Workflow\Workflow;

class BlockerSubscriberTest extends TestCase
{
    public function testOnWorkflowBlockerTransition(): void
    {
        $workflowRegistryMock = $this->createMock(Registry::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // Create a dummy Customer object
        $customerMock = new Customer();
        $customerMock->setEmbBlocker(new Blocker('enabled')); // Initial blocker state
        $customerMock->setEmbState(new State('agreed')); // Initial customer state

        // Create the BlockerSubscriber instance
        $blockerSubscriber = new BlockerSubscriber($workflowRegistryMock, $loggerMock, $entityManagerMock);

        // Create a dummy Event
        $eventMock = $this->createMock(\Symfony\Component\Workflow\Event\Event::class);
        $eventMock
            ->method('getSubject')
            ->willReturn($customerMock);
        $eventMock
            ->method('getWorkflowName')
            ->willReturn('blocker');
        $eventMock
            ->method('getTransition')
            ->willReturn($this->createMock(\Symfony\Component\Workflow\Transition::class, ['getName' => 'disable']));

        // Create a dummy Workflow for the blocker
        $blockerWorkflowMock = $this->createMock(Workflow::class);
        $blockerWorkflowMock
            ->method('can')
            ->willReturn(true);
        $blockerWorkflowMock
            ->expects($this->once())
            ->method('apply')
            ->with($customerMock, 'disable');

        // Create a dummy Workflow for the customer
        $customerWorkflowMock = $this->createMock(Workflow::class);
        $customerWorkflowMock
            ->method('can')
            ->willReturn(true);
        $customerWorkflowMock
            ->expects($this->once())
            ->method('apply')
            ->with($customerMock, 'close');

        // Set expectations on the workflowRegistryMock
        $workflowRegistryMock
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([$customerMock, 'blocker'], [$customerMock, 'customer'])
            ->willReturnOnConsecutiveCalls($blockerWorkflowMock, $customerWorkflowMock);

        // Call the method under test
        $blockerSubscriber->onWorkflowBlockerTransition($eventMock);
        $blockerSubscriber->applyTransitionToWorkflow($customerMock, 'blocker', 'disable', $workflowRegistryMock);

        // Add assertions to check the final state after transitions
       // $this->assertEquals('disabled', $customerMock->getEmbBlocker()->getState());
        $this->assertEquals('close', $customerMock->getEmbState()->getState());
    }
}
