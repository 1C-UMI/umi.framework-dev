<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\toolbox;

use umi\pagination\IPaginationAware;
use umi\pagination\IPaginatorFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты работы с постраничной навигацией.
 */
class PaginationTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'pagination';

    use TToolbox;

    /**
     * @var string $paginatorFactoryClass класс фабрики для создания постраничной навигации
     */
    public $paginatorFactoryClass = 'umi\pagination\toolbox\factory\PaginatorFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'paginator',
            $this->paginatorFactoryClass,
            ['umi\pagination\IPaginatorFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\pagination\IPaginatorFactory':
                return $this->getPaginatorFactory();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IPaginationAware) {
            $object->setPaginatorFactory($this->getPaginatorFactory());
        }
    }

    /**
     * Возвращает фабрику постраничной навигации.
     * @return IPaginatorFactory
     */
    protected function getPaginatorFactory()
    {
        return $this->getFactory('paginator');
    }

}