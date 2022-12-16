<?php

namespace backend\controllers\catalog;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\Option;
use app\models\OptionValue;
use common\models\FilterGroupDesc;
use common\models\OptionDesc;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OptionValueController implements the CRUD actions for OptionValue model.
 */
class OptionValueController extends Controller
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
  public function actionIndex($option_id)
  {
    $groupDescModel = $this->findGroupModel($option_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = OptionValue::find()
      ->alias('ov')
      ->select([
        'ov.*', 
        'ovd.*',
      ])
      ->leftJoin('oc_option_value_description ovd', '(ovd.option_value_id = ov.id AND ovd.language_id = "' . \Yii::$app->language . '")')
      ->where(['ov.option_id' => $option_id]);

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
      'option_id' => $option_id,
    ]);
  }

  /**
   * Creates a new Filter model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($option_id)
  {
    $model = new OptionValue();
    $max_sort = OptionValue::find()
      ->select(['MAX(sort_order)'])
      ->scalar();

    $model->sort_order = $max_sort + 10;
    $model->option_id = $option_id;

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }


    return $this->render('create', [
      'model' => $model,
      'languages' => $languages,
      'option_id' => $option_id,
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
    if (($model = OptionValue::findOne($id)) !== null) {
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
    if (($option = Option::findOne($id)) !== null) {
      if (in_array($option->type, ['select', 'radio', 'checkbox'])) {
        if (($model = OptionDesc::findOne(['option_id' => $id, 'language_id' => \Yii::$app->language])) !== null) {
          return $model;
        }
      }
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
