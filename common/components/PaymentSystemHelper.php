<?php

namespace common\components;

use common\models\Customer;
use Exception;
use Yii;
use yii\helpers\FileHelper;

class PaymentSystemHelper
{

  const MODE_TEST = 0;
  const MODE_REAL = 1;

  private $get_token_href = [
    self::MODE_TEST => 'https://gw-test.b2binpay.com/api/login',
    self::MODE_REAL => 'https://gw.b2binpay.com/api/login',
  ];

  private $payment_href = [
    self::MODE_TEST => 'https://cr-test.b2binpay.com/api/v1/pay/bills',
    self::MODE_REAL => 'https://omni.b2binpay.com/api/v1/pay/bills',
  ];

  public static function log($title, $message = '') {
    file_put_contents(
      FileHelper::normalizePath(YII::getAlias('@root') . '/payment_system.log'), 
      date('d.m.Y H:i:s') . ' - ' . $title . ' - ' . $message . "\r\n", 
      FILE_APPEND
    );
  }

  public static function getAccessToken($merchant)
  {
    $auth = base64_encode($merchant['key'] . ':' . $merchant['secret']);
    $url = self::$get_token_href[(int)$merchant['mode']];

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array( "Authorization: Basic " . $auth ),
    ));

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $err = curl_error($curl);
    
    curl_close($curl);

    if ($err) {
  
      self::log('Ошибка получения токена', $err);
  
      return false;
    } else {
      if (in_array($info['http_code'], array(401,403,404))) {
  
        self::log('Ошибка получения токена', $info['http_code']);
  
        return false;
      }
      
      $result = json_decode($response, true);

      if (!empty($result['access_token'])) {

        self::log('Получение токена', 'Успешно');

        return $result['access_token'];
      }

      self::log('Ошибка получения токена', 'Результат отсутствует');
    }    

    return false;
  }

  public static function createPayment($merchant, int $customer_id, float $sum)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $auth = base64_encode($merchant['key'] . ':' . $merchant['secret']);
      $url = self::$get_token_href[(int)$merchant['mode']];

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://gw-test.b2binpay.com/api/v1/pay/bills",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "wallet=2&amount=100000&lifetime=0&pow=8",
        CURLOPT_HTTPHEADER => array(
          "authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJCMkJDcnlwdG9QYXkiLCJzdWIiOiIwZmJhODBiZjQwZmQ5MzEiLCJpYXQiOjE1MTE5MDY3MjIsImV4cCI6MTUyMDU0NjcyMn0.-AaTOAnhne-u8ioWMJrTozph_25mQhSTQGS2cx3tx6w",
          "content-type: application/x-www-form-urlencoded"
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }

    }

    return $result;
  }
}