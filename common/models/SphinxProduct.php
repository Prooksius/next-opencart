<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "trip".
 */
class SphinxProduct extends \yii\db\ActiveRecord
{

  public static function getDb() {
    return Yii::$app->get('db2');
  }
  
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'next_products';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'category_id', 'manufacturer_id'], 'integer'],
      [['product_name', 'product_description', 'category_name', 'manufacturer_name'], 'string'],
    ];
  }
}
