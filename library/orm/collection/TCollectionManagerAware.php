<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\RequiredDependencyException;

/**
 * Трейт для внедрения менеджера коллекций объектов.
 */
trait TCollectionManagerAware
{
    /**
     * @var ICollectionManager $traitCollectionManager менеджер коллекций
     */
    private $traitCollectionManager;

    /**
     * @see ICollectionManagerAware::setCollectionManager()
     */
    public function setCollectionManager(ICollectionManager $collectionManager)
    {
        $this->traitCollectionManager = $collectionManager;
    }

    /**
     * Возвращает менеджер коллекций объектов
     * @throws RequiredDependencyException если менеджер коллекций объектов не установлен
     * @return ICollectionManager
     */
    protected function getCollectionManager()
    {
        if (!$this->traitCollectionManager) {
            throw new RequiredDependencyException(sprintf(
                'Collection manager is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->traitCollectionManager;
    }
}
