<?php

namespace App\Filter;

final class OldRelationFilter extends NumericFilter {
    /**
     * @return mixed[]
     */
    public function getDescription(string $resourceClass): array {
        $description = [];
        $metadata = $this->getClassMetadata($resourceClass);

        $properties = $this->getProperties();
        if (null === $properties) {
            $properties = array_fill_keys($this->getClassMetadata($resourceClass)->getFieldNames(), null);
        }

        foreach ($properties as $property => $unused) {
            if (!$metadata->hasAssociation($property)) {
                continue;
            }

            $propertyName = $this->normalizePropertyName($property);
            $description[$propertyName] = [
                'property' => $propertyName,
                'type' => $this->getType($this->getDoctrineFieldType($property, $resourceClass)),
                'required' => false,
            ];
        }

        return $description;
    }

    protected function emptyPropertyCondition(string $property, string $resourceClass): bool {
        return !$this->getClassMetadata($resourceClass)->hasAssociation($property);
    }
}
