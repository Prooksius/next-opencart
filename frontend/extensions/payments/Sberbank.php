<?php

namespace frontend\extensions\payments;

use common\components\Rbs;
use common\models\Order;
use common\models\OrderProduct;
use Yii;
use common\models\OrderHistory;

class Sberbank
{

  private static $rbs;

  /**
   * Инициализация библиотеки RBS
   */
  private static function initializeRbs(array $settings)
  {
      self::$rbs = new Rbs();
      self::$rbs->login = $settings['rbs_merchantLogin'];
      self::$rbs->password = $settings['rbs_merchantPassword'];
      self::$rbs->stage = $settings['rbs_stage'];
      self::$rbs->mode = $settings['rbs_mode'];
      self::$rbs->logging = $settings['rbs_logging'];
      self::$rbs->currency = $settings['rbs_currency'];
      self::$rbs->taxSystem = $settings['rbs_taxSystem'];
      self::$rbs->taxType = $settings['rbs_taxSystem'];
      self::$rbs->ofd_status = $settings['rbs_ofd_status'];

      self::$rbs->ffd_version = $settings['rbs_ffdVersion'];
      self::$rbs->paymentMethodType = $settings['rbs_paymentMethodType'];
      self::$rbs->deliveryPaymentMethodType = $settings['rbs_deliveryPaymentMethodType'];
      self::$rbs->paymentObjectType = $settings['rbs_paymentObjectType'];

      $c_locale = substr(Yii::$app->language, 0, 2);
      self::$rbs->language = ($c_locale == "ru" || $c_locale == "en") ? $c_locale : "ru";
  }

  public static function getMethod(array $settings, array $address, float $total) {

    $method_data = [
      'code'       => 'Sberbank',
      'title'      => $settings['title'][Yii::$app->language],
      'terms'      => '',
    ];

		return $method_data;
	}

  public static function payment(array $settings, array $order_data, array $totals) {

    self::initializeRbs($settings);
    
    $baseUrl = rtrim(str_replace('admin/', '',  Yii::$app->urlManager->createAbsoluteUrl('/')), '/') . '/';

    $return_url = $baseUrl . '/payment-process?method=sberbank';
    $fail_url = $baseUrl . '/payment-fail';

    $order_number = (int)$order_data['order_id'];
    $amount = round($order_data['total'], 2) * 100;

    // here we will collect data for orderBundle
    $orderBundle = [];

    $orderBundle['customerDetails'] = array(
        'email' => $order_data['email'],
        'phone' => preg_match('/[7]\d{9}/', $order_data['phone']) ? $order_data['phone'] : ''
    );

    foreach ($order_data['products'] as $product) {
      $product_taxSum = 0;
      $product_amount = ($product['price'] + $product_taxSum) * $product['quantity'];

      $sber_product = [
          'positionId' => $product['order_product_id'],
          'name' => $product['name'],
          'quantity' => [
            'value' => (int)$product['quantity'],
            'measure' => 'шт',
          ],
          'itemAmount' => (float)$product_amount * 100,
          'itemCode' => $product['product_id'] . "_" . $product['order_product_id'], //fix by PLUG-1740, PLUG-2620
          'tax' => [
              'taxType' => 1,
              'taxSum' => 0
          ],
          'itemPrice' => ((float)$product['price'] + $product_taxSum) * 100,
      ];

      // FFD 1.05 added
      if ($settings['ffdVersion'] == 'v105') {

          $attributes = [];
          $attributes[] = [
              "name" => "paymentMethod",
              "value" => $settings['paymentMethodType']
          ];
          $attributes[] = [
              "name" => "paymentObject",
              "value" => $settings['paymentObjectType']
          ];

          $sber_product['itemAttributes']['attributes'] = $attributes;
      }

      $orderBundle['cartItems']['items'][] = $sber_product;

    }

    $delivery_method = Yii::$app->delivery->getCurrent();

    // DELIVERY
    if (isset($delivery_method['cost']) && $delivery_method['cost'] > 0) {

        $delivery['positionId'] = 'delivery';
        $delivery['name'] = $delivery_method['title'];
        $delivery['itemAmount'] = $delivery_method['cost'] * 100;
        $delivery['quantity']['value'] = 1;
        //todo fix piece
        $delivery['quantity']['measure'] = 'шт';
        $delivery['itemCode'] = $delivery_method['code'];
        $delivery['tax']['taxType'] = $settings['rbs_taxType'];
        $delivery['tax']['taxSum'] = 0;
        $delivery['itemPrice'] = $delivery_method['cost'] * 100;

        // FFD 1.05 added
        if (self::$rbs->getFFDVersion() == 'v105') {

            $attributes = array();
            $attributes[] = array(
                "name" => "paymentMethod",
                "value" => self::$rbs->getPaymentMethodType(true)
            );
            $attributes[] = array(
                "name" => "paymentObject",
                "value" => 4
            );

            $delivery['itemAttributes']['attributes'] = $attributes;
        }

        $orderBundle['cartItems']['items'][] = $delivery;
    }

    // DISCOUNT CALCULATE
    $discount = self::$rbs->discountHelper->discoverDiscount($amount, $orderBundle['cartItems']['items']);
    if ($discount > 0) {
        self::$rbs->discountHelper->setOrderDiscount($discount);
        $recalculatedPositions = self::$rbs->discountHelper->normalizeItems($orderBundle['cartItems']['items']);
        $recalculatedAmount = self::$rbs->discountHelper->getResultAmount();
        $orderBundle['cartItems']['items'] = $recalculatedPositions;
    }

    $currency_code = self::$rbs->currency_code2num[$order_data['currency_code']];

    $response = self::$rbs->register_order($order_number, $amount, $return_url, $fail_url, $currency_code, $orderBundle);

    if (isset($response['errorCode'])) {
      return [
        'success' => 0,
        'error' => $response['errorMessage'],
        'errorCode' => $response['errorCode'],
      ];
    }

    return [
      'success' => 1,
      'url' => $response['formUrl']
    ];
  }

  public static function paymentCallback()
  {

    $method = Yii::$app->payment->methodByCode('Sberbank');
    if ($method) {
      $settings = $method['settings'];

      if (Yii::$app->request->get('orderId', '')) {
          $order_id = Yii::$app->request->get('orderId');
      } else {
        return [
          'success' => 0,
          'error' => 'Illegal Access',
        ];
      }

      self::initializeRbs($settings);
      $response = self::$rbs->get_order_status($order_id);

      $ex = explode("_", $response['orderNumber']);
      $order_number = $ex[0];

      $order = Order::findOne($order_number);

      if ($order instanceof Order) {

        $order_data = $order->attributes;
        $order_data['order_id'] = $order_number;
        $order_data['products'] = OrderProduct::productsList($order_number);

        $order_status_id = (int)$settings['order_status'];

        OrderHistory::addHistory($order_data, $order_status_id, $order_data['comment']);

        Yii::$app->cart->clear();
        
        return [
          'success' => 1,
          'url' => '/payment-success',
        ];
      }

      return [
        'success' => 0,
        'error' => 'Order not found',
      ];
    }

    return [
      'success' => 0,
      'error' => 'Payment method not found',
    ];
  }
}