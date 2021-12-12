<?php

namespace App\Attribute\Couchdb\Abstract;

use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;

abstract class OneToMany {
    /**
     * The fetching strategy to use for the association.
     */
    public string $fetch = Fetch::LAZY;

    public bool $owned;

    public string $targetEntity;

    public function __construct(
        string $targetEntity,
        string $fetch = Fetch::LAZY,
        bool $owned = true,
    ) {
        $this->owned = $owned;
        $this->targetEntity = $targetEntity;
        $this->fetch = $fetch;
    }
   /**
    * @param ReflectionAttribute $property
    * @return array<string,mixed>
    * @phpstan-ignore-next-line
    */
   #[ArrayShape(['targetEntity' => "mixed", 'owned' => "mixed", 'fetch' => "mixed", 'type' => "string"])]
   public static function getPropertyData(ReflectionAttribute $property):array {
      $instance = $property->newInstance();
      /** @phpstan-ignore-next-line  */
      return [ 'targetEntity'=> $instance->targetEntity, 'owned'=> $instance->owned, 'fetch'=>$instance->fetch, 'type'=>self::class ];
   }
}
