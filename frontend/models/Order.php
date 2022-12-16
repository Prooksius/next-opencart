<?php

namespace frontend\models;

use common\models\Order as ModelsOrder;
use Yii;

/**
 * This is the model class for table "order".
 */
class Order extends ModelsOrder
{

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    $rules = parent::rules();
    return $rules;
  }

  public static function addOrder(array $cart, array $totals, string $comment)
  {
    $current_delivery = Yii::$app->delivery->getCurrent();
    $current_payment = Yii::$app->payment->getCurrent();

    $customer = Yii::$app->cart->getCustomer();
    $address = Yii::$app->cart->getAddress();

    $order_data = [
      'fullname' => $customer['name'],
      'email' => $customer['email'],
      'phone' => $customer['phone'],
      'address_country_code' => $address['countryCode'],
      'address_country' => $address['country'],
      'address_region_code' => $address['regionCode'],
      'address_region' => $address['region'],
      'address_city' => $address['city'],
      'address_postcode' => $address['postalCode'],
      'address_street' => $address['street'],
      'address_house' => $address['house'],
      'address_apartment' => '',
      'address_latitude' => $address['geo_lat'],
      'address_longitude' => $address['geo_lon'],
      'delivery_method' => $current_delivery['title'],
      'delivery_code' => $current_delivery['code'],
      'payment_method' => $current_payment['title'],
      'payment_code' => $current_payment['code'],
      'comment' => $comment,
      'total' => (float)$totals['total'],
      'order_status_id' => 0,
      'language_id' => Yii::$app->language,
      'currency_code' => Yii::$app->currency->getCurrent(),
      'currency_value' => Yii::$app->currency->getValue(Yii::$app->currency->getCurrent()),
      'ip' => Yii::$app->request->userIP,
      'user_agent' => Yii::$app->request->userAgent,
      'products' => [],
      'totals' => [],
    ];

    $new_order = new self();
    $new_order->attributes = $order_data;
    $new_order->save();
    $new_order->refresh();

    $order_id = (int)$new_order->id;

    $order_data['order_id'] = $order_id;

    foreach ($cart['products'] as $product) {

      $product_data = [
        'order_id'   => $order_id,
        'product_id' => $product['product_id'],
        'name'       => $product['name'],
        'model'      => $product['model'],
//        'download'   => $product['download'],
        'quantity'   => $product['quantity'],
//        'subtract'   => $product['subtract'],
        'price'      => $product['price'],
        'total'      => $product['total'],
      ];

      $new_product = new OrderProduct();
      $new_product->attributes = $product_data;
      $new_product->save();
      $new_product->refresh();

      $order_product_id = (int)$new_product->id;

      $product_data['subtract'] = $product['subtract'];
      $product_data['order_product_id'] = $order_product_id;
      $product_data['options'] = [];

      foreach ($product['option'] as $option) {
        $option_data = [
          'order_id'                => $order_id,
          'order_product_id'        => $order_product_id,
          'product_option_id'       => $option['product_option_id'],
          'product_option_value_id' => $option['product_option_value_id'],
//          'option_id'               => $option['option_id'],
          'option_value_id'         => $option['option_value_id'],
          'name'                    => $option['name'],
          'value'                   => $option['value'],
          'type'                    => $option['type']
        ];

        $new_option = new OrderOption();
        $new_option->attributes = $option_data;
        $new_option->save();

        $product_data['options'][] = $option_data;

      }

      $order_data['products'][] = $product_data;

    }

    foreach ($totals['totals'] as $key => $total_item) {
      $total_attrs = [
        'order_id'   => $order_id,
        'code'       => $total_item['code'],
        'title'      => $total_item['title'],
        'value'      => $total_item['value'],
        'sort_order' => $key,
      ];

      $new_total = new OrderTotal();
      $new_total->attributes = $total_attrs;
      $new_total->save();

      $order_data['totals'][] = $total_attrs;

    }

    return $order_data;
  }
}
