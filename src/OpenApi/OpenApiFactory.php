<?php

namespace App\OpenApi;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OpenApiFactory implements OpenApiFactoryInterface {
    /** @var array<string, class-string> */
    private array $resources = [];

    public function __construct(
        private DashPathSegmentNameGenerator $dashGenerator,
        private OpenApiFactoryInterface $decorated,
        ResourceMetadataFactoryInterface $metadataFactory,
        ResourceNameCollectionFactoryInterface $resources,
        private UrlGeneratorInterface $urlGenerator
    ) {
        foreach ($resources->create() as $resource) {
            /** @var class-string $resource */
            $this->resources[$metadataFactory->create($resource)->getShortName()] = $resource;
        }
    }

    /**
     * @param mixed[] $context
     */
    public function __invoke(array $context = []): OpenApi {
        $securitySchema = 'cookieAuth';
        return (new OpenApiWrapper($this->decorated->__invoke($context), $this->dashGenerator, $this))
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
            ->createSchema('Violation', [
                'properties' => [
                    'code' => [
                        'example' => 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                        'type' => 'string'
                    ],
                    'message' => [
                        'example' => 'This value should not be blank.',
                        'type' => 'string'
                    ],
                    'propertyPath' => [
                        'example' => 'name',
                        'type' => 'string'
                    ]
                ],
                'required' => ['code', 'message', 'propertyPath']
            ])
            ->createSchema('Violations', [
                'properties' => [
                    '@context' => [
                        'example' => '/api/contexts/ConstraintViolationList',
                        'type' => 'string'
                    ],
                    '@type' => [
                        'example' => 'ConstraintViolationList',
                        'type' => 'string'
                    ],
                    'hydra:title' => [
                        'example' => 'An error occurred',
                        'type' => 'string'
                    ],
                    'hydra:description' => [
                        'example' => "name: This value should not be blank.\nname: This value should not be blank.",
                        'type' => 'string'
                    ],
                    'violations' => [
                        'items' => [
                            '$ref' => '#/components/schemas/Violation'
                        ],
                        'type' => 'array'
                    ]
                ],
                'required' => ['violations'],
                'type' => 'object'
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
            ->setJsonLdDoc()
            ->getApi();
    }

    /**
     * @return class-string
     */
    public function getResourceClass(string $resource): string {
        return $this->resources[$resource];
    }

    private function getLogin(): string {
        return $this->urlGenerator->generate('login');
    }
}
