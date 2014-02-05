<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http;

use \Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Компонент работы с HTTP запросом.
 */
class Request extends SymfonyRequest
{

    /**
     * Возвращает реферер запроса.
     * @return string|null
     */
    public function getReferer()
    {
        return $this->headers->get('referer');
    }

}
