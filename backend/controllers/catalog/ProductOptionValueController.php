<?php

namespace backend\controllers\catalog;

use app\models\Option;
use app\models\OptionValue;
use app\models\Product;
use app\models\ProductDesc;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\ProductOption;
use app\models\ProductOptionValue;
use common\models\OptionDesc;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductOptionValueController implements the CRUD actions for ProductOptionValue model.
 */
class ProductOptionValueController extends Controller
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
  public function actionIndex($product_option_id)
  {
    $productOptionModel = $this->findProductOptionModel($product_option_id);
    $productDescModel = $this->findProductModel($productOptionModel->product_id);
    $optionDescModel = $this->findOptionModel($productOptionModel->option_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = ProductOptionValue::find()
      ->alias('pov')
      ->select([
        'pov.*',
//        'ov.sort_order AS sort_order',
        'ovd.name AS optionValueName',
      ])
      ->leftJoin('oc_option_value ov', 'ov.id = pov.option_value_id')
      ->leftJoin('oc_option_value_description ovd', 'ovd.option_value_id = ov.id AND ovd.language_id = "' . Yii::$app->language . '"')
      ->where(['product_option_id' => $product_option_id]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'optionValueName',
//          'sort_order',
        ],
        'defaultOrder'=>[
          'id' => SORT_ASC
        ],
      ],
    ]);

    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'product_name' => $productDescModel->name,
      'option_name' => $optionDescModel->name,
      'product_id' => $productOptionModel->product_id,
      'product_option_id' => $product_option_id,
    ]);
  }

  /**
   * Creates a new ProductOptionValue model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($product_option_id)
  {
    $productOptionModel = $this->findProductOptionModel($product_option_id);

    $model = new ProductOptionValue();
    $model->product_option_id = $product_option_id;
    $model->product_id = $productOptionModel->product_id;
    $model->option_id = $productOptionModel->option_id;

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }

    return $this->render('create', [
      'model' => $model,
      'product_option_id' => $product_option_id,
      'product_id' => $productOptionModel->product_id,
    ]);
  }

  /**
   * Updates an existing ProductOptionValue model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    $productOptionModel = $this->findProductOptionModel($model->product_option_id);

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
      'product_id' => $productOptionModel->product_id,
    ]);
  }

  /**
   * Deletes an existing ProductOptionValue model.
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
   * Finds the ProductOptionValue model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductOptionValue the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = ProductOptionValue::findOne($id)) !== null) {
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

  /**
   * Finds the ProductOption model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductDesc the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findProductOptionModel($id)
  {
    if (($model = ProductOption::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the ProductOption model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductDesc the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findOptionModel($id)
  {
    if ((Option::findOne($id)) !== null) {
      if (($model = OptionDesc::findOne(['option_id' => $id, 'language_id' => \Yii::$app->language])) !== null) {
        return $model;
      }
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
