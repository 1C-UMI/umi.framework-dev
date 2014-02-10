<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository  http://github.com/Umisoft/framework for the canonical source repositoryhttp://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\monolog\func;

use umi\monolog\toolbox\MonologTools;
use utest\monolog\MonologTestCase;

/**
 * Тестирование логгирования
 */
class LoggingTest extends MonologTestCase
{
    /**
     * @var MonologTools $factory
     */
    protected $tools;
    /**
     * @var string $fsLogFile файл для логгирования
     */
    protected $fsLogFile;

    public function setUpFixtures()
    {
        $this->fsLogFile = __DIR__ . DIRECTORY_SEPARATOR . 'testLog';
    }

    public function testBaseFunctionality()
    {
        $this->getTestToolkit()->setSettings(
            [
                MonologTools::NAME => [
                    'handlers'=>[
                        'path'=>$this->fsLogFile
                    ]
                ]
            ]
        );

        /**
         * @var ILogger $logger
         */
        $logger = $this->getTestToolkit()->getLogger();

        $logger->info(
            'Logged from {function}',
            [
                'function' => __FUNCTION__
            ]
        );

        $this->assertEquals(
            "info | Logged from testBaseFunctionality" . PHP_EOL,
            file_get_contents($this->fsLogFile),
            "Ожидается, что сообщение запишется в лог"
        );
    }

    public function testLogLevel()
    {
        $this->getTestToolkit()->setSettings(
            [
                LogTools::NAME => [
                    'type' => LogTools::TYPE_FILE,
                    'messageFormat' => '{level} | {message}',
                    'minLevel' => 'error',
                    'options' => [
                        'filename' => $this->fsLogFile
                    ]
                ]
            ]
        );

        /**
         * @var ILogger $logger
         */
        $logger = $this->getTestToolkit()->getService('umi\log\ILogger');

        $logger->info(
            'Info from {function}',
            [
                'function' => __FUNCTION__
            ]
        );

        $logger->error(
            'Error from {function}',
            [
                'function' => __FUNCTION__
            ]
        );

        $this->assertEquals(
            "error | Error from testMinLogLevel" . PHP_EOL,
            file_get_contents($this->fsLogFile),
            "Ожидается, что сообщение запишется в лог"
        );
    }

}
