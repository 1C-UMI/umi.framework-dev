<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\special;

use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\special\FileField;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тесты файлового поля
 */
class FileFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new FileField(
            'mock',
            IField::TYPE_FILE,
            [
                'sourcePath' => TESTS_ROOT . '/utest/orm/mock',
                'sourceURI' => 'http://example.com'
            ]
        );
    }

    public function testConfig()
    {
        $e = null;
        try {
            new FileField('mock', IField::TYPE_FILE);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле FileField без указания sourcePath'
        );

        $e = null;
        try {
            new FileField('mock', IField::TYPE_FILE, ['sourcePath' => TESTS_ROOT . '/utest/orm/mock']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле FileField без указания sourceURI'
        );
    }
}
 