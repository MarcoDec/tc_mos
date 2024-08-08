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
        // dump([
        //     'entity' => $entity,
        //     'entitySafe' => $entitySafe,
        //     'timestamp' => $timestamp,
        //     'timestampSafe' => $timestampSafe
        // ]);
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
}