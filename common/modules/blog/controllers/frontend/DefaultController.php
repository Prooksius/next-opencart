<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

namespace common\modules\blog\controllers\frontend;

use common\modules\blog\models\BlogCategory;
use common\modules\blog\models\BlogComment;
use common\modules\blog\models\BlogCommentSearch;
use common\modules\blog\models\BlogPost;
use common\modules\blog\models\BlogPostSearch;
use common\modules\blog\Module;
use common\modules\blog\traits\IActiveStatus;
use common\modules\blog\traits\ModuleTrait;
use frontend\models\Settings;
use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    use ModuleTrait;

    public $load_count = 15;
    public $layout = 'blog';

    /**
     * @var null|int maximum comments level, level starts from 1, null - unlimited level;
     */
    public $maxLevel = 10;

    /**
     * @var array DataProvider config - for comments
     */
    public $dataProviderConfig = [
        'pagination' => [
            'pageSize' => 10,
        ],
    ];

    /**
     * @var array ListView config
     */
    public $listViewConfig = [
        'emptyText' => '',
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'lesha724\MathCaptcha\MathCaptchaAction',
            ],
        ];
    }

    public function actionIndex($tag = '')
    {
        Yii::$app->session['current_page'] = 1;

//        if ($tag) {
            $query = BlogPost::find()
                ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])->innerJoinWith('category')
                ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
                ->andWhere(['LIKE', '{{%blog_post}}.tags', $tag])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit($this->load_count);
/*        } else {
            $query = BlogPost::find()
                ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])->innerJoinWith('category')
                ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit($this->load_count);
        }
*/
        $categories = BlogCategory::find()
            ->where(['status' => IActiveStatus::STATUS_ACTIVE, 'is_nav' => BlogCategory::IS_NAV_YES])
            ->orderBy(['sort_order' => SORT_ASC])->all();

        $cat_items = ArrayHelper::toArray($categories, [
            'common\modules\blog\models\BlogCategory' => [
                'label' => 'title',
                'url' => function ($cat) {
                    return ['default/index', 'category_id' => $cat->id, 'slug' => $cat->slug];
                },
            ],
        ]);

        return $this->render('index', [
            'query' => $query,
            'cat_items' => $cat_items,
            'next' => ceil($query->count() / $this->load_count) - Yii::$app->session['current_page'],
            'page' => Yii::$app->session['current_page'],
            'seo' => Settings::readAllSettings('blog'),
        ]);
    }

    public function actionAll()
    {
        $query = BlogPost::find()
            ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])
            ->innerJoinWith('category')
            ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_DESC]);

        return $this->render('all_simple', [
            'query' => $query,
            'seo' => Settings::readAllSettings('blog'),
        ]);
    }

    public function actionCategory($category_id)
    {
        Yii::$app->session['current_page'] = 1;

        $cat_query = BlogCategory::findOne(['id' => $category_id, 'status' => IActiveStatus::STATUS_ACTIVE]);
        $query = BlogPost::find()
            ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])->innerJoinWith('category')
            ->andWhere(['{{%blog_category}}.id' => $category_id])
            ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($this->load_count);

        return $this->render('category', [
            'query' => $query,
            'cat_query' => $cat_query,
            'next' => ceil($query->count() / $this->load_count) - Yii::$app->session['current_page'],
            'page' => Yii::$app->session['current_page'],
            'seo' => Settings::readAllSettings('blog'),
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $post = BlogPost::find()->where(['status' => IActiveStatus::STATUS_ACTIVE, 'id' => $id])->one();
        if ($post === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $post->updateCounters(['click' => 1]);

        $dataProvider = new ArrayDataProvider($this->dataProviderConfig);
        if (!isset($this->dataProviderConfig['allModels'])) {
            $dataProvider->allModels = BlogComment::getTree($id, $this->maxLevel);
        }

        $searchModel = new BlogCommentSearch();
        $searchModel->scenario = BlogComment::SCENARIO_USER;
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);


        $comment = new BlogComment();
        $comment->scenario = BlogComment::SCENARIO_USER;
        $comment->post_id = $id;

        $other_posts = BlogPost::find()
            ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])->innerJoinWith('category')
            ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
            ->andWhere(['NOT', ['{{%blog_post}}.id' => $id]])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5);

        return $this->render('view', [
            'post' => $post,
            'other_posts' => $other_posts,
            'dataProvider' => $dataProvider,
            'comment' => $comment,
            'listViewConfig' => $this->listViewConfig,
            'maxLevel' => $this->maxLevel,
        ]);
    }

    public function actionUpdatecomment($id)
    {
        if (Yii::$app->getRequest()->getIsAjax()) {
            $comment = new BlogComment();
            $comment->scenario = BlogComment::SCENARIO_USER;
            $comment->post_id = $id;

            if ($comment->load(Yii::$app->request->post()) && $comment->saveComment()) {
                return $this->asJson(['success' => true]);
            }
            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($comment->getErrors() as $attribute => $errors) {
                $result[yii\helpers\Html::getInputId($comment, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }
    }

    public function actionVerifycaptcha()
    {
        if (Yii::$app->request->isPost) {

            $success = false;

            $captcha_token = Yii::$app->request->post('token', '');
            $captcha_action = Yii::$app->request->post('action', '');
            if (!$captcha_token || !$captcha_action) {
                return $this->asJson($success);
            }

            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $params = [
                'secret' => '6LezU60UAAAAAMxHlj2qsS8CJ8mNdvqh-AgASnLB',
                'response' => $captcha_token,
                'remoteip' => Yii::$app->request->userIP,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            if(!empty($response)) $decoded_response = json_decode($response);

            if ($decoded_response && $decoded_response->success && $decoded_response->action == $captcha_action && $decoded_response->score > 0) {
                $success = $decoded_response->success;
                // обрабатываем данные формы, которая защищена капчей
            } else {
                // прописываем действие, если пользователь оказался ботом
            }

            return $this->asJson($success);
        }
    }

    /**
     * Displays Blog internal page.
     *
     * @return mixed
     */
    private function _getPage($query)
    {
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->setPageSize($this->load_count);
        $pages->setPage(Yii::$app->session['current_page']);
//            var_dump($pages->offset);
        $models = $query->offset($pages->offset)->limit($pages->limit);
        $max_pages = $countQuery->count() / $this->load_count;
        if (Yii::$app->session['current_page'] > ceil($max_pages)) {
            return Json::encode(['data' => false, 'next' => 0]);
        } else {
            Yii::$app->session['current_page'] = Yii::$app->session['current_page'] + 1;
            return Json::encode(['data' => $this->renderPartial('blogposts', [
                'query' => $models,
                'page' => Yii::$app->session['current_page'],
            ]), 'next' => (ceil($max_pages) - Yii::$app->session['current_page'])]);
        }
    }

    /**
     * Displays Blog page.
     *
     * @return mixed
     */
    public function actionPage()
    {
        if (Yii::$app->getRequest()->getIsAjax()) {

            $query = BlogPost::find()
                ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])->innerJoinWith('category')
                ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
                ->orderBy(['created_at' => SORT_DESC]);

            return $this->_getPage($query);
        }
    }

    /**
     * Displays Blog page.
     *
     * @return mixed
     */
    public function actionCatpage($category_id)
    {
        if (Yii::$app->getRequest()->getIsAjax()) {

            $query = BlogPost::find()
                ->andWhere(['{{%blog_post}}.status' => IActiveStatus::STATUS_ACTIVE])->innerJoinWith('category')
                ->andWhere(['{{%blog_category}}.id' => $category_id])
                ->andWhere(['{{%blog_category}}.status' => IActiveStatus::STATUS_ACTIVE])
                ->orderBy(['created_at' => SORT_DESC]);

            return $this->_getPage($query);
        }
    }
}
