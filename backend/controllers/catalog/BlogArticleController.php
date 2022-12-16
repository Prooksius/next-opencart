<?php

namespace backend\controllers\catalog;

use app\models\Category;
use app\models\Manufacturer;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\BlogArticle;
use app\models\BlogArticleSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BlogArticleController implements the CRUD actions for BlogArticle model.
 */
class BlogArticleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all BlogArticle models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $searchModel = new BlogArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new BlogArticle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogArticle();
        $max_sort = BlogArticle::find()
            ->select(['MAX(sort_order)'])
            ->scalar();

        $model->sort_order = $max_sort + 10;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlogArticle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->main_category_id = $model->fetchMainCategoryId();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->get('inplace_edit', '0') == '1') {
                $field = '';
                foreach (Yii::$app->request->post() as $arr) {
                    if (is_array($arr)) {
                        foreach ($arr as $value) {
                            $field = $value;
                        }
                    }
                }
                return \yii\helpers\Json::encode(['success' => '1', 'value' => $field]);
            } else {
                return $this->goBack();
            }
        }

        return $this->render('update', [
          'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BlogArticle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BlogArticle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogArticle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogArticle::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
