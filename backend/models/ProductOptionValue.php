<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "oc_product_option_value".
 */
class ProductOptionValue extends \yii\db\ActiveRecord
{
  
  public $optionValueName;
  public $sort_order;
  
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_option_value';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_option_id', 'product_id', 'option_id', 'option_value_id'], 'required'],
      [['product_option_id', 'product_id', 'option_id', 'option_value_id', 'quantity', 'subtract', 'points'], 'integer'],
      [['price', 'weight'], 'number'],
      [['price', 'weight', 'points', 'quantity'], 'default', 'value' => 0],
      [['price_prefix', 'points_prefix', 'weight_prefix'], 'string', 'max' => 1],
      [['image'], 'string', 'max' => 255],
    ];
  }

  public function getActionsList()
  {
    return ['+' => '+', '-' => '-'];
  }

  public function getAllOptionValues()
  {
    return OptionValue::getAllOptionValues($this->option_id);
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'product_option_id' => YII::t('option', 'Product option'),
      'option_id' => YII::t('option', 'Option'),
      'option_value_id' => YII::t('option', 'Option value'),
      'optionValueName' => YII::t('option', 'Option value'),
      'product_id' => YII::t('product', 'Product'),
      'image' => YII::t('app', 'Image'),
      'price' => YII::t('app', 'Price'),
      'weight' => YII::t('app', 'Weight'),
      'price_prefix' => YII::t('app', 'Price prefix'),
      'points_prefix' => YII::t('app', 'Points prefix'),
      'weight_prefix' => YII::t('app', 'Weight prefix'),
      'quantity' => YII::t('app', 'Quantity'),
      'subtract' => YII::t('product', 'Subtract'),
      'points' => YII::t('product', 'Points'),
    ];
  }
}
