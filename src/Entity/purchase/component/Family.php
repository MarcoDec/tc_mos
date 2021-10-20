<?php

namespace App\Entity\purchase\component;

use App\Entity\Entity;
use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\FamilyTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @method Family[]|Collection getChildren()
 *
 * @ORM\AttributeOverrides({@ORM\AttributeOverride(column=@ORM\Column(nullable=true), name="code")})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="component_family")
 */
class Family extends Entity
{
    use CodeTrait;
    use FamilyTrait {
        __construct as private constructFamily;
        addChild as private addFamilyChild;
        getParent as private getFamilyParent;
        removeChild as private removeFamilyChild;
        setParent as private setFamilyParent;
    }
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\Column(options={"default": false}, type="boolean")
     *
     * @var boolean
     */
    private $copperable = false;
    /**
     * @ORM\ManyToOne(fetch="EAGER", inversedBy="children", targetEntity=Family::class)
     *
     * @var Family|null
     */

    private $parent;
    public function __construct() {
        $this->constructFamily();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isCopperable(): bool
    {
        return $this->copperable;
    }

    /**
     * @param bool $copperable
     */
    public function setCopperable(bool $copperable): void
    {
        $this->copperable = $copperable;
    }

    /**
     * @return Family|null
     */
    public function getParent(): ?Family
    {
        return $this->parent;
    }

    /**
     * @param Family|null $parent
     */
    public function setParent(?Family $parent): void
    {
        $this->parent = $parent;
    }

}