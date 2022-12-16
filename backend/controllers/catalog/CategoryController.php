<?php

namespace backend\controllers\catalog;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
   * Lists all Category models.
   * @return mixed
   */
  public function actionIndex($parent_id = 0)
  {

    $parent = 0;
    if ($parent_id) {
      $parent = $this->findModel($parent_id);
    }

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = Category::find()
      ->alias('c')
      ->select([
        'c.*',
        'cd.name',
        '(SELECT COUNT(c1.id) FROM oc_category c1 WHERE c1.parent_id = c.id) AS child_count'
      ])
      ->leftJoin('oc_category_description cd', '(cd.category_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")')
      ->where(['c.parent_id' => $parent_id]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'name',
          'sort_order',
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
   * Creates a new Category model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($parent_id = 0)
  {

    $model = new Category();
    $max_sort = Category::find()
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
      'languages' => $languages,
      'parent_id' => $parent_id,
      'catsTree' => Category::getCategoriesTree(),
    ]);
  }

  /**
   * Updates an existing Category model.
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
      'catsTree' => Category::getCategoriesTree(),
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
    Category::repairCategories();

    return $this->goBack();
  }

  /**
   * Deletes an existing Category model.
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
   * Finds the Category model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Category the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Category::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
