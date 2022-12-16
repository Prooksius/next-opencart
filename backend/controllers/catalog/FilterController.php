<?php

namespace backend\controllers\catalog;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\FilterGroup;
use app\models\Filter;
use common\models\FilterGroupDesc;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FilterController implements the CRUD actions for Filter model.
 */
class FilterController extends Controller
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
   * Lists all Filter models.
   * @return mixed
   */
  public function actionIndex($filter_group_id)
  {
    $groupDescModel = $this->findGroupModel($filter_group_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = Filter::find()
      ->alias('f')
      ->select([
        'f.*', 
        'fd.*',
      ])
      ->leftJoin('oc_filter_description fd', '(fd.filter_id = f.id AND fd.language_id = "' . \Yii::$app->language . '")')
      ->where(['f.filter_group_id' => $filter_group_id]);

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
      'group_name' => $groupDescModel->name,
      'filter_group_id' => $filter_group_id,
    ]);
  }

  /**
   * Creates a new Filter model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($filter_group_id)
  {
    $model = new Filter();
    $max_sort = Filter::find()
      ->select(['MAX(sort_order)'])
      ->scalar();

    $model->sort_order = $max_sort + 10;
    $model->filter_group_id = $filter_group_id;

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }


    return $this->render('create', [
      'model' => $model,
      'languages' => $languages,
      'filter_group_id' => $filter_group_id,
    ]);
  }

  /**
   * Updates an existing FilterGroup model.
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
   * Deletes an existing FilterGroup model.
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
   * Finds the Filter model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Filter the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Filter::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the FilterGroup model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return FilterGroup the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findGroupModel($id)
  {
    if ((FilterGroup::findOne($id)) !== null) {
      if (($model = FilterGroupDesc::findOne(['filter_group_id' => $id, 'language_id' => \Yii::$app->language])) !== null) {
        return $model;
      }
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
