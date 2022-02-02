<?php

namespace App\OpenApi\Factory;

use ApiPlatform\Core\OpenApi\Model\Link;

final class Links {
    /** @var array<string, Link> */
    private array $links = [];

    /**
     * @return array<string, Link>
     */
    public function get(string $operationId): array {
        return isset($this->links[$operationId])
            ? [ucfirst($operationId) => $this->links[$operationId]]
            : [];
    }

    public function put(string $operationId, Link $link): self {
        $this->links[$operationId] = $link;
        return $this;
    }
}
