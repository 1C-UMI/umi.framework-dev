<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\form\element\Checkbox;

/**
 * Тесты элемента формы - Флаг
 */
class CheckboxElementTest extends BaseElementTest
{

    /**
     * Базовые тесты.
     */
    public function testBasic()
    {
        $element = $this->getFormElement('testElement', ['data-id' => 'id', 'value' => 1], ['value' => 1]);

        $this->assertArrayHasKey(
            'data-id',
            $element->getAttributes(),
            'Ожидается, что аттрибуты будут установлены.'
        );

        $this->assertEquals('testElement', $element->getName(), 'Ожидается, что имя элемента будет установлено.');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormElement($name, array $attributes = [], array $options = [])
    {
        $e = new Checkbox($name, $attributes, $options);
        $this->resolveOptionalDependencies($e);

        return $e;
    }

    public function testValues()
    {
        $element = $this->getFormElement(
            'testElement',
            ['data-id' => 'id', 'value' => 'My Value'],
            ['label' => 'My element']
        );

        $element->setValue(false);
        $this->assertEquals(false, $element->getValue(), 'Ожидается, что значение будет установлено.');
    }
}