<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\orm\exception\UnexpectedValueException;

/**
 * Трейт для полей связи.
 */
trait TRelationField
{

    /**
     * @var string $targetCollectionName имя коллекции на которую выставлена связь (modelName.collectionName)
     */
    protected $targetCollectionName;
    /**
     * @var string $targetFieldName имя связанного поля, по которому осуществляется связь с target-коллекцией
     */
    protected $targetFieldName;
    /**
     * @var string $bridgeCollectionName имя коллекции, которая является мостом для связи manyToMany
     */
    protected $bridgeCollectionName;
    /**
     * @var string $relatedFieldName имя связанного поля в bridge-коллекции у полей с типом связи manyToMany
     */
    protected $relatedFieldName;

    /**
     * @see IField::getName()
     */
    abstract public function getName();
    /**
     * @see TLocalizable::translate()
     */
    abstract protected function translate($message, array $placeholders = [], $localeId = null);


    /**
     * Разбирает и применяет конфигурацию для target-коллекции
     * @param array $config конфигурация поля
     * @throws UnexpectedValueException
     */
    protected function applyTargetCollectionConfig(array $config)
    {
        if (isset($config['target']) && is_string($config['target'])) {
            $this->targetCollectionName = $config['target'];
        } elseif (isset($config['collectionName']) && is_string($config['collectionName'])) {
            $this->targetCollectionName = $config['collectionName'];
        } else {
            throw new UnexpectedValueException($this->translate(
                'Relation field "{field}" configuration should contain either "target" or "collectionName" option.',
                ['field' => $this->getName()]
            ));
        }
    }

    /**
     * Разбирает и применяет конфигурацию для bridge-коллекции
     * @param array $config конфигурация поля
     * @throws UnexpectedValueException
     */
    protected function applyBridgeCollectionConfig(array $config)
    {
        if (!isset($config['bridge']) || !is_string($config['bridge'])) {
            throw new UnexpectedValueException($this->translate(
                'Relation field configuration should contain bridge collection name and name should be a string.'
            ));
        }
        $this->bridgeCollectionName = $config['bridge'];
    }

    /**
     * Разбирает и применяет конфигурацию для связанного поля в bridge-коллекции
     * @param array $config конфигурация поля
     * @throws UnexpectedValueException
     */
    protected function applyRelatedFieldConfig(array $config)
    {
        if (!isset($config['relatedField']) || !is_string($config['relatedField'])) {
            throw new UnexpectedValueException($this->translate(
                'Relation field configuration should contain related field name and name should be a string.'
            ));
        }
        $this->relatedFieldName = $config['relatedField'];
    }

    /**
     * Разбирает и применяет конфигурацию для поля, по которому осуществляется связь с target-коллекцией
     * @param array $config конфигурация поля
     * @throws UnexpectedValueException
     */
    protected function applyTargetFieldConfig(array $config)
    {
        if (!isset($config['targetField']) || !is_string($config['targetField'])) {
            throw new UnexpectedValueException($this->translate(
                'Relation field configuration should contain target field name and name should be a string.'
            ));
        }
        $this->targetFieldName = $config['targetField'];
    }

}
