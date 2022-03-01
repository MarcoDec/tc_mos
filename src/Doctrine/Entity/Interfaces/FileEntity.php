<?php

namespace App\Doctrine\Entity\Interfaces;

use Symfony\Component\HttpFoundation\File\File;

interface FileEntity {
    public function getFile(): ?File;

    public function getFilepath(): ?string;

    public function getId(): ?int;

    public function setFile(?File $file): self;
}
