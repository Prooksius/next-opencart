<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 08.09.2021
 * Time: 15:08
 */

namespace frontend\controllers\api\v1;

use common\components\TradeSystemHelper;
use common\models\Customer;
use Exception;
use frontend\components\Helper;
use frontend\components\PublicApiController;
use frontend\models\Country;
use frontend\models\LanguageChoose;
use frontend\models\Settings;
use frontend\models\Translation;
use frontend\components\WebSocketServer;
use frontend\models\Order;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class CartController extends PublicApiController
{

  public function beforeAction($action)
  {

    Yii::$app->cart->setCustomer(Helper::cleanData(Yii::$app->request->post('customer', [])));
    Yii::$app->cart->setAddress(Helper::cleanData(Yii::$app->request->post('address', [])));

    Yii::$app->delivery->setCurrent(Helper::cleanData(Yii::$app->request->post('delivery', '')));
    Yii::$app->payment->setCurrent(Helper::cleanData(Yii::$app->request->post('payment', '')));

    return parent::beforeAction($action);
  }

  public function actionIndex()
  {
    try {
      if (!Yii::$app->shopConfig->getParam('session_id')) {
        throw new \Exception('session_id not specified');
      }

      $cart = Yii::$app->cart->getProducts();

      // Delivery Methods
      Yii::$app->delivery->calculate();

      // Totals
      $totalsArray = [
        'totals' => [],
        'total'  => $cart['total'],
        'count'  => $cart['count'],
        'shipping' => $cart['shipping'],
      ];

      // Cart totals
      Yii::$app->totals->calculate($totalsArray);

      // Payment Methods
      Yii::$app->payment->calculate((float)$totalsArray['total']);

      $cart['total'] = $totalsArray['total'];
      $cart['totals'] = $totalsArray['totals'];
      $cart['total_str'] = Yii::$app->currency->format((float)$totalsArray['total'], Yii::$app->currency->getCurrent());
      $cart['count'] = $totalsArray['count'];
      $cart['deliveries'] = Yii::$app->delivery->getMethods();
      $cart['current_delivery'] = Yii::$app->delivery->getCurrentFull();
      $cart['payments'] = Yii::$app->payment->getMethods();
      $cart['current_payment'] = Yii::$app->payment->getCurrentFull();

      return [
        'success' => 1,
        'cart' => $cart,
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }
 
  public function actionAddToCart()
  {
    $error = '';

    try {
      if (!Yii::$app->shopConfig->getParam('session_id')) {
        throw new \Exception('session_id not specified');
      }

      $product_id = Helper::cleanData(Yii::$app->request->post('product_id', 0));
      $quantity = Helper::cleanData(Yii::$app->request->post('quantity', 0));
      $option = Helper::cleanData(Yii::$app->request->post('option', []));

      $result = Yii::$app->cart->add($product_id, $quantity, $option);

      if ($result['result'] != 'success') {
        $error = Yii::t('app', 'Error adding to Cart');
        Yii::$app->response->setStatusCode(500);
      } else {

        // Delivery Methods
        Yii::$app->delivery->calculate();

        // Totals
        $totalsArray = [
          'totals' => [],
          'total'  => $result['cart']['total'],
          'count'  => $result['cart']['count'],
          'shipping' => $result['cart']['shipping'],
        ];

        // Cart totals
        Yii::$app->totals->calculate($totalsArray);

        // Payment Methods
        Yii::$app->payment->calculate((float)$totalsArray['total']);

        $result['cart']['total'] = $totalsArray['total'];
        $result['cart']['totals'] = $totalsArray['totals'];
        $result['cart']['total_str'] = Yii::$app->currency->format((float)$totalsArray['total'], Yii::$app->currency->getCurrent());
        $result['cart']['count'] = $totalsArray['count'];
        $result['cart']['deliveries'] = Yii::$app->delivery->getMethods();
        $result['cart']['current_delivery'] = Yii::$app->delivery->getCurrentFull();
        $result['cart']['payments'] = Yii::$app->payment->getMethods();
        $result['cart']['current_payment'] = Yii::$app->payment->getCurrentFull();

        return $result;
      }

    } catch (\Exception $e) {
      $error = $e->getMessage();
      Yii::$app->response->setStatusCode(500);
    }

    return [
      'result' => 'error',
      'error' => $error,
      'cart' => $result,
	    'option' => $option,
    ];
  }

  public function actionUpdateCart()
  {
    $error = '';

    try {
      if (!Yii::$app->shopConfig->getParam('session_id')) {
        throw new \Exception('session_id not specified');
      }

      $cart_id = Helper::cleanData(Yii::$app->request->post('cart_id', 0));
      $quantity = Helper::cleanData(Yii::$app->request->post('quantity', 0));

      $result = Yii::$app->cart->edit($cart_id, $quantity);

      if ($result['result'] != 'success') {
        $error = Yii::t('app', 'Error editing Cart');
      } else {

        // Delivery Methods
        Yii::$app->delivery->calculate();

        // Totals
        $totalsArray = [
          'totals' => [],
          'total'  => $result['cart']['total'],
          'count'  => $result['cart']['count'],
          'shipping' => $result['cart']['shipping'],
        ];

        // Cart totals
        Yii::$app->totals->calculate($totalsArray);

        // Payment Methods
        Yii::$app->payment->calculate((float)$totalsArray['total']);

        $result['cart']['total'] = $totalsArray['total'];
        $result['cart']['totals'] = $totalsArray['totals'];
        $result['cart']['total_str'] = Yii::$app->currency->format((float)$totalsArray['total'], Yii::$app->currency->getCurrent());
        $result['cart']['count'] = $totalsArray['count'];
        $result['cart']['deliveries'] = Yii::$app->delivery->getMethods();
        $result['cart']['current_delivery'] = Yii::$app->delivery->getCurrentFull();
        $result['cart']['payments'] = Yii::$app->payment->getMethods();
        $result['cart']['current_payment'] = Yii::$app->payment->getCurrentFull();

        return $result;
      }

    } catch (\Exception $e) {
      $error = $e->getMessage();
      Yii::$app->response->setStatusCode(500);
    }

    return [
      'result' => 'error',
      'error' => $error,
      'cart' => $result,
    ];
  }

  public function actionRemoveFromCart()
  {
    $error = '';

    try {
      if (!Yii::$app->shopConfig->getParam('session_id')) {
        throw new \Exception('session_id not specified');
      }

      $cart_id = Helper::cleanData(Yii::$app->request->post('cart_id', 0));

      $result = Yii::$app->cart->remove($cart_id);

      if ($result['result'] != 'success') {
        $error = Yii::t('app', 'Error deleting from Cart');
      } else {

        // Delivery Methods
        Yii::$app->delivery->calculate();

        // Totals
        $totalsArray = [
          'totals' => [],
          'total'  => $result['cart']['total'],
          'count'  => $result['cart']['count'],
          'shipping' => $result['cart']['shipping'],
        ];

        // Cart totals
        Yii::$app->totals->calculate($totalsArray);

        // Payment Methods
        Yii::$app->payment->calculate((float)$totalsArray['total']);

        $result['cart']['total'] = $totalsArray['total'];
        $result['cart']['totals'] = $totalsArray['totals'];
        $result['cart']['total_str'] = Yii::$app->currency->format((float)$totalsArray['total'], Yii::$app->currency->getCurrent());
        $result['cart']['count'] = $totalsArray['count'];
        $result['cart']['deliveries'] = Yii::$app->delivery->getMethods();
        $result['cart']['current_delivery'] = Yii::$app->delivery->getCurrentFull();
        $result['cart']['payments'] = Yii::$app->payment->getMethods();
        $result['cart']['current_payment'] = Yii::$app->payment->getCurrentFull();

        return $result;
      }

    } catch (\Exception $e) {
      $error = $e->getMessage();
      Yii::$app->response->setStatusCode(500);
    }

    return [
      'result' => 'error',
      'error' => $error,
      'cart' => $result,
    ];
  }

  public function actionClearCart()
  {
    $error = '';

    try {
      if (!Yii::$app->shopConfig->getParam('session_id')) {
        throw new \Exception('session_id not specified');
      }

      $result = Yii::$app->cart->clear();

      // Delivery Methods
      Yii::$app->delivery->calculate();

      // Totals
      $totalsArray = [
        'totals' => [],
        'total'  => $result['cart']['total'],
        'count'  => $result['cart']['count'],
        'shipping' => $result['cart']['shipping'],
      ];

      // Cart totals
      Yii::$app->totals->calculate($totalsArray);

      // Payment Methods
      Yii::$app->payment->calculate((float)$totalsArray['total']);

      $result['cart']['total'] = $totalsArray['total'];
      $result['cart']['totals'] = $totalsArray['totals'];
      $result['cart']['total_str'] = Yii::$app->currency->format((float)$totalsArray['total'], Yii::$app->currency->getCurrent());
      $result['cart']['count'] = $totalsArray['count'];
      $result['cart']['deliveries'] = Yii::$app->delivery->getMethods();
      $result['cart']['current_delivery'] = Yii::$app->delivery->getCurrentFull();
      $result['cart']['payments'] = Yii::$app->payment->getMethods();
      $result['cart']['current_payment'] = Yii::$app->payment->getCurrentFull();

      return $result;

    } catch (\Exception $e) {
      $error = $e->getMessage();
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'result' => 'error',
      'error' => $error,
      'cart' => $result,
    ];
  }

  public function actionCreateOrder()
  {
    $error = '';

    try {
      if (!Yii::$app->shopConfig->getParam('session_id')) {
        throw new \Exception('session_id not specified');
      }

      $comment = Helper::cleanData(Yii::$app->request->post('comment', ''));

      $cart = Yii::$app->cart->getProducts();

      // Delivery Methods
      Yii::$app->delivery->calculate();

      // Totals
      $totalsArray = [
        'totals' => [],
        'total'  => $cart['total'],
        'count'  => $cart['count'],
        'shipping' => $cart['shipping'],
      ];

      // Cart totals
      Yii::$app->totals->calculate($totalsArray);

      // Payment Methods
      Yii::$app->payment->calculate((float)$totalsArray['total']);

      $order_data = Order::addOrder($cart, $totalsArray, $comment);

      $payment_data = Yii::$app->payment->processPayment($order_data, $totalsArray);

      $cart = Yii::$app->cart->getProducts();

      // Delivery Methods
      Yii::$app->delivery->calculate();

      // Totals
      $totalsArray = [
        'totals' => [],
        'total'  => $cart['total'],
        'count'  => $cart['count'],
        'shipping' => $cart['shipping'],
      ];

      // Cart totals
      Yii::$app->totals->calculate($totalsArray);

      // Payment Methods
      Yii::$app->payment->calculate((float)$totalsArray['total']);

      $cart['total'] = $totalsArray['total'];
      $cart['totals'] = $totalsArray['totals'];
      $cart['total_str'] = Yii::$app->currency->format((float)$totalsArray['total'], Yii::$app->currency->getCurrent());
      $cart['count'] = $totalsArray['count'];
      $cart['deliveries'] = Yii::$app->delivery->getMethods();
      $cart['current_delivery'] = Yii::$app->delivery->getCurrentFull();
      $cart['payments'] = Yii::$app->payment->getMethods();
      $cart['current_payment'] = Yii::$app->payment->getCurrentFull();

      return [
        'success' => 1,
        'order_id' => $order_data['order_id'],
        'payment_data' => $payment_data,
        'cart' => $cart,
      ];
      
    } catch (\Exception $e) {
      $error = $e->getMessage();
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'result' => 'error',
      'error' => $error,
      'cart' => $cart,
    ];
  }

  public function actionPaymentCallbackLocal()
  {
    $method = Helper::cleanData(Yii::$app->request->get('method', ''));

    $method = Inflector::camelize($method);

    $moduleClass = 'frontend\extensions\payments\\' . $method;

    if (!class_exists($moduleClass)) {

      Yii::$app->response->setStatusCode(500);
      return [
        'result' => 'error',
        'error' => 'link error',
      ];
    }

    $callbackResults = $moduleClass::paymentCallback();

    if (!isset($callbackResults['success']) || !$callbackResults['success']) {
      Yii::$app->response->setStatusCode(500);
    }

    return $callbackResults;

  }

  public function actionPaymentCallback()
  {
    $method = Helper::cleanData(Yii::$app->request->get('method', ''));

    $method = Inflector::camelize($method);

    //echo $method . '<br />';

    $moduleClass = 'frontend\extensions\payments\\' . $method;

    if (!class_exists($moduleClass)) {
      die('link error');
    }

    $moduleClass::paymentCallback();

    exit;

  }
}