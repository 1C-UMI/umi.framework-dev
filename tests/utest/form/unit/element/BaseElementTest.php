<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\filter\IFilterCollection;
use umi\filter\IFilterFactory;
use umi\filter\toolbox\factory\FilterFactory;
use umi\form\element\IFormElement;
use umi\validation\IValidatorCollection;
use umi\validation\IValidatorFactory;
use umi\validation\toolbox\factory\ValidatorFactory;
use utest\form\FormTestCase;

/**
 * Базовые тесты элементов.
 */
abstract class BaseElementTest extends FormTestCase
{
    /**
     * Создает элемент с заданными параметрами
     * @param string $name имя элемента
     * @param array $attributes аттрибуты
     * @param array $options опции
     * @return IFormElement элемент
     */
    abstract public function getElement($name, array $attributes = [], array $options = []);

    /**
     * Базовые тесты.
     */
    public function testBasic()
    {
        $element = $this->getElement('testElement', ['data-id' => 'id']);

        $this->assertArrayHasKey(
            'data-id',
            $element->getAttributes()
                ->getArrayCopy(),
            'Ожидается, что аттрибуты будут установлены.'
        );
        $this->assertArrayHasKey(
            'name',
            $element->getAttributes()
                ->getArrayCopy(),
            'Ожидается, что имя будет установлено как аттрибут.'
        );

        $this->assertEquals('testElement', $element->getName(), 'Ожидается, что имя элемента будет установлено.');

        $this->assertEquals($element->getName(), $element->getAttributes()['name']);
    }

    /**
     * Тест установки/получения значений элементов.
     */
    public function testValues()
    {
        $element = $this->getElement(
            'testElement',
            ['data-id' => 'id'],
            ['default' => 'test value', 'label' => 'My element']
        );

        $this->assertSame($element, $element->setValue('New value'));
        $this->assertEquals('New value', $element->getValue(), 'Ожидается, что значение будет установлено.');
    }

    /**
     * @test исключения, при попытке создать элемент без имени.
     * @expectedException \umi\form\exception\InvalidArgumentException
     */
    public function elementWithoutName()
    {
        $this->getElement(null);
    }

    /**
     * Фильтров входных данных.
     */
    public function testValidators()
    {
        $e = $this->getElement('test');

        $this->assertInstanceOf(
            'umi\validation\IValidatorCollection',
            $e->getValidators(),
            'Ожидается, что цепочку валидаторов можно получить у любого элемента.'
        );

        $this->assertSame(
            $e,
            $e->setValidators($this->getValidatorCollection([IValidatorFactory::TYPE_REQUIRED => []])),
            'Ожидается, что будет получен $this'
        );

        $this->assertTrue($e->isValid());
        $e->setValue('');
        $this->assertFalse($e->isValid());
    }

    /**
     * Фильтров входных данных.
     */
    public function testFilters()
    {
        $e = $this->getElement('test');

        $this->assertInstanceOf(
            'umi\filter\IFilterCollection',
            $e->getFilters(),
            'Ожидается, что цепочку фильтров можно получить у любого элемента.'
        );

        $this->assertSame(
            $e,
            $e->setFilters($this->getFilterCollection([IFilterFactory::TYPE_INT => []])),
            'Ожидается, что будет получен $this'
        );

        $e->setValue('1aa');

        $this->assertEquals(1, $e->getValue());
    }

    /**
     * @param array $conf
     * @return IValidatorCollection
     */
    protected function getValidatorCollection(array $conf)
    {
        $validatorFactory = new ValidatorFactory();
        $this->resolveOptionalDependencies($validatorFactory);
        return $validatorFactory->createValidatorCollection($conf);
    }

    /**
     * @param array $conf
     * @return IFilterCollection
     */
    protected function getFilterCollection(array $conf)
    {
        $filterFactory = new FilterFactory();
        $this->resolveOptionalDependencies($filterFactory);
        return $filterFactory->createFilterCollection($conf);
    }
}