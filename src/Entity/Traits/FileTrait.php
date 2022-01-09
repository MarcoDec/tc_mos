<?php

namespace App\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation as Serializer;

trait FileTrait {
    #[Serializer\Groups(['write:file'])]
    private ?File $file = null;

    public function getFile(): ?File {
        return $this->file;
    }

    public function getFilepath(): ?string {
        return $this->file?->getPathname();
    }

    public function setFile(?File $file): self {
        $this->file = $file;
        return $this;
    }
}
