<?php

namespace App\Service;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class HistoryLogger
{
    private $redisService;

    public function __construct(RedisService $redisService) {
        $this->redisService = $redisService;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function logChange(
        string $entity,
        int $entityId,
        array $changes,
        string $message,
        string $userId): void {
        $timestamp = (new \DateTime())->format('Y-m-d\TH:i:s\Z');
        $entitySafe = str_replace('\\', '_', $entity);
        $timestampSafe = str_replace(':', '#', $timestamp);
        $key = sprintf('history_%s_%d_%s', $entitySafe, $entityId, $timestampSafe);
        $value = json_encode([
            'user_id' => $userId,
            'changes' => $changes,
            'timestamp' => $timestamp,
            'message' => $message
        ]);
        $redisClient = $this->redisService->getClient();
        $redisAdapter = new RedisAdapter($redisClient);
        // create a new cache item
        $cacheItem = $redisAdapter->getItem($key);
        $cacheItem->set($value);
        // save the cache item
        $redisAdapter->save($cacheItem);
    }
    public function getLogs(string $entity, int $entityId): array {
        $entitySafe = str_replace('\\', '_', $entity);
        $redisClient = $this->redisService->getClient();
        $redisAdapter = new RedisAdapter($redisClient);
        $keys = $redisClient->keys("history_{$entitySafe}_{$entityId}_*");
        $logs = [];
        foreach ($keys as $key) {
            $cacheItem = $redisAdapter->getItem($key);
            $logs[] = json_decode($cacheItem->get(), true);
        }
        return $logs;
    }
}