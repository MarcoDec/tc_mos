<?php

namespace App\Attribute\Couchdb\Abstract;

use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;

abstract class ManyToOne {
    /**
     * The fetching strategy to use for the association.
     */
    public string $fetch = Fetch::LAZY;

    public string|null $inversedBy;

    public string  $targetEntity;

    public function __construct(
        string $targetEntity,
        string $fetch = Fetch::LAZY,
        ?string $inversedBy = null
    ) {
        $this->targetEntity = $targetEntity;
        $this->fetch = $fetch;
        $this->inversedBy = $inversedBy;
    }
   /**
    * @param ReflectionAttribute $property
    * @return array<string,mixed>
    * @phpstan-ignore-next-line
    */
   #[ArrayShape(['targetEntity' => "mixed", 'inversedBy' => "mixed", 'fetch' => "mixed", 'type' => "string"])]
   public static function getPropertyData(ReflectionAttribute $property):array {
      $instance = $property->newInstance();
      /** @phpstan-ignore-next-line  */
      return [ 'targetEntity'=> $instance->targetEntity, 'inversedBy'=> $instance->inversedBy, 'fetch'=>$instance->fetch, 'type'=>self::class ];
   }
}
