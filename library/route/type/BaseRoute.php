<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type;

/**
 * Абстрактный базовый класс для правил маршрутизатора.
 */
abstract class BaseRoute implements IRoute
{
    /**
     * @var string $route правило маршрутизатора
     */
    protected $route;
    /**
     * @var array $defaults параметры по-умолчанию
     */
    protected $defaults = [];
    /**
     * @var int $priority приоритет маршрута
     */
    protected $priority = 1;
    /**
     * @var IRoute[] $subroutes дочерние правила маршрутизатора
     */
    protected $subroutes = [];
    /**
     * @var array $params параметры, полученные при разборе
     */
    protected $params = [];

    /**
     * Конструктор.
     * @param array $options опции маршрута
     * @param IRoute[] $subroutes дочерние правила маршрутизатора
     */
    public function __construct(array $options = [], array $subroutes = [])
    {
        if (isset($options[self::OPTION_ROUTE])) {
            $this->route = $options[self::OPTION_ROUTE];
        }

        if(isset($options[self::OPTION_DEFAULTS])) {
            $this->defaults = $options[self::OPTION_DEFAULTS];
        }

        $this->subroutes = $subroutes;
    }

    /**
     * {@inheritdoc}
     */
    public function getParams()
    {
        return $this->params + $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public final function getSubRoutes()
    {
        return $this->subroutes;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }
}