<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element;

/**
 * Элемент формы - Кнопка отправки(submit).
 * @example <input type="submit" />
 */
class Submit extends Button
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'submit';

    /**
     * {@inheritdoc}
     */
    protected $type = 'submit';

    /**
     * @var string $buttonType тип кнопки
     */
    protected $buttonType = 'submit';

}