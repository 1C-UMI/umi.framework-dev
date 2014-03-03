<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc;

/**
 * Интерфейс для внедрения фабрики сущностей компонента MVC.
 */
interface IMvcEntityFactoryAware
{
    /**
     * Устанавливает фабрику MVC сущностей.
     * @param IMvcEntityFactory $factory фабрика
     */
    public function setMvcEntityFactory(IMvcEntityFactory $factory);
}
 