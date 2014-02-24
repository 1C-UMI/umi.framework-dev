<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\exception\http;

use umi\http\Response;

/**
 * Исключение бросаемое для неавторизованного пользователя.
 */
class HttpUnauthorized extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $message, $previous);
    }
}