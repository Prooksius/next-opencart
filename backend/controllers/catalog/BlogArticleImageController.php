<?php

namespace backend\controllers\catalog;

use app\models\BlogArticle;
use app\models\BlogArticleDesc;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\BlogArticleImage;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BlogArticleImageController implements the CRUD actions for BlogArticleImage model.
 */
class BlogArticleImageController extends Controller
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
   * Lists all ProductImage models.
   * @return mixed
   */
  public function actionIndex($article_id)
  {
    $articleDescModel = $this->findArticleModel($article_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = BlogArticleImage::find()
      ->where(['article_id' => $article_id]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'name',
          'sort_order',
        ],
        'defaultOrder'=>[
          'sort_order' => SORT_ASC
        ],
      ],
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'article_name' => $articleDescModel->name,
      'article_id' => $article_id,
    ]);
  }

  /**
   * Creates a new ProductImage model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($article_id)
  {
    $model = new BlogArticleImage();
    $max_sort = BlogArticleImage::find()
      ->select(['MAX(sort_order)'])
      ->where(['article_id' => $article_id])
      ->scalar();

    $model->sort_order = $max_sort + 10;
    $model->article_id = $article_id;

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }

    return $this->render('create', [
      'model' => $model,
      'languages' => $languages,
      'article_id' => $article_id,
    ]);
  }

  /**
   * Updates an existing ProductImage model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    $languages = Language::find()->all();

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
      'languages' => $languages,
    ]);
  }

  /**
   * Deletes an existing ProductImage model.
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
   * Finds the ProductImage model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductImage the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = BlogArticleImage::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the BlogArticleDesc model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return BlogArticleDesc the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findArticleModel($id)
  {
    if ((BlogArticle::findOne($id)) !== null) {
      if (($model = BlogArticleDesc::findOne(['article_id' => $id, 'language_id' => \Yii::$app->language])) !== null) {
        return $model;
      }
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
