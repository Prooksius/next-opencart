<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_order_product".
 */
class OrderProduct extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order_product';
  }

  public static function productsList(int $id)
  {
    $products = self::find()
      ->where(['order_id' => $id])
      ->asArray()
      ->all();

    foreach ($products as &$product) {
      $options = OrderOption::find()
        ->where(['order_id' => $id, 'order_product_id' => $product['id']])
        ->asArray()
        ->all();

      $product['options'] = $options;
    }

    return $products;
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'order_id', 'name', 'quantity'], 'required'],
      [['order_id', 'product_id', 'quantity'], 'integer'],
      [['name'], 'string', 'max' => 255],
      [['model'], 'string', 'max' => 64],
      [['price', 'total'], 'number'],
      [['price', 'total'], 'default', 'value' => 0],
    ];
  }

}
