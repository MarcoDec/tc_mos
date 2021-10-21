<?php

namespace App\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation as Serializer;

trait FileTrait {
    #[Serializer\Groups(['write:file'])]
    private ?File $file = null;

    final public function getFile(): ?File {
        return $this->file;
    }

    final public function getFilepath(): ?string {
        return $this->file?->getPathname();
    }

    final public function setFile(?File $file): self {
        $this->file = $file;
        return $this;
    }
}
