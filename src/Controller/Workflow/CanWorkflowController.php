<?php

namespace App\Controller\Workflow;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Registry;

class CanWorkflowController
{
    public function __construct(private readonly IriConverterInterface $iriConverter, private Registry $workflowRegistry)
    {
    }
    public function __invoke(Request $request): array
    {
        $content = json_decode($request->getContent(), true);
        /** @var Entity $entity */
        $entity = $this->iriConverter->getItemFromIri($content['iri']);
        if (!$entity) {
            return ['error' => 'Entity not found'];
        }
        //Si l'entité existe, on récupère les workflows associés
        $workflows = [];
        foreach ($this->workflowRegistry->all($entity) as $workflow) {
            $workflows[] = $workflow;
        }
        $response = [];
        foreach ($workflows as $workflow) {
            $workflowName = $workflow->getName();
            $currentState = key($workflow->getMarking($entity)->getPlaces());
            $possibleTransitions = $workflow->getEnabledTransitions($entity);
            $transitions = [];
            foreach ($possibleTransitions as $transition) {
                $transitions[] = $transition->getName();
            }
            $response[] = [
                'workflowName' => $workflowName,
                'currentState' => $currentState,
                'can' => $transitions
            ];
        }
        return $response;
    }
}