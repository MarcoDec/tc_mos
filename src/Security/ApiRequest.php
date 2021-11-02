<?php

namespace App\Security;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
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
     * @return array{password: string, username: string}
     */
    #[ArrayShape(['password' => 'mixed', 'username' => 'mixed'])]
    public function getCredentials(): array {
        if (
            is_string($username = $this->getRequest()->get('username'))
            && is_string($password = $this->getRequest()->get('password'))
        ) {
            return ['password' => $password, 'username' => $username];
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
        return ($request = $this->getRequest())->has('username')
            && !empty($request->get('username'))
            && $request->has('password')
            && !empty($request->get('password'));
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

    private function getRequest(): InputBag {
        return $this->request->request;
    }
}
