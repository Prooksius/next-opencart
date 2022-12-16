<?php

namespace backend\controllers\catalog;

use app\models\CustomerGroup;
use app\models\Filter;
use app\models\Product;
use app\models\ProductDesc;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\ProductDiscount;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductDiscountController implements the CRUD actions for ProductDiscount model.
 */
class ProductDiscountController extends Controller
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
   * Lists all ProductSpecial models.
   * @return mixed
   */
  public function actionIndex($product_id)
  {
    $productDescModel = $this->findProductModel($product_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = ProductDiscount::find()
      ->alias('ps')
      ->select([
        'ps.*',
        'cgd.name AS customer_group'
      ])
      ->leftJoin('oc_customer_group_description cgd', 'cgd.customer_group_id = ps.customer_group_id AND cgd.language_id = "' . Yii::$app->language . '"')
      ->where(['product_id' => $product_id]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'priority',
          'quantity',
          'price',
          'date_start',
          'date_end',
          'customer_group',
        ],
        'defaultOrder'=> [
          'priority' => SORT_ASC,
          'price' => SORT_ASC
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
   * Creates a new ProductSpecial model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($product_id)
  {
    $model = new ProductDiscount();
    $model->product_id = $product_id;

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->goBack();
    }


    return $this->render('create', [
      'model' => $model,
      'product_id' => $product_id,
      'allGroups' => CustomerGroup::getAllGroups(),
    ]);
  }

  /**
   * Updates an existing ProductDiscount model.
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
      'allGroups' => CustomerGroup::getAllGroups(),
    ]);
  }

  /**
   * Deletes an existing ProductDiscount model.
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
   * Finds the ProductDiscount model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return ProductDiscount the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = ProductDiscount::findOne($id)) !== null) {
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
