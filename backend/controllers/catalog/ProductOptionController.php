<?php

namespace backend\controllers\catalog;

use app\models\Filter;
use app\models\Option;
use app\models\Product;
use app\models\ProductDesc;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\ProductOption;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductOptionController implements the CRUD actions for ProductOption model.
 */
class ProductOptionController extends Controller
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
   * Lists all ProductFilter models.
   * @return mixed
   */
  public function actionIndex($product_id)
  {
    $productDescModel = $this->findProductModel($product_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = ProductOption::find()
      ->alias('po')
      ->select([
        'po.*',
        'o.type AS optionType',
        'od.name AS optionName'
      ])
      ->leftJoin('oc_option o', 'o.id = po.option_id')
      ->leftJoin('oc_option_description od', 'od.option_id = o.id AND od.language_id = "' . Yii::$app->language . '"')
      ->where(['product_id' => $product_id]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'optionName',
        ],
        'defaultOrder'=>[
          'optionName' => SORT_ASC
        ],
      ],
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'product_name' => $productDescModel->name,
      'product_id' => $product_id,
    ]);
  }

  /**
   * Creates a new ProductOption model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($product_id)
  {
    $model = new ProductOption();
    $model->scenario = 'create';
    $model->product_id = $product_id;

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }


    return $this->render('create', [
      'model' => $model,
      'product_id' => $product_id,
      'allOptions' => Option::getAllOptions(),
    ]);
  }

  /**
   * Updates an existing ProductOption model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
    $optionModel = $this->findOptionModel($model->option_id);

    $model->scenario = 'update';

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
      'allOptions' => Option::getAllOptions(),
      'optionType' => $optionModel->type,
    ]);
  }

  /**
   * Deletes an existing ProductOption model.
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
   * Finds the ProductOption model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductOption the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = ProductOption::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the Option model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Option the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findOptionModel($id)
  {
    if (($model = Option::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the ProductFilter model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductDesc the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findProductModel($id)
  {
    if ((Product::findOne($id)) !== null) {
      if (($model = ProductDesc::findOne(['product_id' => $id, 'language_id' => \Yii::$app->language])) !== null) {
        return $model;
      }
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
