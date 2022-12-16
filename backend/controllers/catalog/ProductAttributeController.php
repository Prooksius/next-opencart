<?php

namespace backend\controllers\catalog;

use app\models\Attribute;
use app\models\Product;
use app\models\ProductDesc;
use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\ProductAttribute;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductAttributeController implements the CRUD actions for ProductAttribute model.
 */
class ProductAttributeController extends Controller
{

  public $attributeName;

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
   * Lists all ProductAttribute models.
   * @return mixed
   */
  public function actionIndex($product_id)
  {
    $productDescModel = $this->findProductModel($product_id);

    Yii::$app->user->returnUrl = Url::current([], true);

    $query = ProductAttribute::find()
      ->alias('pa')
      ->select([
        'pa.*',
        'ad.name AS attributeName'
      ])
      ->leftJoin('oc_attribute a', 'a.id = pa.attribute_id')
      ->leftJoin('oc_attribute_description ad', 'ad.attribute_id = a.id AND ad.language_id = "' . Yii::$app->language . '"')
      ->where(['pa.product_id' => $product_id, 'pa.language_id' => Yii::$app->language]);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'attributeName',
          'text',
          'alias',
        ],
        'defaultOrder'=>[
          'attributeName' => SORT_ASC
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
   * Creates a new ProductAttribute model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate($product_id)
  {
    $model = new ProductAttribute();
    $model->product_id = $product_id;
    $model->language_id = Yii::$app->language;

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {

      foreach ($languages as $language) {
        $new = new ProductAttribute();
        $new->product_id = $model->product_id;
        $new->attribute_id = $model->attribute_id;
        $new->language_id = $language->locale;
        $new->alias = $model->alias;
        $new->text = isset($model->_textsarr[$language->locale]) ? $model->_textsarr[$language->locale] : $model->alias;
        $new->save(); 
      }

      return $this->goBack();
    }


    return $this->render('create', [
      'model' => $model,
      'languages' => $languages,
      'product_id' => $product_id,
      'attributesList' => Attribute::getAllAttributes(),
    ]);
  }

  /**
   * Updates an existing ProductAttribute model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($attribute_id, $product_id, $language_id)
  {
    $model = $this->findModel(['attribute_id' => $attribute_id, 'product_id' => $product_id , 'language_id' => $language_id]);

    $languages = Language::find()->all();

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {

      ProductAttribute::getDb()
        ->createCommand()
        ->delete('oc_product_attribute', ['product_id' => $model->product_id, 'attribute_id' => $attribute_id])
        ->execute();

      foreach ($languages as $language) {
        $new = new ProductAttribute();
        $new->product_id = $model->product_id;
        $new->attribute_id = $model->attribute_id;
        $new->language_id = $language->locale;
        $new->alias = $model->alias;
        $new->text = isset($model->_textsarr[$language->locale]) ? $model->_textsarr[$language->locale] : $model->alias;
        $new->save(); 
      }

      return $this->goBack();
    }

    return $this->render('update', [
      'model' => $model,
      'languages' => $languages,
      'attributesList' => Attribute::getAllAttributes(),
    ]);
  }

  /**
   * Deletes an existing ProductAttribute model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($attribute_id, $product_id, $language_id)
  {
    ProductAttribute::getDb()
      ->createCommand()
      ->delete('oc_product_attribute', ['product_id' => $product_id, 'attribute_id' => $attribute_id])
      ->execute();

    return $this->redirect(['index']);
  }

  /**
   * Finds the ProductAttribute model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param array $params
   * @return ProductAttribute the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($params)
  {
    if (($model = ProductAttribute::findOne($params)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  /**
   * Finds the ProductDesc model based on its primary key value.
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
