<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\func;

use umi\route\IRouter;
use utest\route\RouteTestCase;

/**
 * Тесты инструментария роутинга.
 */
class RouteTest extends RouteTestCase
{
    /**
     * @var IRouter $router
     */
    public $router;

    public function setUpFixtures()
    {
        $routeFactory = $this->getTestToolkit()->getService('umi\route\IRouteFactory');
        $this->router = $routeFactory->createRouter(require 'routes.php');
    }

    public function testMatch()
    {
        $result = $this->router->match('/application');
        $this->assertEquals('application', $result->getName(), 'Ожидается, что отработает роут с заданным именем.');
        $this->assertEquals(
            [
                'application' => 'public',
                'module'      => 'base',
                'method'      => 'index'
            ],
            $result->getMatches(),
            'Ожидается, что параметры будут получены.'
        );

        $result = $this->router->match('/application/vote/poll');
        $this->assertEquals(
            'application/default',
            $result->getName(),
            'Ожидается, что отработает роут с заданным именем.'
        );
        $this->assertEquals(
            [
                'application' => 'public',
                'module'      => 'vote',
                'method'      => 'poll'
            ],
            $this->router->match('/application/vote/poll')
                ->getMatches()
        );

        $result = $this->router->match('/application/login');
        $this->assertEquals(
            'application/login',
            $result->getName(),
            'Ожидается, что отработает роут с заданным именем.'
        );
        $this->assertEquals(
            [
                'application' => 'public',
                'module'      => 'user',
                'method'      => 'login'
            ],
            $result->getMatches()
        );

        $result = $this->router->match('/application/login');
        $this->assertEquals(
            'application/login',
            $result->getName(),
            'Ожидается, что отработает роут с заданным именем.'
        );
        $this->assertEquals(
            [
                'application' => 'public',
                'module'      => 'user',
                'method'      => 'login'
            ],
            $result->getMatches()
        );

        $result = $this->router->match('/application/vote/poll/test');
        $this->assertEquals(
            'application/default',
            $result->getName(),
            'Ожидается, что отработает роут с заданным именем.'
        );
        $this->assertEquals(
            [
                'application' => 'public',
                'module'      => 'vote',
                'method'      => 'poll'
            ],
            $result->getMatches()
        );
    }

    public function testPriority()
    {
        $result = $this->router->match('/admin');
        $this->assertEquals('admin', $result->getName(), 'Ожидается, что был выбран более приоритетный маршрут.');
    }

    public function testAssemble()
    {
        $this->assertEquals(
            '/application/moduleName/methodName',
            $this->router->assemble('application/default', ['module' => 'moduleName', 'method' => 'methodName'])
        );

        $this->assertEquals(
            '/application/login',
            $this->router->assemble('application/login', [])
        );
    }

    /**
     * @test исключение, если не передан обязательный параметр
     * @expectedException \umi\route\exception\RuntimeException
     */
    public function noRequiredParam()
    {
        $this->router->assemble('admin/edit');
    }
}