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
 * Элемент формы - Многострочное текстовое поле(textarea).
 * @example <textarea></textarea>
 */
class Textarea extends BaseFormElement
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'textarea';

    /**
     * {@inheritdoc}
     */
    protected $type = 'textarea';
    /**
     * {@inheritdoc}
     */
    protected $tagName = 'textarea';
}