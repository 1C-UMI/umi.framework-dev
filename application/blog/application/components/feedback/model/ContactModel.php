<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\components\feedback\model;

use umi\hmvc\model\IModel;

/**
 * Модель обратной связи.
 */
class ContactModel implements IModel
{
    /**
     * @var array $adminMail email администратора
     */
    protected $adminMail = ['administrator@demo.local', 'Administrator'];
    /**
     * @var array $appMail email приложения
     */
    protected $appMail = ['no-reply@demo.local', 'Demo application'];

    /**
     * Отправляет данные о созданном тикете администратору и пользователю.
     * @param array $data данные тикета
     * @return bool
     */
    public function sendContact(array $data)
    {
        return $this->sendUserNotification($data) && $this->sendAdminNotification($data);
    }

    /**
     * Отправляет оповещение пользователю, создавшему заявку.
     * @param array $data данные тикета
     * @return bool
     */
    protected function sendUserNotification(array $data)
    {
        // todo:
    }

    /**
     * Отправляет оповещение администратору сайта.
     * @param array $data данные тикета
     * @return bool
     */
    protected function sendAdminNotification(array $data)
    {
        // todo:
    }
}