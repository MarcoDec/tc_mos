<?php

namespace App\Controller\Workflow;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Entity;
use App\Service\HistoryLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;

class ApplyWorkflowController
{
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly Registry $workflowRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly HistoryLogger $historyLogger
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        /** @var Entity $entity */
        $entity = $this->iriConverter->getItemFromIri($content['iri']);
        if (!$entity) {
            return New JsonResponse(['error' => 'Entity not found'], 404);
        }
        $transitionToApply = $content['transition'];
        $workflowName = $content['workflowName'];
        $message = $content['message'];
        $workflow = $this->workflowRegistry->get($entity, $workflowName);
        if (!$workflow->can($entity, $transitionToApply)) {
            return new JsonResponse(['error' => 'Transition not allowed'], 400);
        }
        // On récupère l'état de workflow courant de l'entité
        $currentPlace = $workflow->getMarking($entity)->getPlaces();
        // On récupère l'état activé de currentPlace
        $currentPlace = array_keys($currentPlace)[0];
        // On récupère l'état de workflow après application de la transition
        $nextPlace = $workflow->getEnabledTransition($entity, $transitionToApply)->getTos();
        $workflow->apply($entity, $transitionToApply);
        $user = $this->security->getUser();
        $this->entityManager->flush();
        $changes = [
            'workflow' => $workflowName,
            'transition' => $transitionToApply,
            'initialState' => $currentPlace,
            'finalState' => $nextPlace[0]
        ];
        $this->historyLogger->logChange($entity::class, $entity->getId(), $changes, $message, $user->getUserIdentifier());
        return new JsonResponse('Transition applied', 200);
    }
}