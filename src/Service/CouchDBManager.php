<?php

namespace App\Service;

use App\Attribute\Couchdb\Abstract\Fetch;
use App\Attribute\Couchdb\Document as CouchdbDocumentAttribute;
use App\Attribute\Couchdb\ODM\ManyToMany;
use App\Attribute\Couchdb\ODM\ManyToOne;
use App\Attribute\Couchdb\ODM\OneToMany;
use App\Attribute\Couchdb\ODM\OneToOne;
use App\Attribute\Couchdb\ORM\ManyToMany as ORMManyToMany;
use App\Attribute\Couchdb\ORM\ManyToOne as ORMManyToOne;
use App\Attribute\Couchdb\ORM\OneToMany as ORMOneToMany;
use App\Attribute\Couchdb\ORM\OneToOne as ORMOneToOne;
use App\DataCollector\CouchdbLogItem;
use App\Entity\Couchdb\Document as CouchdbDocumentEntity;
use App\Entity\Couchdb\Item;
use App\Event\Couchdb\Events;
use App\Event\Couchdb\Item\CouchdbItemPostRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPostUpdateEvent;
use App\Event\Couchdb\Item\CouchdbItemPrePersistEvent;
use App\Event\Couchdb\Item\CouchdbItemPreRemoveEvent;
use App\Event\Couchdb\Item\CouchdbItemPreUpdateEvent;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\HTTP\Response;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CouchDBManager {
    /**
     * @var array<string,CouchdbLogItem>
     */
    public array $actions;

    private CouchDBClient $client;

    /**
     * @var ArrayCollection<int,CouchdbDocumentEntity>
     */
    private ArrayCollection $documents;

    /**
     * @param string                   $couchUrl
     * @param string                   $couchDBName
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     * @param EntityManagerInterface   $entityManager
     * @param CacheInterface           $cache
     */
    public function __construct(
        private string $couchUrl,
        private string $couchDBName,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {
        $this->init();
        $this->documents = new ArrayCollection();
    }

    public static function getRepositoryFromEntityClass(string $className): string {
        return str_replace('Entity', 'Repository', $className).'Repository';
    }

    public function allDocs(): Response {
        $couchLog = new CouchdbLogItem('all documents', 'allDocs', __METHOD__);
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        try {
            $allDoc = $this->client->allDocs();
            $couchLog->setDetail('[Appel client->allDocs() Ok]');
        } catch (Exception $e) {
            $couchLog->setDetail("[Appel client->allDocs() KO]\t".$e->getMessage());
            $couchLog->setErrors(true);
            $allDoc = new Response(500, $e->getTrace(), $e->getMessage());
        }
        $this->actions[$time] = $couchLog;
        return $allDoc;
    }

    /**
     * @param array<mixed> $itemArray
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function convertArrayToEntity(array $itemArray, string $className): mixed {
        if (class_exists($className)) {
            $entity = new $className();
            $refEntity = new ReflectionObject($entity);
            $reflectionClass = new ReflectionClass($className);
            $arrayProperties = array_keys($itemArray);
            foreach ($arrayProperties as $arrayProperty) {
                foreach ($reflectionClass->getProperties() as $property) {
                    if ($property->getName() == $arrayProperty) {
                        $refProperty = $refEntity->getProperty($property->getName());
                        $refProperty->setAccessible(true);
                        $refPropertyType = $refProperty->getType();
                        if ($refPropertyType instanceof ReflectionNamedType) {
                            switch ($refPropertyType->getName()) {
                                case 'DateTime':
                                    $newValue = $itemArray[$property->getName()] === null ? null : new DateTime($itemArray[$property->getName()]);
                                    $refProperty->setValue($entity, $newValue);
                                    break;
                                case 'string':
                                case 'int':
                                case 'bool':
                                case 'float':
                                    $refProperty->setValue($entity, $itemArray[$property->getName()]);
                                    break;
                                default:  //C'est une entité

                                    $manyToManyORM = $refProperty->getAttributes(ORMManyToMany::class);
                                    if (count($manyToManyORM) > 0) {
                                        $propertyData = ManyToMany::getPropertyData($manyToManyORM[0]);
                                        //'targetEntity'=> $instance->targetEntity, 'owned'=> $instance->owned, 'fetch'=>$instance->fetch, 'type'=>self::class
                                        if ($propertyData['fetch'] == Fetch::EAGER && $propertyData['owned']) {
                                            $targetEntitiesIds = $itemArray[$property->getName()];
                                            $targetEntityClass = $propertyData['targetEntity'];
                                            /** @phpstan-ignore-next-line */
                                            $targetEntityRepo = $this->entityManager->getRepository($targetEntityClass);
                                            $targetEntities = $targetEntityRepo->findBy(['id' => $targetEntitiesIds]);
                                            $refProperty->setValue($entity, $targetEntities);
                                        } else {
                                            $refProperty->setValue($entity, []);
                                        }
                                    }
                                    $manyToOneORM = $refProperty->getAttributes(ORMManyToOne::class);
                                    if (count($manyToOneORM)) {
                                        $propertyData = ManyToOne::getPropertyData($manyToOneORM[0]);
                                        //[ 'targetEntity'=> $instance->targetEntity, 'inversedBy'=> $instance->inversedBy, 'fetch'=>$instance->fetch, 'type'=>self::class ];
                                        if ($propertyData['fetch'] == Fetch::EAGER || !$refPropertyType->allowsNull()) {
                                            /** @phpstan-ignore-next-line */
                                            $targetEntity = $this->entityManager->getRepository($refPropertyType->getName())->find($itemArray[$property->getName()]);
                                            $refProperty->setValue($entity, $targetEntity);
                                        } else {
                                            $refProperty->setValue($entity, null);
                                        }
                                    }
                                    $oneToManyORM = $refProperty->getAttributes(ORMOneToMany::class);
                                    if (count($oneToManyORM) > 0) {
                                        $propertyData = OneToMany::getPropertyData($oneToManyORM[0]);
                                        //[ 'targetEntity'=> $instance->targetEntity, 'owned'=> $instance->owned, 'fetch'=>$instance->fetch, 'type'=>self::class ];
                                        if ($propertyData['fetch'] === Fetch::EAGER && $propertyData['owned']) {
                                            $targetEntitiesIds = $itemArray[$property->getName()];
                                            $targetEntityClass = $propertyData['targetEntity'];
                                            /** @phpstan-ignore-next-line */
                                            $targetEntityRepo = $this->entityManager->getRepository($targetEntityClass);
                                            $targetEntities = $targetEntityRepo->findBy(['id' => $targetEntitiesIds]);
                                            $refProperty->setValue($entity, $targetEntities);
                                        } else {
                                            $refProperty->setValue($entity, []);
                                        }
                                    }
                                    $oneToOneORM = $refProperty->getAttributes(ORMOneToOne::class);
                                    if (count($oneToOneORM) > 0) {
                                        $propertyData = OneToOne::getPropertyData($oneToOneORM[0]);
                                        //[ 'targetEntity'=> $instance->targetEntity, 'mappedBy'=> $instance->mappedBy, 'fetch'=>$instance->fetch, 'type'=>self::class ];
                                        if ($propertyData['fetch'] === Fetch::EAGER || !$refPropertyType->allowsNull()) {
                                            /** @phpstan-ignore-next-line */
                                            $targetEntity = $this->entityManager->getRepository($refPropertyType->getName())->find($itemArray[$property->getName()]);
                                            $refProperty->setValue($entity, $targetEntity);
                                        } else {
                                            $refProperty->setValue($entity, null);
                                        }
                                    }

                            }
                        } else {
                            throw new Exception("La propriété $property n'a pas de Type, la ReflectionProperty a renvoyé null");
                        }
                    }
                }
            }
            return $entity;
        }
        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function convertCouchdbItemToEntity(Item $couchdbItem, string $className): mixed {
        $itemArray = $couchdbItem->getContent();
        return $this->convertArrayToEntity($itemArray, $className);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     *
     * @return array<mixed>
     */
    public function convertEntityToArray(mixed $entity): array {
        $content = [];
        $reflectionClass = new ReflectionClass($entity);
        $refProperties = $reflectionClass->getProperties();
        foreach ($refProperties as $refProperty) {
            $refProperty->setAccessible(true);
            $propertyClass = $refProperty->getType();
            if ($propertyClass instanceof ReflectionNamedType) {
                if ($refProperty->isDefault() && !$refProperty->hasDefaultValue() && $refProperty->getValue($entity) === null) {
                    $content[$refProperty->getName()] = null;
                } else {
                    $content[$refProperty->getName()] = match ($propertyClass->getName()) {
                        'DateTime' => $refProperty->getValue($entity)?->format('Y-m-d\TH:i:s.u'),
                        'string', 'int', 'float' => $refProperty->getValue($entity),
                        'bool' => (bool) ($refProperty->getValue($entity)),
                        default => $refProperty->getValue($entity)?->getId(),
                    };
                }
            } else {
                throw new Exception("La propriété $refProperty->name n'a pas de type defini");
            }
        }
        return $content;
    }

    /**
     * @throws HTTPException
     */
    public function createDatabase(): void {
        $this->client->createDatabase($this->client->getDatabase());
    }

    /**
     * Créer un nouveau document et recharge le document une fois créé.
     *
     * @param array<string,mixed> $content
     */
    public function documentCreate(array $content): ?CouchdbDocumentEntity {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem($content['_id'], CouchdbLogItem::METHOD_DOCUMENT_CREATE, __METHOD__);
        try {
            $id_rev = $this->client->postDocument($content);
            /** @phpstan-ignore-next-line */
            $couchLog->setDetail('Document '.$id_rev[0].' created ('.$id_rev[1].')');
            $this->actions[$time] = $couchLog;
            /** @phpstan-ignore-next-line */
            return $this->documentRead($id_rev[0], true);
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        } catch (InvalidArgumentException $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        }
    }

    /**
     * Supprime un document.
     */
    public function documentDelete(string $id, string $rev): void {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem($id, CouchdbLogItem::METHOD_DOCUMENT_DELETE, __METHOD__);
        try {
            $this->client->deleteDocument($id, $rev);
            $couchLog->setDetail('Document '.$id.' ('.$rev.') deleted');
            $this->actions[$time] = $couchLog;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
        }
    }

    public function documentGetRev(string $dbDoc): string {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem($dbDoc, 'documentGetRev', __METHOD__);
        try {
            foreach ($this->allDocs()->body['rows'] as $doc) {
                if ($doc['id'] == $dbDoc) {
                    $couchLog->setDetail('Document '.$dbDoc.' found');
                    $this->actions[$time] = $couchLog;
                    return $doc['value']['rev'];
                }
            }
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
        }
        return '';
    }

    /**
     * Charge les entités liées d'un couchDocument.
     *
     * @throws Exception
     */
    public function documentHydrate(object &$document, bool $force = false): void {
        //On vérifie que $document est bien un couchDocument
        if (!$this->isCouchdbDocument($document)) {
            throw new Exception('la classe '.get_class($document).' n\'implémente pas l\'attribut '.CouchdbDocumentAttribute::class, 500);
        }
        $class = get_class($document);
        /** @var CouchdbDocumentEntity $couchdbDoc */
        $couchdbDoc = $this->getDocuments()[$class];

        $ormProperties = $this->getLinkedORMProperties($document);
        foreach ($ormProperties as $ormProperty) {
            if ($force || $ormProperty['autoLoad']) {
                //hydratation de l'entité
                /** @phpstan-ignore-next-line */
                $repository = $this->entityManager->getRepository($ormProperty['class']);
                //$entity = $repository->find($id);
            }
        }
    }

    /**
     * Charge un document existant.
     */
    public function documentRead(string $id, bool $forceCacheUpdate = false): ?CouchdbDocumentEntity {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $text = __METHOD__.' forceCacheUpdate ='.($forceCacheUpdate ? 'true' : 'false');
        $couchLog = new CouchdbLogItem($id, CouchdbLogItem::METHOD_DOCUMENT_READ, $text);
        try {
            if ($forceCacheUpdate) { //Si on force l'update du cache alors on supprime le cache avant de le recharger
                $this->cache->delete(CouchdbDocumentEntity::getName($id));
            }
            //Recherche l'élément dans le cache et le récupère si présent, sinon le crée
            $coudbDoc = $this->cache->get(CouchdbDocumentEntity::getName($id), fn () => new CouchdbDocumentEntity($this->client->findDocument($id)));
            $couchLog->setDetail('Document '.$id.' loaded');
            $this->actions[$time] = $couchLog;
            return $coudbDoc;
        } catch (Exception|InvalidArgumentException $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        }
    }

    //region CRUD document

    /**
     * Met à jour un document, cela incrémente sa révision.
     *
     * @return CouchdbDocumentEntity|null [$id,$rev]
     */
    public function documentUpdate(CouchdbDocumentEntity $couchdbDocument): ?CouchdbDocumentEntity {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem($couchdbDocument->getId(), CouchdbLogItem::METHOD_DOCUMENT_UPDATE, __METHOD__);
        $this->logger->info(__METHOD__);
        try {
            //Suppression du cache
            $this->cache->delete(CouchdbDocumentEntity::getName($couchdbDocument->getId()));
            /** @var array{0: int|string} $response */
            $response = $this->client->putDocument(['content' => $couchdbDocument->getContent()], $couchdbDocument->getId(), $couchdbDocument->getRev());
            /** @var int|string $id */
            $id = $response[0];
            $couchLog->setDetail('Document '.$id.' updated');
            $this->actions[$time] = $couchLog;
            //Mise à jour du cache
            return $this->documentRead($couchdbDocument->getId(), true);
        } catch (Exception|InvalidArgumentException $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        }
    }

    /**
     * Retourne la liste des Entités qui ont l'attribut Couchdb\Document.
     *
     * @return array<int,CouchdbDocumentEntity>
     */
    public function getCouchdbDocuments(): array {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem('Entity CouchdbDocument documents', 'getCouchdbDocuments (List)', __METHOD__);
        try {
            /** @var class-string[] $allClasses */
            $allClasses = ClassFinder::getClassesInNamespace('App\Entity', ClassFinder::RECURSIVE_MODE);
            $filteredClass = collect($allClasses)->filter(static function ($class) {
                $reflexionClass = new ReflectionClass($class);
                return count($reflexionClass->getAttributes(CouchdbDocumentAttribute::class)) > 0;
            })->toArray();
            $couchLog->setDetail(count($filteredClass).' CouchdbDocument found');
        } catch (Exception $e) {
            $filteredClass = [];
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
        }
        $this->actions[$time] = $couchLog;
        echo __METHOD__."filteredClass\n";
        echo print_r($filteredClass, true);
        return $filteredClass;
    }

    /**
     * Retourne la liste des noms(id) des documents.
     *
     * @throws Exception
     *
     * @return array<int,string>
     */
    public function getDocList(): array {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem('all documents', 'getDocList', __METHOD__);
        try {
            /** @var array{id: string}[] $rows */
            $rows = $this->allDocs()->body['rows'];
            $couchLog->setDetail(count($rows).' document(s) found');
            $this->actions[$time] = $couchLog;
            return collect($rows)->map(static fn ($doc) => $doc['id'])->toArray();
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            throw new Exception('La base '.$this->couchDBName.' n\'existe pas', 0);
        }
    }
    //endregion

    /**
     * @return ArrayCollection<int,CouchdbDocumentEntity>
     */
    public function getDocuments(): Collection {
        return $this->documents;
    }

    /**
     * Retourne l'ensemble des propriétés liées à un ORM pour une entité ($document) ayant l'attribut App\Attribute\Couchdb\Document.
     *
     * @throws ReflectionException
     *
     * @return array<string,mixed[]>
     */
    public function getLinkedORMProperties(object $document): array {
        $class = get_class($document);
        $reflectionClass = new ReflectionClass($class);
        $linkedORMProperties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyManyToMany = $property->getAttributes(ORMManyToMany::class);
            $propertyManyToOne = $property->getAttributes(ORMManyToOne::class);
            $propertyOneToMany = $property->getAttributes(ORMOneToMany::class);
            $propertyOneToOne = $property->getAttributes(ORMOneToOne::class);
            if (count($propertyManyToMany) > 0) {
                $linkedORMProperties[$property->getName()] = ORMManyToMany::getPropertyData($propertyManyToMany[0]);
            }
            if (count($propertyManyToOne) > 0) {
                $linkedORMProperties[$property->getName()] = ORMManyToOne::getPropertyData($propertyManyToOne[0]);
            }
            if (count($propertyOneToOne) > 0) {
                $linkedORMProperties[$property->getName()] = ORMOneToOne::getPropertyData($propertyOneToOne[0]);
            }
            if (count($propertyOneToMany) > 0) {
                $linkedORMProperties[$property->getName()] = ORMOneToMany::getPropertyData($propertyOneToMany[0]);
            }
        }
        return $linkedORMProperties;
    }

    /**
     * Retourne Vraie si l'entité est d'une classe comportant l'attribut Couchdb/Document.
     */
    public function isCouchdbDocument(object $entity): bool {
        $reflexionClass = new ReflectionClass(get_class($entity));
        return count($reflexionClass->getAttributes(CouchdbDocumentAttribute::class)) > 0;
    }

    //endregion
    //region CRUD item

    /**
     * Crée un item dans la base via l'évènement CouchdbPrePersistEvent.
     */
    public function itemCreate(object $entity): ?object {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_CREATE, __METHOD__);
        $this->logger->info(__CLASS__.'/'.__METHOD__);
        try {
            $newEventPrePersist = new CouchdbItemPrePersistEvent($entity);
            $this->eventDispatcher->dispatch($newEventPrePersist, Events::prePersist);
            $couchLog->setDetail('Document '.$class.' CouchdbPrePersistEvent lancé');
            $this->actions[$time] = $couchLog;
            return $entity;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        }
    }

    /**
     * Supprime un item dans la base.
     */
    public function itemDelete(object $entity): void {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $this->logger->info(__CLASS__.'/'.__METHOD__);
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_DELETE, __METHOD__);
        try {
            $newEventPreRemove = new CouchdbItemPreRemoveEvent($entity);
            $this->eventDispatcher->dispatch($newEventPreRemove, Events::preRemove);
            if (method_exists($entity, 'getId')) {
                $couchLog->setDetail('Document '.$class.' item '.$entity->getId().' CouchdbPreRemoveEvent lancé');
            } else {
                throw new Exception('la methode getId() pour la classe '.$class.' n\'existe pas');
            }
            $this->actions[$time] = $couchLog;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
        }
    }

    /**
     * @throws Exception
     */
    public function itemRead(object $entity): ?Item {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        if (method_exists($entity, 'getId')) {
            $id = $entity->getId();
        } else {
            throw new Exception('la methode getId() pour la classe '.get_class($entity).' n\'existe pas');
        }
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_READ, __METHOD__);
        try {
            // 1. Récupération Document
            $couchdbDoc = $this->documentRead($class);
            if ($couchdbDoc === null) {
                throw new Exception('No CouchDB document found for id = '.$class);
            }
            // 2. Récupération du contenu
            $content = $couchdbDoc->getContent();
            // 3. Récupération de l'item
            $item = $couchdbDoc->getItem($id);
            $couchLog->setDetail(__METHOD__.' Document => '.$class.' item => '.$id);
            $this->actions[$time] = $couchLog;
            return $item;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        }
    }

    /**
     * Supprime un item dans la base.
     *
     * @param array<mixed> $entities
     *
     * @throws Exception
     */
    public function itemsDelete(array $entities): void {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $this->logger->info(__CLASS__.'/'.__METHOD__);
        $first = collect($entities)->first();
        /** @var class-string $class */
        $class = get_class($first);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_DELETE, __METHOD__);
        if ($couchdbDoc = $this->documentRead($class)) {
            $documentContent = $couchdbDoc->getContent();
            try {
                /** @var mixed $entity */
                foreach ($entities as $entity) {
                    foreach ($documentContent as $key => $content) {
                        if ($content['id'] === $entity->getId()) {
                            unset($documentContent[$key]);
                        }
                    }
                }
                $couchdbDoc->setContent($documentContent);
                $this->documentUpdate($couchdbDoc);
                /** @var mixed $entity */
                foreach ($entities as $entity) {
                    $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
                    $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_DELETE, __METHOD__);
                    $newEventPostRemove = new CouchdbItemPostRemoveEvent($entity);
                    $this->eventDispatcher->dispatch($newEventPostRemove, Events::postRemove);
                    $couchLog->setDetail('Document '.$class.' item '.$entity->getId().' CouchdbItemPostRemoveEvent lancé');
                    $this->actions[$time] = $couchLog;
                }
            } catch (Exception $e) {
                $couchLog->setDetail($e->getMessage());
                $couchLog->setErrors(true);
                $this->actions[$time] = $couchLog;
            }
        } else {
            throw new Exception("Le document couchdb `$class` n'a pas été trouvé en base, veuillez relancer la commande d'initialisation de la base");
        }
    }

    /**
     * @param array<mixed> $entities
     *
     * @throws Exception
     */
    public function itemsUpdate(array $entities): void {
        $this->logger->info(__CLASS__.'/'.__METHOD__);
        $first = collect($entities)->first();
        /** @var class-string $class */
        $class = get_class($first);

        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_UPDATE, __METHOD__);
        $couchLog->setDetail('Document '.$class.' '.count($entities).' items en cours d\'update groupé');
        $this->actions[$time] = $couchLog;
        if ($couchdbDoc = $this->documentRead($class)) {
            $documentContent = $couchdbDoc->getContent();
            try {
                /** @var mixed $entity */
                foreach ($entities as $entity) {
                    $entityArray = $this->convertEntityToArray($entity);
                    foreach ($documentContent as $key => $content) {
                        if ($content['id'] === $entity->getId()) {
                            $documentContent[$key] = $entityArray;
                        }
                    }
                }
                $couchdbDoc->setContent($documentContent);
                $this->documentUpdate($couchdbDoc);

                /** @var mixed $entity */
                foreach ($entities as $entity) {
                    $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
                    $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_UPDATE, __METHOD__);
                    $newEventPostUpdate = new CouchdbItemPostUpdateEvent($entity);
                    $this->eventDispatcher->dispatch($newEventPostUpdate, Events::postUpdate);
                    $couchLog->setDetail('Document '.$class.' item '.$entity->getId().' CouchdbPostUpdateEvent lancé');
                    $this->actions[$time] = $couchLog;
                }
            } catch (Exception $e) {
                $couchLog->setDetail($e->getMessage());
                $couchLog->setErrors(true);
                $this->actions[$time] = $couchLog;
            }
        } else {
            throw new Exception("Le document couchdb `$class` n'a pas été trouvé en base, veuillez relancer la commande d'initialisation de la base");
        }
    }

    public function itemUpdate(object $entity): ?object {
        $time = date_format(new DateTime('now'), 'Y/m/d - H:i:s:u');
        $this->logger->info(__CLASS__.'/'.__METHOD__);
        $class = get_class($entity);
        $couchLog = new CouchdbLogItem($class, CouchdbLogItem::METHOD_UPDATE, __METHOD__);
        try {
            $newEventPreUpdate = new CouchdbItemPreUpdateEvent($entity);
            $this->eventDispatcher->dispatch($newEventPreUpdate, Events::preUpdate);
            $item = $newEventPreUpdate->getEntity();
            if (method_exists($entity, 'getId')) {
                $couchLog->setDetail('Document '.$class.' item '.$entity->getId().' CouchdbPreUpdateEvent lancé');
            } else {
                throw new Exception('la methode getId() pour la classe '.$class.' n\'existe pas');
            }
            $this->actions[$time] = $couchLog;
            return $item;
        } catch (Exception $e) {
            $couchLog->setDetail($e->getMessage());
            $couchLog->setErrors(true);
            $this->actions[$time] = $couchLog;
            return null;
        }
    }

    /**
     * @param ArrayCollection<int,CouchdbDocumentEntity> $documents
     */
    public function setDocuments(ArrayCollection $documents): void {
        $this->documents = $documents;
    }

    private function init(): void {
        $this->client = CouchDBClient::create([
            'url' => $this->couchUrl,
            'dbname' => $this->couchDBName
        ]);
        $this->actions = [];
    }
}
