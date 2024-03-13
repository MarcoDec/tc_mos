<?php

namespace App\Controller\Workflow;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Registry;

class ApplyWorkflowController
{
    public function __construct(private readonly IriConverterInterface $iriConverter, private readonly Registry $workflowRegistry, private EntityManagerInterface $entityManager)
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
        $workflow = $this->workflowRegistry->get($entity, $workflowName);
        if (!$workflow->can($entity, $transitionToApply)) {
            return new JsonResponse(['error' => 'Transition not allowed'], 400);
        }
        $workflow->apply($entity, $transitionToApply);
        $this->entityManager->flush();
        return new JsonResponse('Transition applied', 200);
    }
}