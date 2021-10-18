<?php

namespace App\Entity\Interfaces;

use Symfony\Component\HttpFoundation\File\File;

interface FileEntity {
    public function getFile(): ?File;

    public function getFilepath(): ?string;

    public function getId(): ?int;

    public function setFile(?File $file): self;
}
