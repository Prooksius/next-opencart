<?php

namespace common\models;

/**
 * This is the model class for table "trip".
 */
class Product extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order', 'quantity', 'stock_status_id', 'manufacturer_id', 'shipping', 'points', 
        'date_available', 'weight_class_id', 'length_class_id', 'subtract', 'minimum', 'status', 'viewed'], 'integer'],
      [['sku', 'mpn', 'upc', 'ean', 'jan', 'isbn', 'location', 'alias', 'image'], 'string'],
      [['price', 'weight', 'length', 'width', 'height'], 'number'],
    ];
  }
}
