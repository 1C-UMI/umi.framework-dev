<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\form\element\Hidden;

/**
 * Тесты элемента формы - Скрытый
 */
class HiddenElementTest extends BaseElementTest
{
    /**
     * {@inheritdoc}
     */
    public function getFormElement($name, array $attributes = [], array $options = [])
    {
        $e = new Hidden($name, $attributes, $options);
        $this->resolveOptionalDependencies($e);

        return $e;
    }
}