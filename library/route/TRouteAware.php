<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route;

use umi\route\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки маршрутеризации.
 */
trait TRouteAware
{
    /**
     * @var IRouteFactory $_routerFactory фабрика
     */
    private $_routeFactory;

    /**
     * Устанавливает фабрику для создания маршрутеризатора.
     * @param IRouteFactory $routerFactory фабрика
     */
    public function setRouteFactory(IRouteFactory $routerFactory)
    {
        $this->_routeFactory = $routerFactory;
    }

    /**
     * Создает маршрутеризатор на основе конфигурации.
     * @param array $config конфигурация
     * @return IRouter
     */
    protected function createRouter(array $config)
    {
        return $this->getRouterFactory()
            ->createRouter($config);
    }

    /**
     * Возвращает фабрику для создания маршрутеризаторов.
     * @return IRouteFactory
     * @throws RequiredDependencyException
     */
    private function getRouterFactory()
    {
        if (!$this->_routeFactory) {
            throw new RequiredDependencyException(sprintf(
                'Route factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_routeFactory;
    }
}
