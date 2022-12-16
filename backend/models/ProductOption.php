<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "oc_product_image".
 */
class ProductOption extends \yii\db\ActiveRecord
{
  
  public $optionName;
  public $optionType;
  
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_option';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'option_id'], 'required'],
      [['product_id', 'option_id', 'required'], 'integer'],
      [['value'], 'string'],
    ];
  }

  public function scenarios()
  {
    return [
      'create' => ['product_id', 'option_id', 'value', 'required'],
      'update' => ['product_id', 'value', 'required']
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'option_id' => YII::t('option', 'Option'),
      'optionType' => YII::t('option', 'Option type'),
      'optionName' => YII::t('option', 'Option'),
      'product_id' => YII::t('product', 'Product'),
      'value' => YII::t('app', 'Value'),
      'required' => YII::t('app', 'Required'),
    ];
  }
}
