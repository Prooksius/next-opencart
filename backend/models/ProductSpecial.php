<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_product_special".
 */
class ProductSpecial extends \yii\db\ActiveRecord
{

    public $customer_group;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oc_product_special';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_group_id', 'product_id'], 'required'],
            [['product_id', 'customer_group_id', 'priority', 'date_start', 'date_end'], 'integer'],
            [['price'], 'number'],
            [['datestart', 'dateend'], 'safe'],
        ];
    }

  public function getDatestart()
  {
    if ($this->date_start) {
      return date('d.m.Y H:i', $this->date_start);
    } else {
      return '';
    }
  }

  public function setDatestart($value)
  {
    if ($value) {
      $date_field = date_create_from_format('d.m.Y H:i', $value);
      $this->date_start = date_timestamp_get($date_field);
    } else {
      $this->date_start = 0;
    }
  }

  public function getDateend()
  {
    if ($this->date_end) {
      return date('d.m.Y H:i', $this->date_end);
    } else {
      return '';
    }
  }

  public function setDateend($value)
  {
    if ($value) {
      $date_field = date_create_from_format('d.m.Y H:i', $value);
      $this->date_end = date_timestamp_get($date_field);
    } else {
      $this->date_end = 0;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'product_id' => YII::t('product', 'Product'),
      'customer_group_id' => YII::t('customer', 'Customer group'),
      'customer_group' => YII::t('customer', 'Customer group'),
      'price' => YII::t('app', 'Price'),
      'priority' => YII::t('product', 'Priority'),
      'date_start' => YII::t('product', 'Start date'),
      'datestart' => YII::t('product', 'Start date'),
      'date_end' => YII::t('product', 'End date'),
      'dateend' => YII::t('product', 'End date'),
    ];
  }
}
