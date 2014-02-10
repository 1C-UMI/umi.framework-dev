<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\components\auth\controller;

use application\model\UserModel;
use umi\form\IForm;
use umi\form\IFormAware;
use umi\form\TFormAware;
use umi\hmvc\controller\BaseController;
use umi\http\Response;

/**
 * Контроллер авторизации.
 */
class LoginController extends BaseController implements IFormAware
{
    use TFormAware;

    /**
     * @var UserModel $userModel модель пользователей
     */
    protected $userModel;

    /**
     * Конструктор.
     * @param UserModel $userModel
     */
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Производит авторизацию в системе.
     * {@inheritdoc}
     */
    public function __invoke()
    {
        if ($this->userModel->isAuthenticated()) {
            return $this->createViewResponse('already', ['user' => $this->userModel->getCurrentUser()]);
        }

        $form = $this->createForm(require dirname(__DIR__) . '/form/login.php');

        if ($this->isRequestMethodPost()) {
            $form->setData($this->getAllPostVars());

            if ($form->isValid()) {
                $data = $form->getData();

                if (!$this->userModel->login($data['email'], $data['password'])) {
                    $response = $this->showForm($form, 'Wrong username or password');
                    $response->setStatusCode(Response::HTTP_FORBIDDEN);

                    return $response;
                };

                return $this->createViewResponse('success', ['user' => $this->userModel->getCurrentUser()]);
            }

            return $this->showForm($form);
        }

        return $this->showForm($form);
    }

    /**
     * Выводит форму авторизации.
     * @param IForm $form форма
     * @param string $flashMessage сообщение [optional]
     * @return Response
     */
    protected function showForm(IForm $form, $flashMessage = null)
    {
        return $this->createViewResponse(
            'login',
            [
                'message' => $flashMessage,
                'form'    => $form
            ]
        );
    }
}
