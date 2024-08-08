<?php

namespace App\Controller\Workflow;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Entity;
use App\Service\HistoryLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Registry;

class GetHistoryController
{
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private Registry $workflowRegistry,
        private HistoryLogger $historyLogger
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        /** @var Entity $entity */
        $entity = $this->iriConverter->getItemFromIri($content['iri']);
        if (!$entity) {
            return new JsonResponse(['error' => 'Entity not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        // Prepare the response with workflow history
        $logs = $this->historyLogger->getLogs(get_class($entity), $entity->getId());

        $response = [];
        foreach ($logs as $log) {
            $response[] = [
                'user_id' => $log['user_id'],
                'changes' => $log['changes'],
                'timestamp' => $log['timestamp'],
                'message' => $log['message'],
            ];
        }

        return new JsonResponse($response);
    }
}
