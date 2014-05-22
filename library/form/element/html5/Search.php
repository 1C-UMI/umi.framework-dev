<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element\html5;

use umi\form\element\BaseFormInput;

/**
 * HTML5 элемент формы - поиск (search).
 * @example <input type="search" />
 */
class Search extends BaseFormInput
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'search';

    /**
     * {@inheritdoc}
     */
    protected $type = 'search';
    /**
     * {@inheritdoc}
     */
    protected $inputType = 'search';
}