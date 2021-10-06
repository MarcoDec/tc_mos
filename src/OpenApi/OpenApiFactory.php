<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(private OpenApiFactoryInterface $decorated) {
    }

    /**
     * @param mixed[] $context
     */
    public function __invoke(array $context = []): OpenApi {
        return (new OpenApiWrapper($this->decorated->__invoke($context)))
            ->createSecuritySchema('cookieAuth', ['in' => 'cookie', 'name' => 'PHPSESSID', 'type' => 'apiKey'])
            ->createSchema('Auth', [
                'properties' => [
                    'password' => [
                        'description' => 'mot de passe',
                        'example' => 'super',
                        'type' => 'string',
                    ],
                    'username' => [
                        'description' => 'identifiant',
                        'example' => 'super',
                        'type' => 'string',
                    ],
                ],
                'type' => 'object',
            ])
            ->addPath(
                id: 'login',
                path: '/api/login',
                tag: 'Auth',
                responses: [200 => ['description' => 'Utilisateur connecté', 'schema' => ['tag' => 'Employee', 'value' => 'Employee-read']]],
                description: 'Connexion',
                requestBody: ['description' => 'Identifiants', 'schema' => 'Auth']
            )
            ->addPath(
                id: 'logout',
                path: '/api/logout',
                tag: 'Auth',
                responses: [204 => ['description' => 'Déconnexion réussie']],
                description: 'Déconnexion'
            )
            ->hidePaths()
            ->getApi();
    }
}
