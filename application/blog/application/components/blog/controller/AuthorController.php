<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\components\blog\controller;

use application\components\blog\model\AuthorModel;
use umi\hmvc\controller\BaseController;

/**
 * Контроллер отображения постов по авторам.
 */
class AuthorController extends BaseController
{
    /**
     * @var AuthorModel $authorModel модель компонента
     */
    protected $authorModel;

    /**
     * Конструктор.
     * @param AuthorModel $model
     */
    public function __construct(AuthorModel $model)
    {
        $this->authorModel = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $guid = $this->getRouteVar('id');

        $author = $this->authorModel->getAuthor($guid);

        return $this->createViewResponse(
            'author',
            [
                'author' => $author,
                'posts'  => $this->authorModel->getAuthorPosts($author)
            ]
        );
    }
}
