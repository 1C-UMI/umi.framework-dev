<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation;

/**
 * Класс коллекции валидаторов.
 * По очереди валидирует значение каждым валидатором.
 */
class ValidatorCollection implements \IteratorAggregate, IValidatorCollection
{
    /**
     * @var IValidator[] $collection коллекция валидаторов в виде [[имя] => IValidator, ...]
     */
    protected $collection;
    /**
     * @var array $messages ошибки валидации
     */
    protected $messages;

    /**
     * Создает коллекцию валидаторов на базе массива валидаторов
     * @param IValidator[] $validators валидаторы
     */
    public function __construct(array $validators)
    {
        $this->collection = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $this->messages = [];
        $isValid = true;

        foreach ($this->collection as $validator) {
            $isValid = $validator->isValid($value) && $isValid;
            if ($message = $validator->getMessage()) {
                $this->messages[] = $message;
            }
        }

        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function appendValidator(IValidator $validator)
    {
        array_push($this->collection, $validator);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prependValidator(IValidator $validator)
    {
        array_unshift($this->collection, $validator);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }
}