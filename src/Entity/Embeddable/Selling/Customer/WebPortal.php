<?php

namespace App\Entity\Embeddable\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class WebPortal {
    #[
        ApiProperty(description: 'Mot de passe', example: 'C@ble3j!'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:accounting', 'write:customer:logistics'])
    ]
    private ?string $password = null;

    #[
        ApiProperty(description: 'URL', example: 'https://www.monsite.fr'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:accounting', 'write:customer:logistics'])
    ]
    private ?string $url = null;

    #[
        ApiProperty(description: 'Nom d\'utilisateur', example: 'Patrick'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:accounting', 'write:customer:logistics'])
    ]
    private ?string $username = null;

    final public function getPassword(): ?string {
        return $this->password;
    }

    final public function getUrl(): ?string {
        return $this->url;
    }

    final public function getUsername(): ?string {
        return $this->username;
    }

    final public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    final public function setUrl(?string $url): self {
        $this->url = $url;
        return $this;
    }

    final public function setUsername(?string $username): self {
        $this->username = $username;
        return $this;
    }
}
