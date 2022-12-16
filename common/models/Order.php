<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_order".
 */
class Order extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['fullname', 'email', 'currency_code', 'language_id', 'address_country_code', 'address_country'], 'required'],
      [['customer_id', 'customer_group_id', 'order_status_id'], 'integer'],
      [['fullname', 'address_region', 'user_agent'], 'string', 'max' => 255],
      [['email', 'address_country', 'address_city', 'address_street', 'delivery_method', 'delivery_code', 'payment_method', 'payment_code'], 'string', 'max' => 128],
      [['phone'], 'string', 'max' => 50],
      [['currency_code'], 'string', 'max' => 3],
      [['ip'], 'string', 'max' => 40],
      [['address_country_code', 'address_region_code', 'language_id'], 'string', 'max' => 10],
      [['address_postcode', 'address_house', 'address_apartment'], 'string', 'max' => 20],
      [['comment'], 'string'],
      [['total', 'currency_value', 'address_latitude', 'address_longitude'], 'number'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
    ];
  }

  public function afterDelete()
  {
    static::getDb()
      ->createCommand()
      ->delete('oc_order_product', ['order_id' => $this->id])
      ->execute();

    static::getDb()
      ->createCommand()
      ->delete('oc_order_option', ['order_id' => $this->id])
      ->execute();

    static::getDb()
      ->createCommand()
      ->delete('oc_order_total', ['order_id' => $this->id])
      ->execute();

    static::getDb()
      ->createCommand()
      ->delete('oc_order_history', ['order_id' => $this->id])
      ->execute();

  }

}
