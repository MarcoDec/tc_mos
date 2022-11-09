<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Operation;
use App\Collection;
use ArrayObject;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Serializer\Annotation as Serializer;

class SchemaFactory implements SchemaFactoryInterface {
    public function __construct(private readonly SchemaFactoryInterface $wrapped) {
    }

    /** @param string[] $context */
    private static function filter(array $context, ReflectionMethod|ReflectionProperty $member): ?bool {
        return self::inGroup($context, $member) === false ? null : self::isRequired($member);
    }

    /** @param string[] $context */
    private static function inGroup(array $context, ReflectionMethod|ReflectionProperty $member): bool {
        $attributes = $member->getAttributes(Serializer\Groups::class);
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $groups = $instance->getGroups();
            foreach ($context as $group) {
                if (in_array($group, $groups, true)) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function isRequired(ReflectionMethod|ReflectionProperty $member): ?bool {
        $attributes = $member->getAttributes(ApiProperty::class);
        foreach ($attributes as $attribute) {
            $property = $attribute->newInstance();
            if (null !== $required = $property->isRequired()) {
                return $required;
            }
        }
        return null;
    }

    /**
     * @param class-string $className
     * @param mixed[]|null $serializerContext
     */
    public function buildSchema(string $className, string $format = 'json', string $type = Schema::TYPE_OUTPUT, ?Operation $operation = null, ?Schema $schema = null, ?array $serializerContext = null, bool $forceCollection = false): Schema {
        $schema = $this->wrapped->buildSchema($className, 'json', $type, $operation, $schema, $serializerContext, $forceCollection);
        if (empty($operation)) {
            return $schema;
        }
        if (empty($key = $schema->getRootDefinitionKey())) {
            return $schema;
        }
        /** @var ArrayObject<string, mixed> $definition */
        $definition = $schema->getDefinitions()[$key];
        if ($definition->offsetExists('required') === false) {
            return $schema;
        }
        /** @var string[] $required */
        $required = $definition->offsetGet('required');
        if (empty($required)) {
            return $schema;
        }
        $context = $type === Schema::TYPE_OUTPUT ? $operation->getNormalizationContext() : $operation->getDenormalizationContext();
        if (empty($context)) {
            return $schema;
        }
        $context = $context['groups'] ?? [];
        if (empty($context)) {
            return $schema;
        }
        $refl = new ReflectionClass($className);
        $definition->offsetSet(
            key: 'required',
            value: (new Collection($required))
                ->filter(static fn (string $propertyName): bool => $refl->hasProperty($propertyName) && self::filter($context, $refl->getProperty($propertyName)) !== false
                    && $refl->hasMethod($get = 'get'.ucfirst($propertyName)) && self::filter($context, $refl->getMethod($get)) !== false
                    && $refl->hasMethod($set = 'set'.ucfirst($propertyName)) && self::filter($context, $refl->getMethod($set)) !== false)
                ->values()
        );
        return $schema;
    }
}
