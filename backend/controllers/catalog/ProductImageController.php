<?php

namespace backend\controllers\catalog;

use app\models\Product;
use app\models\ProductDesc;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\ProductImage;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductImageController implements the CRUD actions for ProductImage model.
 */
class ProductImageController extends Controller
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
  public function actionIndex($product_id)
  {
    $productDescModel = $this->findProductModel($product_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = ProductImage::find()
      ->where(['product_id' => $product_id]);

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
      'product_name' => $productDescModel->name,
      'product_id' => $product_id,
    ]);
  }

  /**
   * Creates a new ProductImage model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($product_id)
  {
    $model = new ProductImage();
    $max_sort = ProductImage::find()
      ->select(['MAX(sort_order)'])
      ->where(['product_id' => $product_id])
      ->scalar();

    $model->sort_order = $max_sort + 10;
    $model->product_id = $product_id;

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }


    return $this->render('create', [
      'model' => $model,
      'languages' => $languages,
      'product_id' => $product_id,
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
    if (($model = ProductImage::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the ProductImage model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductImage the loaded model
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
