<?php

namespace App\Security;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\LogicException;

final class ApiRequest {
    public function __construct(private Request $request) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getAuthorization(): string {
        if (!empty($authorization = $this->getHeaders()->get('Authorization'))) {
            return $authorization;
        }
        throw new InvalidArgumentException('Missing Authorization headers');
    }

    /**
     * @return array{password?: string, username?: string}
     */
    #[ArrayShape(['password' => 'string', 'username' => 'string'])]
    public function getContent(): array {
        return is_string($content = $this->request->getContent())
        && !empty($decoded = json_decode($content, true))
            ? $decoded
            : [];
    }

    /**
     * @return array{password: string, username: string}
     */
    #[ArrayShape(['password' => 'string', 'username' => 'string'])]
    public function getCredentials(): array {
        $content = $this->getContent();
        if (
            isset($content['username']) && !empty($content['username'])
            && isset($content['password']) && !empty($content['password'])
        ) {
            return ['password' => $content['password'], 'username' => $content['username']];
        }
        throw new LogicException('No username or password found. Did you call hasCredentials before?');
    }

    public function getRequestUri(): string {
        return $this->request->getRequestUri();
    }

    /**
     * @return array{requestedUri: string, token: string}
     */
    #[ArrayShape(['requestedUri' => 'string', 'token' => 'string'])]
    public function getToken(): array {
        return [
            'requestedUri' => $this->getRequestUri(),
            'token' => removeStart($this->getAuthorization(), 'Bearer ')
        ];
    }

    public function hasAuthorization(): bool {
        return ($headers = $this->getHeaders())->has('Authorization') && !empty($headers->get('Authorization'));
    }

    public function hasCredentials(): bool {
        $content = $this->getContent();
        return isset($content['username'])
            && isset($content['password']);
    }

    public function isApiUri(): bool {
        return str_starts_with($this->getRequestUri(), '/api/');
    }

    public function isBearerAuthorization(): bool {
        try {
            return str_starts_with($this->getAuthorization(), 'Bearer ');
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    private function getHeaders(): HeaderBag {
        return $this->request->headers;
    }
}
