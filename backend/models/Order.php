<?php

namespace app\models;

use common\models\CustomerGroupDesc;
use common\models\Order as ModelsOrder;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order".
 */
class Order extends ModelsOrder
{

  public $customer;
  public $status;
  public $products;
  public $status_color;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    $rules = parent::rules();
    return $rules;
  }

  public function getStatusesList()
  {
    return OrderStatus::getAllStatuses();
  }

  public function getCustomerGroup()
  {
    $group = CustomerGroupDesc::findOne(['customer_group_id' => $this->customer_group_id, 'language_id' => Yii::$app->language]);
    if ($group instanceof CustomerGroupDesc) {
      return $group->name;
    }
    return YII::t('app', 'Not set');
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'created_at' => YII::t('order', 'Create Date'),
      'created' => YII::t('order', 'Create Date'),
      'customer_id' => YII::t('order', 'Customer'),
      'customer' => YII::t('order', 'Customer'),
      'customer_group_id' => YII::t('order', 'Customer Group'),
      'order_status_id' => YII::t('order', 'Order Status'),
      'status' => YII::t('order', 'Order Status'),
      'fullname' => YII::t('order', 'Full Name'),
      'email' => YII::t('order', 'Email'),
      'phone' => YII::t('order', 'Phone'),
      'address_country' => YII::t('order', 'Country'),
      'address_region' => YII::t('order', 'Region'),
      'address_city' => YII::t('order', 'City'),
      'address_street' => YII::t('order', 'Street'),
      'address_postcode' => YII::t('order', 'Postal code'),
      'address_house' => YII::t('order', 'House'),
      'address_apartment' => YII::t('order', 'Apartnemt'),
      'language_id' => YII::t('order', 'Language'),
      'total' => YII::t('order', 'Total'),
      'currency_code' => YII::t('order', 'Currency Code'),
      'currency_value' => YII::t('order', 'Currency value'),
      'delivery_method' => YII::t('order', 'Delivery method'),
      'delivery_code' => YII::t('order', 'Delivery code'),
      'payment_method' => YII::t('order', 'Payment method'),
      'payment_code' => YII::t('order', 'Payment code'),
      'comment' => YII::t('order', 'Comment'),
      'user_agent' => YII::t('order', 'User agent'),
      'ip' => YII::t('order', 'IP'),
      'products' => YII::t('order', 'Products'),
    ];
  }
}
