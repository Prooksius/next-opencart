<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_product_filter".
 */
class ProductFilter extends \yii\db\ActiveRecord
{

  public $filter;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
      return 'oc_product_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'filter_id'], 'required'],
      [['product_id', 'filter_id'], 'integer'],
      ['filter_id', 'unique', 'targetAttribute' => ['product_id', 'filter_id'], 
        'message' => YII::t('product', 'This filter has already been added to the product')],
    ];
}

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'product_id' => YII::t('product', 'Product'),
      'filter_id' => YII::t('product', 'Product filter'),
      'filter' => YII::t('product', 'Product filter'),
    ];
  }
}
