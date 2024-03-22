<?php
namespace App\Entity\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NewFileDetectedEvent extends Event
{
    private string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}