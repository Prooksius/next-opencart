<?php

namespace backend\controllers\catalog;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\BlogCategory;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BlogCategoryController implements the CRUD actions for BlogCategory model.
 */
class BlogCategoryController extends Controller
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
   * Lists all BlogCategory models.
   * @return mixed
   */
  public function actionIndex($parent_id = 0)
  {

    $parent = 0;
    if ($parent_id) {
      $parent = $this->findModel($parent_id);
    }

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = BlogCategory::find()
      ->alias('bc')
      ->select([
        'bc.*',
        'bcd.name',
        '(SELECT COUNT(bc1.id) FROM oc_newsblog_category bc1 WHERE bc1.parent_id = bc.id) AS child_count',
        '(SELECT COUNT(ba.id) 
          FROM oc_newsblog_article ba 
          LEFT JOIN oc_newsblog_article_to_category ba2bc ON (ba2bc.article_id = ba.id)
          WHERE ba2bc.category_id = bc.id
        ) AS article_count',
      ])
      ->leftJoin('oc_newsblog_category_description bcd', '(bcd.category_id = bc.id AND bcd.language_id = "' . \Yii::$app->language . '")')
      ->where(['bc.parent_id' => $parent_id]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'name',
          'sort_order',
          'article_count',
        ],
        'defaultOrder'=>[
          'sort_order'=>SORT_ASC
        ],
      ],
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'parent_id' => $parent_id,
      'up_level' => $parent_id ? $parent->parent_id : -1,
    ]);
  }

  /**
   * Creates a new BlogCategory model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($parent_id = 0)
  {

    $model = new BlogCategory();
    $max_sort = BlogCategory::find()
      ->select(['MAX(sort_order)'])
      ->where(['parent_id' => $parent_id])
      ->scalar();

    $model->sort_order = $max_sort + 10;
    $model->parent_id = $parent_id;

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }

    return $this->render('create', [
      'model' => $model,
      'parent_id' => $parent_id,
    ]);
  }

  /**
   * Updates an existing BlogCategory model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

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
   * Repair
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionRepair()
  {
    BlogCategory::repairCategories();

    return $this->goBack();
  }

  /**
   * Deletes an existing BlogCategory model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();

    return $this->goBack();
  }

  /**
   * Finds the BlogCategory model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return BlogCategory the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = BlogCategory::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
