<?php

namespace App\Entity\Needs;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/api/needs"
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/api/needs/{id}"
 *         }
 *     }
 * )
 */
class Needs
{ 
    /**
     * @ApiProperty(identifier=true)
     */
    private $id;
    private $componentChartsData;
    private $components;
    private $customers;
    private $productChartsData;
    private $productFamilies;
    private $products;
    private $suppliers;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id)
    {
        return $this->id= $id;
    }

    public function getComponentChartsData(): ?array
    {
        return $this->componentChartsData;
    }

    public function setComponentChartsData(array $componentChartsData): self
    {
        $this->componentChartsData = $componentChartsData;

        return $this;
    }

    public function getComponents(): ?array
    {
        return $this->components;
    }

    public function setComponents(array $components): self
    {
        $this->components = $components;

        return $this;
    }

    public function getCustomers(): ?array
    {
        return $this->customers;
    }

    public function setCustomers(array $customers): self
    {
        $this->customers = $customers;

        return $this;
    }

    public function getProductChartsData(): ?array
    {
        return $this->productChartsData;
    }

    public function setProductChartsData(array $productChartsData): self
    {
        $this->productChartsData = $productChartsData;

        return $this;
    }

    public function getProductFamilies(): ?array
    {
        return $this->productFamilies;
    }

    public function setProductFamilies(array $productFamilies): self
    {
        $this->productFamilies = $productFamilies;

        return $this;
    }

    public function getProducts(): ?array
    {
        return $this->products;
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getSuppliers(): ?array
    {
        return $this->suppliers;
    }

    public function setSuppliers(array $suppliers): self
    {
        $this->suppliers = $suppliers;

        return $this;
    }
}
