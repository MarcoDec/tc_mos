<?php

namespace App\Entity\Couchdb;

use Doctrine\CouchDB\HTTP\Response;
use Exception;

/**
 * Objet représentant un document CouchDB
 * Il est créé à partir d'une Réponse Doctrine\CouchDB\HTTP\Response.
 */
class Document {
    /**
     * @var array<string,mixed>
     */
    private array $content;

    private string $id;
    private string $rev;

    public function __construct(Response $couchdbDocResponse) {
        $this->id = $couchdbDocResponse->body['_id'];
        $this->rev = $couchdbDocResponse->body['_rev'];
        $this->content = $couchdbDocResponse->body['content'] ?? [];
    }

    public static function getName(string $id): string {
        return 'couchdb_document_'.md5($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array {
        return $this->content;
    }

    public function getId(): mixed {
        return $this->id;
    }

    /**
     * @throws Exception
     */
    public function getItem(int $id): ?Item {
        $itemData = collect($this->content)->filter(static fn ($item) => $item['id'] === $id)->first();
        if ($itemData === null) {
            throw new Exception("$this->id ne contient pas d'élément $id", 500);
        }
        return new Item($this->id, $itemData);
    }

    /**
     * @param array<mixed> $conditions
     *
     * @return array<mixed>
     */
    public function getItemsWhere(array $conditions): array {
        return collect($this->content)
            ->filter(
                static function ($item) use ($conditions) {
                    $test = true;
                    foreach ($conditions as $key => $value) {
                        if ($item[$key] != $value) {
                            $test = false;
                        }
                    }
                    return $test;
                }
            )->toArray();
    }

    public function getRev(): mixed {
        return $this->rev;
    }

    /**
     * @param array<mixed> $content
     *
     * @return Document
     */
    public function setContent(array $content): self {
        $this->content = $content;
        return $this;
    }

    /**
     * @param mixed|string $id
     */
    public function setId(mixed $id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed|string $rev
     */
    public function setRev(mixed $rev): self {
        $this->rev = $rev;
        return $this;
    }
}
