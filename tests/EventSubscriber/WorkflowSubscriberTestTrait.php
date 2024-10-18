<?php
namespace App\Tests\EventSubscriber;

use App\Entity\Entity;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\WorkflowInterface;
// use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Collections\ArrayCollection;

trait WorkflowSubscriberTestTrait
{
    /**
     * @var Registry
     */
    private Registry $workflowRegistry;

    /**
     * @var LoggerInterface|MockObject
     */
    private LoggerInterface|MockObject $loggerMock;

    /**
     * @var Event|MockObject
     */
    private Event|MockObject $eventMock;

    /**
     * @var MockObject|Workflow
     */
    private MockObject|Workflow $workflowMock;

    /**
     * @var Transition|MockObject
     */
    private MockObject|Transition $transitionMock;

    /**
     * @var EventSubscriberInterface
     */
    private EventSubscriberInterface $subscriber;

    /**
     * @var string
     */
    private string $transitionName;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $currentWorkflow;
    private Entity $entity;
    private string $workflowName;

    /**
     * @return void
     * @throws Exception
     */
    protected function traitSetUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->workflowRegistry = self::getContainer()->get(Registry::class);

        $this->currentWorkflow = $this->workflowRegistry->get($this->entity, $this->workflowName);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->eventMock = $this->createMock(Event::class);
        $this->transitionMock = $this->createMock(Transition::class);
    }

    public function getWorkflow($workflowName): WorkflowInterface
    {
        return $this->workflowRegistry->get($this->entity, $workflowName);
    }

    /**
     * @return string
     */
    public function getTransitionName(): string
    {
        return $this->transitionName;
    }

    /**
     * @param string $transitionName
     */
    public function setTransitionName(string $transitionName): void
    {
        $this->transitionName = $transitionName;
        $this->transitionMock->method('getName')->willReturn($this->transitionName);
    }

    /**
     * @return EventSubscriberInterface
     */
    public function getSubscriber(): EventSubscriberInterface
    {
        return $this->subscriber;
    }

    /**
     * @param string $subscriberClassName
     * @param Registry|null $registry
     * @param LoggerInterface|null $logger
     */
    public function setSubscriber(
        string $subscriberClassName,
        ?Registry $registry = null,
        ?LoggerInterface $logger = null): void
    {
        $this->workflowRegistry = $registry ?? $this->workflowRegistry;
        $this->loggerMock = $logger ?? $this->loggerMock;
        $this->subscriber = new $subscriberClassName($this->workflowRegistry, $this->loggerMock);
    }

    /**
     * @return Registry|MockObject
     */
    public function getWorkflowRegistry(): Registry
    {
        return $this->workflowRegistry;
    }

    /**
     * @param Registry $workflowRegistry
     */
    public function setWorkflowRegistry(Registry $workflowRegistry): void
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * @return LoggerInterface|MockObject
     */
    public function getLoggerMock(): LoggerInterface|MockObject
    {
        return $this->loggerMock;
    }

    public function setLoggerMock(LoggerInterface|MockObject $loggerMock): void
    {
        $this->loggerMock = $loggerMock;
    }

    public function getEventMock(): Event|MockObject
    {
        return $this->eventMock;
    }

    public function setEventMock(Event|MockObject $eventMock): void
    {
        $this->eventMock = $eventMock;
    }

    public function getWorkflowMock(): WorkflowInterface
    {
        return $this->currentWorkflow;
    }

    public function getTransitionMock(): MockObject|Transition
    {
        return $this->transitionMock;
    }

    public function setTransitionMock(MockObject|Transition $transitionMock): void
    {
        $this->transitionMock = $transitionMock;
    }

    public function getCurrentWorkflow(): WorkflowInterface
    {
        return $this->currentWorkflow;
    }

    public function setCurrentWorkflow(WorkflowInterface $currentWorkflow): void
    {
        $this->currentWorkflow = $currentWorkflow;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    public function setWorkflowName(string $workflowName): void
    {
        $this->workflowName = $workflowName;
    }

}
