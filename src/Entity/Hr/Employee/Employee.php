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
use App\Entity\Hr\Employee\Skill\Skill;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Management\Society\Company;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\BarCodeTrait;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NameTrait;
use App\Filter\RelationFilter;
use App\Repository\Hr\Employee\EmployeeRepository;
use DatetimeInterface;
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
        'name' => 'partial',
        'surname' => 'partial',
        'initials' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'currentPlace' => 'name'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name', 'surname', 'initials'
    ]),
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
    ORM\Entity(repositoryClass: EmployeeRepository::class)
]
class Employee extends Entity implements BarCodeInterface, PasswordAuthenticatedUserInterface, UserInterface {
    use AddressTrait {
        AddressTrait::__construct as private addressContruct;
    }
    use BarCodeTrait;
    use CompanyTrait;
    use NameTrait;

    public const GENDER_TYPE_FRMALE = 'female';
    public const GENDER_TYPE_MALE = 'male';
    public const GENDER_TYPES = [
        self::GENDER_TYPE_MALE,
        self::GENDER_TYPE_FRMALE,
    ];
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
    protected Address $address;

    #[
        ApiProperty(description: 'Companie', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Statut', required: true),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:employee', 'read:employee:collection'])
    ]
    protected CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Super'),
        Assert\NotBlank,
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:name', 'write:name', 'write:employee:post'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Nom de famille', required: true, example: 'Roosevelt'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post', 'read:employee:collection'])
    ]
    protected ?string $surname = null;

    /**
     * @var Collection<int, Token>
     */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Token::class)]
    private Collection $apiTokens;

    #[
        ApiProperty(description: 'Ville de naissance', required: false, example: 'Nancy'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $birthCity = null;

    #[
        ApiProperty(description: 'Date de naissance', example: '1980-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?DatetimeInterface $birthday = null;

    #[ORM\Embedded]
    private Roles $embRoles;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?DatetimeInterface $entryDate = null;

    #[
        ApiProperty(description: 'Prénom', required: false, example: 'Charles'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $firstname = null;

    #[
        ApiProperty(description: 'Sexe', required: false, example: self::GENDER_TYPE_MALE),
        Assert\Choice(choices: self::GENDER_TYPES),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $gender = null;

    #[
        ApiProperty(description: 'Initiales', required: false, example: 'C.R.'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post', 'read:employee:collection'])
    ]
    private ?string $initials = null;

    #[
        ApiProperty(description: 'Niveau d\'étude', required: false, example: 'Bac+5'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $levelOfStudy = null;

    #[
        ApiProperty(description: 'Manager', readableLink: false, required: false, example: '/api/employees/3'),
        ORM\ManyToOne(targetEntity: self::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?self $manager = null;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem ipsum dolor sit am'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $notes = null;

    #[
        ORM\Column
]
    private ?string $password = null;

    #[
        ApiProperty(description: 'Nouveau statut', required: false, example: 'disabled'),
        Serializer\Groups(['write:employee:promote'])
    ]
    private ?string $place = null;

    #[
        ApiProperty(description: 'Mot de passe', required: false, example: 'L0r3m@Ipsum'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post'])
    ]
    private ?string $plainPassword = null;

    private ?Company $sessionCompany = null;

    #[
        ApiProperty(description: 'Situation', required: false, example: self::SITUATION_TYPE_MARRIED),
        Assert\Choice(choices: self::SITUATION_TYPES),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $situation = null;

    /**
     * @var Collection<int, Skill>
     */
    #[
        ApiProperty(description: 'Composants', required: false, readableLink: false, example: ['/api/skills/5', '/api/skills/14']),
        ORM\OneToMany(mappedBy: 'employee', targetEntity: Skill::class),
        Serializer\Groups(['read:component']),
    ]
    private Collection $skills;

    #[
        ApiProperty(description: 'Numéro de sécurité sociale', required: false, example: '1 80 12 75 200 200 36'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $socialSecurityNumber = null;

    #[
        ApiProperty(description: 'Carte de pointage', required: false, example: '65465224'),
        ORM\Column(type: 'string', length: 180, nullable: true),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $timeCard = null;

    #[
        ApiProperty(description: 'Compte validé', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:post', 'read:employee:collection'])
    ]
    private bool $userEnabled = false;

    #[
        ApiProperty(description: 'Identifiant', example: 'super'),
        ORM\Column(length: 180),
        Serializer\Groups(['read:employee', 'write:employee:post'])
    ]
    private ?string $username = null;

    #[Pure]
    final public function __construct() {
        $this->apiTokens = new ArrayCollection();
        $this->embRoles = new Roles();
        $this->currentPlace = new CurrentPlace();
        $this->address = new Address();
        $this->skills = new ArrayCollection();
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

    public function addSkill(Skill $skill): self {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
            $skill->setEmployee($this);
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function eraseCredentials(): void {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAddress(): Address {
        return $this->address;
    }

    /**
     * @return Collection<int, Token>
     */
    final public function getApiTokens(): Collection {
        return $this->apiTokens;
    }

    public function getBirthCity(): ?string {
        return $this->birthCity;
    }

    public function getBirthday(): ?DateTimeInterface {
        return $this->birthday;
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

    public function getEntryDate(): ?DateTimeInterface {
        return $this->entryDate;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function getGender(): ?string {
        return $this->gender;
    }

    public function getInitials(): ?string {
        return $this->initials;
    }

    public function getLevelOfStudy(): ?string {
        return $this->levelOfStudy;
    }

    public function getManager(): ?self {
        return $this->manager;
    }

    public function getNotes(): ?string {
        return $this->notes;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    final public function getPassword(): ?string {
        return $this->password;
    }

    final public function getPlace(): ?string {
        return $this->place;
    }

    public function getPlainPassword(): ?string {
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

    final public function getSessionCompany(): ?Company {
        return $this->sessionCompany ?? $this->company;
    }

    public function getSituation(): ?string {
        return $this->situation;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection {
        return $this->skills;
    }

    public function getSocialSecurityNumber(): ?string {
        return $this->socialSecurityNumber;
    }

    public function getSurname(): ?string {
        return $this->surname;
    }

    public function getTimeCard(): ?string {
        return $this->timeCard;
    }

    #[
        ApiProperty(description: 'Token', example: '47e65f14b42a5398c1eea9125aaf93e44b1ddeb93ea2cca769ea897e0a285e4e7cfac21dee1a56396e15c1c5ee7c8d4e0bf692c83cda86a6462ad707'),
        Serializer\Groups(['read:employee'])
    ]
    final public function getToken(): ?string {
        return $this->getCurrentApiToken()?->getToken();
    }

    public function getUserEnabled(): ?bool {
        return $this->userEnabled;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    final public function getUserIdentifier(): ?string {
        return $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    final public function getUsername(): ?string {
        return $this->username;
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

    public function removeSkill(Skill $skill): self {
        if ($this->skills->removeElement($skill)) {
            // set the owning side to null (unless already changed)
            if ($skill->getEmployee() === $this) {
                $skill->setEmployee(null);
            }
        }

        return $this;
    }

    public function setAddress(Address $address): self {
        $this->address = $address;

        return $this;
    }

    public function setBirthCity(?string $birthCity): self {
        $this->birthCity = $birthCity;

        return $this;
    }

    public function setBirthday(?DateTimeInterface $birthday): self {
        $this->birthday = $birthday;

        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function setEmbRoles(Roles $embRoles): self {
        $this->embRoles = $embRoles;

        return $this;
    }

    public function setEntryDate(?DateTimeInterface $entryDate): self {
        $this->entryDate = $entryDate;

        return $this;
    }

    public function setFirstname(?string $firstname): self {
        $this->firstname = $firstname;

        return $this;
    }

    public function setGender(?string $gender): self {
        $this->gender = $gender;

        return $this;
    }

    public function setInitials(?string $initials): self {
        $this->initials = $initials;

        return $this;
    }

    public function setLevelOfStudy(?string $levelOfStudy): self {
        $this->levelOfStudy = $levelOfStudy;

        return $this;
    }

    public function setManager(?self $manager): self {
        $this->manager = $manager;

        return $this;
    }

    public function setNotes(?string $notes): self {
        $this->notes = $notes;

        return $this;
    }

    final public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    final public function setPlace(?string $place): self {
        $this->place = $place;

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): self {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    final public function setSessionCompany(?Company $sessionCompany): self {
        $this->sessionCompany = $sessionCompany;
        return $this;
    }

    public function setSituation(?string $situation): self {
        $this->situation = $situation;

        return $this;
    }

    public function setSocialSecurityNumber(?string $socialSecurityNumber): self {
        $this->socialSecurityNumber = $socialSecurityNumber;

        return $this;
    }

    public function setSurname(?string $surname): self {
        $this->surname = $surname;

        return $this;
    }

    public function setTimeCard(?string $timeCard): self {
        $this->timeCard = $timeCard;

        return $this;
    }

    public function setUserEnabled(bool $userEnabled): self {
        $this->userEnabled = $userEnabled;

        return $this;
    }

    final public function setUsername(?string $username): self {
        $this->username = $username;
        return $this;
    }
}
