<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class DeliveryNoteSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function onWorkflowDeliveryNoteTransition(Event $event): void
    {
        $transition = $event->getTransition()->getName();

        // Check if the transition is to 'reject'
        if ($transition === 'reject') {
            
            $this->logger->info('Transition to "reject" detected.');
            $deliveryNote = $event->getSubject(); // Assuming your subject is the DeliveryNote

            $email = (new Email())
            ->from('test@gmail.com')
            ->to('test2@gmail.com')
            ->subject('Delivery Note Rejected')
            ->text('The delivery note ' . $deliveryNote->getId() . ' has been rejected.');

        try {
            $this->mailer->send($email);
            $this->logger->info('Email sent successfully.');
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send email. ' . $e->getMessage());
        }
    
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.delivery_note.transition' => 'onWorkflowDeliveryNoteTransition',
        ];
    }
}