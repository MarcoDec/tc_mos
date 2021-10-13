<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(private OpenApiFactoryInterface $decorated, private UrlGeneratorInterface $urlGenerator) {
    }

    /**
     * @param mixed[] $context
     */
    public function __invoke(array $context = []): OpenApi {
        $securitySchema = 'cookieAuth';
        return (new OpenApiWrapper($this->decorated->__invoke($context)))
            ->createSecuritySchema($securitySchema, ['in' => 'cookie', 'name' => 'PHPSESSID', 'type' => 'apiKey'])
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
                path: $this->getLogin(),
                tag: 'Auth',
                responses: [
                    200 => [
                        'description' => 'Utilisateur connecté',
                        'schema' => ['tag' => 'Employee', 'value' => 'Employee-read']
                    ],
                    403 => ['description' => 'hidden'],
                ],
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
            ->setDefaultResponses()
            ->securize($this->getLogin(), $securitySchema)
            ->getApi();
    }

    private function getLogin(): string {
        return $this->urlGenerator->generate('login');
    }
}
