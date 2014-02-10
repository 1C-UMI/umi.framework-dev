<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\extension\twig;

use Twig_Extension;
use Twig_SimpleFunction;
use umi\hmvc\dispatcher\IDispatcher;
use umi\hmvc\view\helper\IsAllowedHelper;
use umi\hmvc\view\helper\UrlHelper;

/**
 * Расширение Twig для подключения помощников вида.
 */
class ViewTwigExtension extends Twig_Extension
{
    /**
     * @var string $macrosFunctionName имя функции для вызова макроса
     */
    public $macrosFunctionName = 'macros';
    /**
     * @var string $urlFunctionName имя функции для генерации URL
     */
    public $urlFunctionName = 'url';
    /**
     * @var string $isAllowedFunctionName имя функции для проверки прав
     */
    public $isAllowedFunctionName = 'isAllowed';

    /**
     * @var IDispatcher $dispatcher диспетчер для вызова макроса
     */
    protected $dispatcher;
    /**
     * @var UrlHelper $urlHelper
     */
    private $urlHelper;
    /**
     * @var IsAllowedHelper $isAllowedHelper
     */
    private $isAllowedHelper;

    /**
     * Конструктор.
     * @param IDispatcher $dispatcher диспетчер для вызова макроса
     */
    public function __construct(IDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
    * {@inheritdoc}
    */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {

        return [
            new Twig_SimpleFunction(
                $this->macrosFunctionName,
                $this->getMacrosHelper(),
                ['is_safe' => ['html']]
            ),

            new Twig_SimpleFunction(
                $this->urlFunctionName,
                $this->getUrlHelper()
            ),
            new Twig_SimpleFunction(
                $this->isAllowedFunctionName,
                $this->getIsAllowedHelper()
            )
        ];
    }

    /**
     * Возвращает помощник вида для генерации URL.
     * @return callable
     */
    protected function getUrlHelper()
    {
        if (!$this->urlHelper) {
            $this->urlHelper = new UrlHelper($this->dispatcher);
        }
        return $this->urlHelper;
    }

    /**
     * Возвращает помощник вида для проверки прав.
     * @return callable
     */
    protected function getIsAllowedHelper()
    {
        if (!$this->isAllowedHelper) {
            $this->isAllowedHelper = new IsAllowedHelper($this->dispatcher);
        }
        return $this->isAllowedHelper;
    }

    /**
     * Возвращает помощник вида для вызова макросов
     * @return callable
     */
    protected function getMacrosHelper()
    {
        return  function($macrosPath, array $args = []) {
            return $this->dispatcher->executeMacros($macrosPath, $args);
        };
    }

}
 