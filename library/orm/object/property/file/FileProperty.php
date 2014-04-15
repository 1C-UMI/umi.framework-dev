<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property\file;

use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;
use umi\orm\object\property\BaseProperty;

/**
 * Класс свойства со значением типа файл
 */
class FileProperty extends BaseProperty implements IFileProperty
{
    /**
     * Конструктор
     * @param IObject $object владелец свойства
     * @param IField $field поле типа данных
     */
    public function __construct(IObject $object, IField $field)
    {
        $this->object = $object;
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function update($value)
    {
        $this->dbValue = $this->field->prepareDbValue($this->object, $this->value);
        $this->isModified = true;
        $this->object->setIsModified();
    }
}
 