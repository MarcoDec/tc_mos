<?php
namespace App\Entity\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NewFileDetectedEvent extends Event
{
    private string $fileName;
    private string $jsonContent;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getJsonContent(): string
    {
        return $this->jsonContent;
    }

    public function setJsonContent(string $jsonContent): void
    {
        $this->jsonContent = $jsonContent;
    }

}