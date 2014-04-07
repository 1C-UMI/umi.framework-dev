<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event;

use umi\event\exception\RequiredDependencyException;

/**
 * Трейт для поддержки событий.
 */
trait TEventObservant
{
    /**
     * @var IEventManager $traitEventManager локальный менеджер событий
     */
    private $traitEventManager;
    /**
     * @var IEventFactory $traitEventFactory
     */
    private $traitEventFactory;

    /**
     * @see IEventObservant::setEventFactory()
     */
    public function setEventFactory(IEventFactory $eventFactory)
    {
        $this->traitEventFactory = $eventFactory;
    }

    /**
     * Возвращает менеджер событий
     * @internal
     * @throws RequiredDependencyException если фабрика событий и менеджеров событий не была внедрена
     * @return IEventManager
     */
    public function getEventManager()
    {
        if (!$this->traitEventManager) {
            if (!$this->traitEventFactory) {
                $this->traitEventFactory = new EventFactory();
            }
            $this->traitEventManager = $this->traitEventFactory->createEventManager();
            $this->bindLocalEvents();
        }
        return $this->traitEventManager;
    }

    /**
     * Подписаться на события другого IEventObservant
     * @param IEventObservant $target
     * @return $this
     */
    public function subscribeTo(IEventObservant $target)
    {
        $target->getEventManager()->attach($this->getEventManager());
        return $this;
    }

    /**
     * Генерирует новое событие
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param array $params список параметров события array('paramName' => 'paramVal', 'relParam' => &$var)
     * Параметр может передаваться по ссылке.
     * @param array $tags тэги, с которыми происходит событие
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * @return bool возвращает true, если хотя бы один обработчик был вызван, false - в противном случае
     */
    public function fireEvent($eventType, array $params = [], array $tags = [])
    {
        return $this->getEventManager()->fireEvent($eventType, $this, $params, $tags);
    }

    /**
     * Устанавливает обработчик события.
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * Если тэги не указаны, обработчик будет вызван для всех объектов, поднимающих события.
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler callable обработчик события
     * @param array $tags тэги, с которыми должно происходить событие, на которое устанавливается обработчик
     * @return $this
     */
    public function bindEvent($eventType, callable $eventHandler, array $tags = [])
    {
        return $this->getEventManager()->bindEvent($eventType, $eventHandler, $tags);
    }

    /**
     * Удаляет обработчик(и) события указанного типа
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler конкретный обработчик события, если null,
     * будут удалены все обработчики событий указанного типа
     * @return $this
     */
    public function unbindEvent($eventType, callable $eventHandler = null)
    {
        return $this->getEventManager()->unbindEvent($eventType, $eventHandler);
    }

    /**
     * Устанавливает локальные обработчики событий
     * @return $this
     */
    protected function bindLocalEvents()
    {
        return $this;
    }
}
