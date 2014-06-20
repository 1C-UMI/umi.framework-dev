<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\persister;

use Doctrine\DBAL\Driver\Connection;
use SplObjectStorage;
use umi\event\TEventObservant;
use umi\i18n\ILocalesAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalesAware;
use umi\i18n\TLocalizable;
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\metadata\field\relation\BelongsToRelationField;
use umi\orm\object\IObject;
use umi\orm\object\property\calculable\ICalculableProperty;

/**
 * Синхронизатор объектов бизнес-транзакций с базой данных (Unit Of Work).
 */
class ObjectPersister implements IObjectPersister, ILocalizable, ILocalesAware, IObjectManagerAware
{

    use TLocalizable;
    use TLocalesAware;
    use TObjectManagerAware;
    use TEventObservant;

    /**
     * @var SplObjectStorage|IObject[] $newObjects список новых объектов
     */
    protected $newObjects;
    /**
     * @var SplObjectStorage|IObject[] $deletedObjects список объектов, помеченных на удаление
     */
    protected $deletedObjects;
    /**
     * @var SplObjectStorage|IObject[] $modifiedObjects список измененных объектов
     */
    protected $modifiedObjects;
    /**
     * @var SplObjectStorage|IObject[] $relatedObjects список зависимостей объектов
     */
    protected $relatedObjects;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->newObjects = new SplObjectStorage();
        $this->deletedObjects = new SplObjectStorage();
        $this->modifiedObjects = new SplObjectStorage();
        $this->relatedObjects = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPersisted()
    {
        return !(count($this->modifiedObjects) || count($this->newObjects) || count($this->deletedObjects));
    }

    /**
     * {@inheritdoc}
     */
    public function executeTransaction(callable $transaction, array $affectedConnections = [])
    {
        if (!$this->getIsPersisted()) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot execute transaction. Not all objects are persisted.'
            ));
        }
        $this->startTransaction($affectedConnections);

        try {
            call_user_func($transaction);
        } catch (\Exception $e) {
            $this->rollback($affectedConnections);

            throw new RuntimeException($this->translate(
                'Cannot execute transaction.'
            ), 0, $e);
        }

        $this->commitTransaction($affectedConnections);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvalidObjects()
    {
        $result = [];
        foreach ($this->modifiedObjects as $object) {
            if (!$object->validate()) {
                $result[] = $object;
            }
        }
        foreach ($this->newObjects as $object) {
            if (!$object->validate()) {
                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewObjects()
    {
        return $this->newObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedObjects()
    {
        return $this->modifiedObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedObjects()
    {
        return $this->deletedObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsNew(IObject $object)
    {
        $this->newObjects->attach($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsDeleted(IObject $object)
    {
        $this->deletedObjects->attach($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsModified(IObject $object)
    {
        if (!$object->getIsNew()) {
            $this->modifiedObjects->attach($object);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function storeNewBelongsToRelation(
        BelongsToRelationField $belongsToRelation,
        IObject $object,
        IObject $relatedObject
    )
    {
        $data = [];
        try {
            $data = $this->relatedObjects->offsetGet($object);
        } catch (\UnexpectedValueException $e) {
        }

        $data[$belongsToRelation->getName()] = $relatedObject;
        $this->relatedObjects->offsetSet($object, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function clearObjectsState()
    {
        $this->newObjects->removeAll($this->newObjects);
        $this->relatedObjects->removeAll($this->relatedObjects);
        $this->modifiedObjects->removeAll($this->modifiedObjects);
        $this->deletedObjects->removeAll($this->deletedObjects);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearObjectState(IObject $object)
    {
        $this->newObjects->detach($object);
        $this->modifiedObjects->detach($object);
        $this->deletedObjects->detach($object);
        $this->relatedObjects->detach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {

        if (!$this->getIsPersisted()) {
            $connections = $this->detectUsedConnections();
            $this->startTransaction($connections);

            try {
                $this->processBeforePersistingEvents();
                $this->persist();
            } catch (\Exception $e) {
                $this->rollback($connections);
                throw new RuntimeException($this->translate(
                    'Cannot persist objects.'
                ), 0, $e);
            }

            $this->commitTransaction($connections);
        }

        return $this;
    }

    /**
     * Откатывает начатые транзакции и изменения объектов
     * @param Connection[] $connections
     * @return $this
     */
    protected function rollback(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->rollback();
        }
        $this->unloadStorageObjects($this->newObjects);
        $this->unloadStorageObjects($this->modifiedObjects);
        $this->unloadStorageObjects($this->deletedObjects);
        $this->clearObjectsState();

        return $this;
    }

    /**
     * Стартует транзакцию для указанных соединений с БД
     * @param Connection[] $connections
     * @return $this
     */
    protected function startTransaction(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->beginTransaction();
        }

        return $this;
    }

    /**
     * Фиксирует все начатые транзакции для указанных соединений с БД
     * @param Connection[] $connections
     * @return $this
     */
    protected function commitTransaction(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->commit();
        }

        return $this;
    }

    /**
     * Определяет используемые для редактирования объектов соединения с БД
     * @return Connection[]
     */
    protected function detectUsedConnections()
    {
        $connections = [];

        foreach ($this->newObjects as $object) {
            $source = $object->getCollection()
                ->getMetadata()
                ->getCollectionDataSource();
            $connections[$source->getMasterServerId()] = $source->getMasterServer()
                ->getConnection();
        }
        foreach ($this->deletedObjects as $object) {
            $source = $object->getCollection()
                ->getMetadata()
                ->getCollectionDataSource();
            $connections[$source->getMasterServerId()] = $source->getMasterServer()
                ->getConnection();
        }
        foreach ($this->modifiedObjects as $object) {
            $source = $object->getCollection()
                ->getMetadata()
                ->getCollectionDataSource();
            $connections[$source->getMasterServerId()] = $source->getMasterServer()
                ->getConnection();
        }

        return $connections;

    }

    /**
     * Сохраняет объекты в БД
     * @throws RuntimeException если при сохранении возникли ошибки
     * @return $this
     */
    protected function persist()
    {
        /**
         * @var IObject $object
         */
        foreach ($this->newObjects as $object) {
            $object->getCollection()
                ->persistNewObject($object);

            /**
             * @var ICalculableProperty[] $calculableProperties
             */
            $calculableProperties = [];
            foreach ($object->getAllProperties() as $property) {
                if (!$property->getIsModified() && $property instanceof ICalculableProperty) {
                    $calculableProperties[] = $property;
                }
            }

            $this->getObjectManager()
                ->storeNewObject($object);

            foreach ($calculableProperties as $property) {
                $property->recalculate();
            }
        }

        foreach ($this->relatedObjects as $object) {
            $this->restoreObjectRelations($object, $this->relatedObjects->getInfo());
        }

        foreach ($this->modifiedObjects as $object) {
            $object->getCollection()
                ->persistModifiedObject($object);
        }

        foreach ($this->deletedObjects as $object) {
            $object->getCollection()
                ->persistDeletedObject($object);
        }

        foreach ($this->modifiedObjects as $object) {
            $object->setIsConsistent();
        }

        $this->unloadStorageObjects($this->deletedObjects);
        $this->clearObjectsState();
    }

    /**
     * Восстанавливает значения полей типа relation
     * @param IObject $object
     * @param array $relatedValues
     */
    protected function restoreObjectRelations(IObject $object, array $relatedValues)
    {
        foreach ($relatedValues as $propertyName => $value) {
            $property = $object->getProperty($propertyName);
            $property->setInitialValue(null);
            $property->setValue($value);
        }
    }

    /**
     * Выгружает все объекты в указанном хранилище
     * @param SplObjectStorage $storage storage из IObject
     */
    protected function unloadStorageObjects(SplObjectStorage $storage)
    {
        $storage->rewind();
        while ($storage->valid()) {
            $object = $storage->current();
            $object->unload();
            $storage->detach($object);
        }
    }

    /**
     * Поднимает события перед сохранением объектов
     */
    protected function processBeforePersistingEvents($processedNewObjects = [], $processedModifiedObjects = [], $processedDeletedObjects = [])
    {
        $eventRaised = false;

        foreach($this->newObjects as $object) {

            if (!in_array($object, $processedNewObjects, true)) {

                $newObjectEventRaised = $this->fireEvent(
                    self::EVENT_BEFORE_PERSISTING_NEW_OBJECT,
                    ['object' => $object],
                    [$object->getGUID(), $object->getCollectionName()]
                );

                $processedNewObjects[] = $object;
                $eventRaised = $eventRaised || $newObjectEventRaised;

            }
        }

        foreach($this->modifiedObjects as $object) {

            if (!in_array($object, $processedModifiedObjects, true)) {

                $modifiedObjectEventRaised = $this->fireEvent(
                    self::EVENT_BEFORE_PERSISTING_MODIFIED_OBJECT,
                    ['object' => $object],
                    [$object->getGUID(), $object->getCollectionName()]
                );

                $processedModifiedObjects[] = $object;
                $eventRaised = $eventRaised || $modifiedObjectEventRaised;
            }
        }

        foreach($this->deletedObjects as $object) {

            if (!in_array($object, $processedDeletedObjects, true)) {

                $deletedObjectEventRaised = $this->fireEvent(
                    self::EVENT_BEFORE_PERSISTING_DELETED_OBJECT,
                    ['object' => $object],
                    [$object->getGUID(), $object->getCollectionName()]
                );

                $processedDeletedObjects[] = $object;
                $eventRaised = $eventRaised || $deletedObjectEventRaised;
            }
        }

        if ($eventRaised) {
            $this->processBeforePersistingEvents($processedNewObjects, $processedModifiedObjects, $processedDeletedObjects);
        }
    }
}
