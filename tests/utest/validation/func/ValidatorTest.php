<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\func;

use umi\validation\IValidator;
use umi\validation\IValidatorCollection;
use umi\validation\IValidatorFactory;
use utest\validation\ValidationTestCase;

/**
 * Тестирование валидаторов
 */
class ValidatorTest extends ValidationTestCase
{
    /**
     * @var IValidatorFactory $factory фабрика валидаторов
     */
    protected $factory = null;

    /**
     * Создание инструментария валидации
     */
    public function setUpFixtures()
    {
        $this->factory = $this->getTestToolkit()->getService('umi\validation\IValidatorFactory');
    }

    /**
     * Тестирование работы 1го валидатора
     */
    public function testSingleValidator()
    {
        /**
         * @var IValidator $validator
         */
        $validator = $this->factory
            ->createValidator(IValidatorFactory::TYPE_REGEXP, ['pattern' => '/[0-9]+/']);

        $this->assertTrue(
            $validator->isValid("1234"),
            "Ожидается, что число должно пройти валидацию по заданому регулярному выражению"
        );
        $this->assertFalse(
            $validator->isValid("test"),
            "Ожидается, что строка не должна пройти валидацию по заданому регулярному выражению"
        );

        $this->assertEquals(
            "String does not meet regular expression.",
            $validator->getMessage(),
            "Ожидается, что валидатор содержит сообщение об ошибке"
        );
    }

    /**
     * Тестирование работы коллекции валидаторов
     */
    public function testMultipleValidator()
    {
        /**
         * @var IValidatorCollection $validatorCollection
         */
        $validatorCollection = $this->factory->createValidatorCollection(
            [
                IValidatorFactory::TYPE_REQUIRED => [],
                IValidatorFactory::TYPE_EMAIL    => []
            ]
        );

        $this->assertTrue($validatorCollection->isValid("example@email.com"), "Ожидается, что email должен пройти валидацию");
        $this->assertFalse($validatorCollection->isValid(""), "Ожидается, что пустая строка не должна пройти валидацию");

        $this->assertEquals(
            ['Value is required.', 'Wrong email format.'],
            $validatorCollection->getMessages(),
            "Ожидается, что оба валидатора вернут сообщение об ошибке"
        );
    }

    /**
     * Проверка правильной установки опций, при создании коллекции валидаторов
     */
    public function testMultipleValidatorOptions()
    {
        /**
         * @var IValidatorCollection $validatorCollection
         */
        $validatorCollection = $this->factory->createValidatorCollection(
            [
                IValidatorFactory::TYPE_REQUIRED => [],
                IValidatorFactory::TYPE_REGEXP   => ['pattern' => '/[0-9]+/']
            ]
        );

        $this->assertTrue($validatorCollection->isValid("1234"), "Ожидается, что число должно пройти валидацию");
    }
}