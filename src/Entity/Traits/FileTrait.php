<?php

namespace App\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;

trait FileTrait {
    private ?File $file = null;

    final public function getFile(): ?File {
        return $this->file;
    }

    public function getFilepath(): ?string {
        return $this->file?->getPathname();
    }

    final public function setFile(?File $file): self {
        $this->file = $file;
        return $this;
    }
}
