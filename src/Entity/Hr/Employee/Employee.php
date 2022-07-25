<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Api\Token;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Hr\Employee\CurrentPlace;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Traits\BarCodeTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['userEnabled']),
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'initials' => 'partial',
        'name' => 'partial',
        'surname' => 'partial'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: ['initials', 'name', 'surname']),
    ApiResource(
        description: 'Employé',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les employés',
                    'summary' => 'Récupère les employés'
                ],
                'normalization_context' => [
                    'groups' => ['read:id', 'read:name', 'read:employee:collection'],
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un employé',
                    'summary' => 'Créer un employé'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')',
                'denormalization_context' => [
                    'groups' => ['write:employee:post']
                ]
            ]
        ],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un employé',
                    'summary' => 'Récupère un employé'
                ]
            ],
            'patch' => [
                'path' => '/employees/{id}/{process}',
                'requirements' => ['process' => '\w+'],
                'openapi_context' => [
                    'description' => 'Modifier un employé',
                    'summary' => 'Modifier un employé',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'enum' => ['main', 'hr', 'it', 'production']
                        ]
                    ]]
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')',
            ],
            'promote' => [
                'method' => 'PATCH',
                'path' => '/employees/{id}/promote',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Passer un employé à un nouveau statut',
                    'summary' => 'Passer un employé à un nouveau statut',
                ],
                'denormalization_context' => [
                    'groups' => ['write:employee:promote']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:employee', 'read:name', 'read:user', 'read:company'],
            'openapi_definition_name' => 'Employee-read'
        ]
    ),
    ORM\Entity
]
class Employee extends Entity implements BarCodeInterface, PasswordAuthenticatedUserInterface, UserInterface {
    use BarCodeTrait;

    public const GENDER_TYPE_FRMALE = 'female';
    public const GENDER_TYPE_MALE = 'male';
    public const GENDER_TYPES = [self::GENDER_TYPE_MALE, self::GENDER_TYPE_FRMALE];
    public const SITUATION_TYPE_MARRIED = 'married';
    public const SITUATION_TYPE_SINGLE = 'single';
    public const SITUATION_TYPE_WINDOWED = 'windowed';
    public const SITUATION_TYPES = [
        self::SITUATION_TYPE_MARRIED,
        self::SITUATION_TYPE_SINGLE,
        self::SITUATION_TYPE_WINDOWED,
    ];

    #[
        Assert\Valid,
        ORM\Embedded(Address::class),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private Address $address;

    /** @var Collection<int, Token> */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Token::class)]
    private Collection $apiTokens;

    #[
        ApiProperty(description: 'Ville de naissance', example: 'Nancy'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $birthCity = null;

    #[
        ApiProperty(description: 'Date de naissance', example: '1980-24-03'),
        Assert\Date,
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?DateTimeImmutable $birthday = null;

    #[
        ApiProperty(description: 'Companie', example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:employee', 'read:employee:collection'])
    ]
    private CurrentPlace $currentPlace;

    #[ORM\Embedded]
    private Roles $embRoles;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        Assert\Date,
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?DateTimeImmutable $entryDate = null;

    #[
        ApiProperty(description: 'Sexe', example: self::GENDER_TYPE_MALE),
        Assert\Choice(choices: self::GENDER_TYPES),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $gender = null;

    #[
        ApiProperty(description: 'Initiales', example: 'C.R.'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post', 'read:employee:collection'])
    ]
    private ?string $initials = null;

    #[
        ApiProperty(description: 'Niveau d\'étude', example: 'Bac+5'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $levelOfStudy = null;

    #[
        ApiProperty(description: 'Manager', readableLink: false, example: '/api/employees/3'),
        ORM\ManyToOne(targetEntity: self::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?self $manager = null;

    #[
        ApiProperty(description: 'Prénom', required: true, example: 'Super'),
        ORM\Column(length: 30),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum dolor sit am'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $notes = null;

    #[ORM\Column(type: 'char', length: 60)]
    private ?string $password = null;

    #[
        ApiProperty(description: 'Mot de passe', example: 'L0r3m@Ipsum'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post'])
    ]
    private ?string $plainPassword = null;

    #[
        ApiProperty(description: 'Situation', example: self::SITUATION_TYPE_MARRIED),
        Assert\Choice(choices: self::SITUATION_TYPES),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $situation = null;

    #[
        ApiProperty(description: 'Numéro de sécurité sociale', example: '1 80 12 75 200 200 36'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $socialSecurityNumber = null;

    #[
        ApiProperty(description: 'Nom', example: 'Roosevelt'),
        ORM\Column,
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post', 'read:employee:collection'])
    ]
    private ?string $surname = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    private ?Team $team = null;

    #[
        ApiProperty(description: 'Carte de pointage', example: '65465224'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $timeCard = null;

    #[
        ApiProperty(description: 'Compte validé', required: true, example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post', 'read:employee:collection'])
    ]
    private bool $userEnabled = false;

    #[
        ApiProperty(description: 'identifiant', example: 'super'),
        ORM\Column(length: 20),
        Serializer\Groups(['read:employee'])
    ]
    private ?string $username = null;

    #[Pure]
    final public function __construct() {
        $this->apiTokens = new ArrayCollection();
        $this->embRoles = new Roles();
        $this->currentPlace = new CurrentPlace();
    }

    public static function getBarCodeTableNumber(): string {
        return self::EMPLOYEE_BAR_CODE_TABLE_NUMBER;
    }

    final public function addApiToken(Token $apiToken): self {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
        }
        return $this;
    }

    final public function addRole(string $role): self {
        $this->embRoles->addRole($role);
        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function eraseCredentials(): void {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    /**
     * @return Collection<int, Token>
     */
    final public function getApiTokens(): Collection {
        return $this->apiTokens;
    }

    final public function getBirthCity(): ?string {
        return $this->birthCity;
    }

    final public function getBirthday(): ?DateTimeImmutable {
        return $this->birthday;
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getCurrentApiToken(): ?Token {
        foreach ($this->apiTokens as $token) {
            if (!$token->isExpired()) {
                return $token;
            }
        }
        return null;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getEmbRoles(): Roles {
        return $this->embRoles;
    }

    final public function getEntryDate(): ?DateTimeImmutable {
        return $this->entryDate;
    }

    final public function getGender(): ?string {
        return $this->gender;
    }

    final public function getInitials(): ?string {
        return $this->initials;
    }

    final public function getLevelOfStudy(): ?string {
        return $this->levelOfStudy;
    }

    final public function getManager(): ?self {
        return $this->manager;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    final public function getPassword(): ?string {
        return $this->password;
    }

    final public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    /**
     * @return string[]
     *
     * @see UserInterface
     */
    #[
        ApiProperty(description: 'Rôles', example: [Roles::ROLE_USER]),
        Pure,
        Serializer\Groups(['read:employee'])
    ]
    final public function getRoles(): array {
        return $this->embRoles->getRoles();
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    final public function getSalt(): ?string {
        return null;
    }

    final public function getSituation(): ?string {
        return $this->situation;
    }

    final public function getSocialSecurityNumber(): ?string {
        return $this->socialSecurityNumber;
    }

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function getTeam(): ?Team {
        return $this->team;
    }

    final public function getTimeCard(): ?string {
        return $this->timeCard;
    }

    #[
        ApiProperty(description: 'Token', example: '47e65f14b42a5398c1eea9125aaf93e44b1ddeb93ea2cca769ea897e0a285e4e7cfac21dee1a56396e15c1c5ee7c8d4e0bf692c83cda86a6462ad707'),
        Serializer\Groups(['read:employee'])
    ]
    final public function getToken(): ?string {
        return $this->getCurrentApiToken()?->getToken();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    final public function getUserIdentifier(): string {
        return (string) $this->username;
    }

    final public function getUsername(): ?string {
        return $this->username;
    }

    final public function isUserEnabled(): bool {
        return $this->userEnabled;
    }

    final public function removeApiToken(Token $apiToken): self {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
        }
        return $this;
    }

    final public function removeRole(string $role): self {
        $this->embRoles->removeRole($role);
        return $this;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setBirthCity(?string $birthCity): self {
        $this->birthCity = $birthCity;
        return $this;
    }

    final public function setBirthday(?DateTimeImmutable $birthday): self {
        $this->birthday = $birthday;
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setEmbRoles(Roles $embRoles): self {
        $this->embRoles = $embRoles;
        return $this;
    }

    final public function setEntryDate(?DateTimeImmutable $entryDate): self {
        $this->entryDate = $entryDate;
        return $this;
    }

    final public function setGender(?string $gender): self {
        $this->gender = $gender;
        return $this;
    }

    final public function setInitials(?string $initials): self {
        $this->initials = $initials;
        return $this;
    }

    final public function setLevelOfStudy(?string $levelOfStudy): self {
        $this->levelOfStudy = $levelOfStudy;
        return $this;
    }

    final public function setManager(?self $manager): self {
        $this->manager = $manager;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    final public function setPlainPassword(?string $plainPassword): self {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    final public function setSituation(?string $situation): self {
        $this->situation = $situation;
        return $this;
    }

    final public function setSocialSecurityNumber(?string $socialSecurityNumber): self {
        $this->socialSecurityNumber = $socialSecurityNumber;
        return $this;
    }

    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }

    final public function setTeam(?Team $team): self {
        $this->team = $team;
        return $this;
    }

    final public function setTimeCard(?string $timeCard): self {
        $this->timeCard = $timeCard;
        return $this;
    }

    final public function setUserEnabled(bool $userEnabled): self {
        $this->userEnabled = $userEnabled;
        return $this;
    }

    final public function setUsername(?string $username): self {
        $this->username = $username;
        return $this;
    }
}
