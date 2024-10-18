<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Action\PlaceholderAction;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Traits\FileTrait;
use App\Validator as AppAssert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Hr\Employee\EmployeeController;
use App\Doctrine\DBAL\Types\Hr\Employee\GenderType;
use App\Doctrine\DBAL\Types\Hr\Employee\SituationType;
use App\Entity\Api\Token;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Hr\Employee\State;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Attachment\EmployeeAttachment;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Management\Society\Company\Company;
use App\Filter\NumericFilter;
use App\Filter\SetFilter;
use App\Repository\Hr\Employee\EmployeeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Hr\Employee\EmployeePatchController;
use App\Filter\CustomGetterFilter;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['userEnabled']),
    ApiFilter(filterClass: NumericFilter::class, properties: ['id']),
    ApiFilter(filterClass: CustomGetterFilter::class, properties: ['getterFilter'=>['fields'=>['name', 'surname']]]),
    ApiFilter(
        filterClass: SearchFilter::class,
        properties: [
            'initials' => 'partial',
            'name' => 'partial',
            'surname' => 'partial',
            'username' => 'partial',
            'company'=>'exact',
            'notes' => 'partial',
            'entryDate' => 'partial',
            'timeCard' => 'partial'
        ]
    ),
    ApiFilter(
        filterClass: OrderFilter::class,
        properties: ['initials', 'id', 'name', 'surname', 'username', 'timeCard', 'initials']),
    ApiFilter(filterClass: SetFilter::class, properties: ['embState.state','embBlocker.state']),
    ApiResource(
        description: 'Employé',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:id', 'read:employee:collection', 'read:state'],
                    'openapi_definition_name' => 'Employee-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les employés',
                    'summary' => 'Récupère les employés'
                ]
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:employee'],
                    'openapi_definition_name' => 'Employee-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un employé',
                    'summary' => 'Créer un employé'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un employé',
                    'summary' => 'Supprime un employé'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un employé',
                    'summary' => 'Récupère un employé'
                ]
            ],
            'patch image' => [
                'openapi_context' => [
                    'description' => 'Modifie la photo d\'un employee',
                    'summary' => 'Modifie la photo d\'un employee'
                ],
                'denormalization_context' => [
                    'groups' => ['write:employee:image'],
                    'openapi_definition_name' => 'Employee-image'
                ],
                'normalization_context' => [
                    'groups' => ['read:employee:image'],
                    'openapi_definition_name' => 'Employee-image'
                ],
                'path' => '/employees/{id}/image',
                'controller' => PlaceholderAction::class,
                'method' => 'POST',
                'input_formats' => ['multipart'],
                'validation_groups' => ['patchImage'],
            ],
            'patch' => [
                'controller' => PlaceholderAction::class, //EmployeePatchController::class,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Modifier un employé',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'enum' => ['main', 'hr', 'it', 'production', 'logistics', 'quality'],
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Modifier un employé'
                ],
                'path' => '/employees/{id}/{process}',
                'read' => true,
                'write' => true,
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')',
                'validation_groups' => AppAssert\ProcessGroupsGenerator::class
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite l\'employé à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['employee', 'blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite l\'employé à son prochain statut de workflow'
                ],
                'path' => '/employees/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
                'validate' => false
            ],
            'user' => [
                'controller' => EmployeeController::class,
                'identifiers' => [],
                'method' => 'GET',
                'openapi_context' => [
                    'description' => 'Récupère l\'utilisateur courant',
                    'summary' => 'Récupère l\'utilisateur courant'
                ],
                'path' => '/user',
                'read' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:employee'],
            'openapi_definition_name' => 'Employee-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:employee', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Employee-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: EmployeeRepository::class)
]
class Employee extends Entity implements PasswordAuthenticatedUserInterface, UserInterface, FileEntity {
    use FileTrait;

    #[
        ApiProperty(description: 'Ancien_Identifiant', example: 1),
        ORM\Column(type: 'integer', nullable: true),
        Serializer\Groups(['read:employee', 'read:employee:collection', 'read:user'])
    ]
    private ?int $oldId = 0;
    #[
        Assert\Valid(groups: ['Default', 'creation', 'update']),
        ORM\Embedded(Address::class),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr', 'write:employee:main'])
    ]
    private Address $address;

    /** @var Collection<int, Token> */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Token::class)]
    private Collection $apiTokens;

   /**
    * @var Collection<int,EmployeeAttachment>
    */
   #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmployeeAttachment::class)]
   private Collection $attachments;

    #[
        ApiProperty(description: 'Ville de naissance', example: 'Nancy'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr'])
    ]
    private ?string $birthCity = null;

    #[
        ApiProperty(description: 'Date de naissance', example: '1980-24-03'),
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr'])
    ]
    private ?DateTimeImmutable $birthday = null;

    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['create:employee', 'read:employee', 'read:user', 'read:employee:collection', 'write:employee', 'write:employee:it'])
    ]
    private ?Company $company = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:employee', 'read:employee:collection', 'read:user'])
    ]
    private Blocker $embBlocker;

    #[  
        ORM\Embedded,
        Serializer\Groups(['create:employee', 'read:employee', 'write:employee', 'write:employee:it'])
    ]
    private Roles $embRoles;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:employee', 'read:employee:collection', 'read:user'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Date d\'arrivée', example: '2021-01-12'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr', 'create:employee', 'read:employee', 'read:employee:collection', 'read:user', 'write:employee', 'write:employee:hr', 'write:employee:main'])
    ]

    private ?DateTimeImmutable $entryDate = null;

    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(type: 'string'),
        Serializer\Groups(['read:file', 'read:employee:collection', 'read:employee'])
    ]
    protected ?string $filePath = '';

    #[
        ApiProperty(description: 'Sexe', example: GenderType::TYPE_MALE, openapiContext: ['enum' => GenderType::TYPES]),
        Assert\Choice(choices: GenderType::TYPES),
        ORM\Column(type: 'gender_place', nullable: true, options: ['default' => GenderType::TYPE_MALE]),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr', 'write:employee:main'])
    ]
    private ?string $gender = GenderType::TYPE_MALE;

    #[
        ApiProperty(description: 'Initiales', example: 'C.R.'),
        ORM\Column,
        Serializer\Groups(['create:employee', 'read:employee', 'read:employee:collection', 'read:user', 'write:employee', 'write:employee:hr'])
    ]
    private ?string $initials = null;

    #[
        ApiProperty(description: 'Niveau d\'étude', example: 'Bac+5'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr'])
    ]
    private ?string $levelOfStudy = null;

    #[
        ApiProperty(description: 'Manager', readableLink: false, example: '/api/employees/3'),
        ORM\ManyToOne(targetEntity: self::class),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:production'])
    ]
    private ?self $manager = null;

    #[
        ApiProperty(description: 'Matricule', example: '65465224'),
        ORM\Column(type: 'string', length: 20, nullable: true),
        Serializer\Groups(['read:employee', 'read:user', 'read:employee:collection', 'write:employee', 'write:employee:it'])
    ]
    private ?string $matricule = null;

    #[
        ApiProperty(description: 'Prénom', required: true, example: 'Super'),
        ORM\Column(length: 30),
        Serializer\Groups(['read:production-quality', 'create:employee', 'read:employee', 'read:employee:collection', 'read:user', 'write:employee', 'write:employee:hr', 'write:employee:main', 'read:manufacturing-operation', 'read:skill'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum dolor sit am'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:main'])
    ]
    private ?string $notes = null;

    #[
        ORM\Column(type: 'char', length: 60, nullable: true),
        Serializer\Groups(['create:employee', 'write:employee', 'write:employee:it'])
    ]
    private ?string $password = null;

    #[
        ApiProperty(description: 'Mot de passe', example: 'L0r3m@Ipsum'),
        ORM\Column(nullable: true),
        Serializer\Groups(['create:employee', 'write:employee', 'write:employee:it'])
    ]
    private ?string $plainPassword = null;

    #[
        ApiProperty(description: 'Situation', example: SituationType::TYPE_SINGLE, openapiContext: ['enum' => SituationType::TYPES]),
        Assert\Choice(choices: SituationType::TYPES),
        ORM\Column(type: 'situation_place', nullable: true, options: ['default' => SituationType::TYPE_SINGLE]),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr', 'write:employee:main'])
    ]
    private ?string $situation = SituationType::TYPE_SINGLE;

    #[
        ApiProperty(description: 'Numéro de sécurité sociale', example: '1 80 12 75 200 200 36'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:employee', 'write:employee', 'write:employee:hr'])
    ]
    private ?string $socialSecurityNumber = null;

    #[
        ApiProperty(description: 'Nom', example: 'Roosevelt'),
        ORM\Column,
        Serializer\Groups(['read:production-quality', 'create:employee', 'read:employee', 'read:employee:collection', 'read:user', 'write:employee', 'write:employee:hr', 'write:employee:main', 'read:manufacturing-operation', 'read:skill'])
    ]
    private ?string $surname = null;

    #[
       ApiProperty(description: 'Equipe', readableLink: false, example: '/api/teams/1'),
       ORM\ManyToOne(inversedBy: 'employees'),
       Serializer\Groups(['read:employee', 'read:user', 'read:employee:collection', 'write:employee', 'write:employee:production'])
    ]
    private ?Team $team = null;

    #[
        ApiProperty(description: 'Badge', example: '65465224'),
        ORM\Column(type: 'integer' , nullable: true),
        Serializer\Groups(['read:employee', 'read:employee:collection', 'write:employee', 'write:employee:it'])
    ]
    private ?int $timeCard = null;

    #[
        ApiProperty(description: 'Compte validé', required: true, example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['create:employee', 'read:employee', 'read:employee:collection', 'write:employee', 'write:employee:it'])
    ]
    private bool $userEnabled = false;

    #[
        ApiProperty(description: 'identifiant', example: 'super'),
        ORM\Column(length: 20, nullable: true),
        Serializer\Groups(['read:operation-employee', 'create:employee', 'read:employee','read:employee:collection', 'read:user', 'read:manufacturing-operation', 'write:employee', 'write:employee:it'])
    ]
    private ?string $username = null;

    public function __construct() {
        $this->apiTokens = new ArrayCollection();
        $this->embBlocker = new Blocker();
        $this->embRoles = new Roles();
        $this->attachments = new ArrayCollection();
        $this->embState = new State();
        $this->address = new Address();
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

    /**
     * @return $this
     */
    public function addAttachment(EmployeeAttachment $attachment): self {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
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

   /**
    * @return Collection<int,EmployeeAttachment>
    */
   public function getAttachments(): Collection {
      return $this->attachments;
   }

    final public function getBirthCity(): ?string {
        return $this->birthCity;
    }

    final public function getBirthday(): ?DateTimeImmutable {
        return $this->birthday;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
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

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbRoles(): Roles {
        return $this->embRoles;
    }

    final public function getEmbState(): State {
        return $this->embState;
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
    #[
        ApiProperty(description: 'Nom complet', example: 'Roosevelt Super'),
        Serializer\Groups(['read:employee', 'read:user', 'read:employee:collection'])
    ]
    final public function getGetterFilter(): string {
        return $this->name.' '.$this->surname;
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
        Serializer\Groups(['read:employee', 'read:user'])
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

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function getTeam(): ?Team {
        return $this->team;
    }

    final public function getTimeCard(): ?int {
        return $this->timeCard;
    }

    #[
        ApiProperty(description: 'Token', example: '47e65f14b42a5398c1eea9125aaf93e44b1ddeb93ea2cca769ea897e0a285e4e7cfac21dee1a56396e15c1c5ee7c8d4e0bf692c83cda86a6462ad707'),
        Serializer\Groups(['read:employee', 'read:user'])
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

    final public function hasRole(string $role): bool {
        return $this->embRoles->hasRole($role);
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

   /**
    * @param Collection<int,EmployeeAttachment> $attachments
    * @return void
    */
   public function setAttachments(Collection $attachments): void {
      $this->attachments = $attachments;
   }

    final public function setBirthCity(?string $birthCity): self {
        $this->birthCity = $birthCity;
        return $this;
    }

    final public function setBirthday(?DateTimeImmutable $birthday): self {
        $this->birthday = $birthday;
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbRoles(Roles $embRoles): self {
        $this->embRoles = $embRoles;
        return $this;
    }

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
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

    final public function setRoles(array $embRoles): self {
        $this->embRoles->setRoles($embRoles);
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

    final public function setState(string $state): self {
        $this->embState->setState($state);
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

    final public function setTimeCard(?int $timeCard): self {
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

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     * @return Employee
     */
    public function setFilePath(?string $filePath): Employee
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->oldId;
    }

    public function setOldId(?int $oldId): void
    {
        $this->oldId = $oldId;
    }

    /**
     * @return string|null
     */
    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    /**
     * @param string|null $matricule
     * @return Employee
     */
    public function setMatricule(?string $matricule): Employee
    {
        $this->matricule = $matricule;
        return $this;
    }

}
