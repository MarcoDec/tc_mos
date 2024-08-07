<?php

namespace App\Entity\Needs;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/api/needs/products',
            ],
            'get_components' => [
                'method' => 'GET',
                'path' => '/api/needs/components',
            ],
        ],
        itemOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/api/needs/{id}',
            ],
        ]
    )

]
class Needs {
    private $componentChartsData;
    private $components;
    private $customers;

    /**
     * @ApiProperty(identifier=true)
     */
    private $id;

    private $productChartsData;
    private $productFamilies;
    private $products;
    private $suppliers;

    public function getComponentChartsData(): ?array {
        return $this->componentChartsData;
    }

    public function getComponents(): ?array {
        return $this->components;
    }

    public function getCustomers(): ?array {
        return $this->customers;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getProductChartsData(): ?array {
        return $this->productChartsData;
    }

    public function getProductFamilies(): ?array {
        return $this->productFamilies;
    }

    public function getProducts(): ?array {
        return $this->products;
    }

    public function getSuppliers(): ?array {
        return $this->suppliers;
    }

    public function setComponentChartsData(array $componentChartsData): self {
        $this->componentChartsData = $componentChartsData;

        return $this;
    }

    public function setComponents(array $components): self {
        $this->components = $components;

        return $this;
    }

    public function setCustomers(array $customers): self {
        $this->customers = $customers;

        return $this;
    }

    public function setId(int $id) {
        return $this->id = $id;
    }

    public function setProductChartsData(array $productChartsData): self {
        $this->productChartsData = $productChartsData;

        return $this;
    }

    public function setProductFamilies(array $productFamilies): self {
        $this->productFamilies = $productFamilies;

        return $this;
    }

    public function setProducts(array $products): self {
        $this->products = $products;

        return $this;
    }

    public function setSuppliers(array $suppliers): self {
        $this->suppliers = $suppliers;

        return $this;
    }
}
