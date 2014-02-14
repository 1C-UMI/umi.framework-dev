<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n;

use umi\i18n\translator\ITranslator;

/**
 * Интерфейс для поддержки локализации.
 */
interface ILocalizable
{

    /**
     * Устанавливает транслятор для локализации
     * @param ITranslator $translator
     */
    public function setTranslator(ITranslator $translator);
}
