<?php

namespace common\components;

use common\models\Customer;
use common\models\Strategy;
use Exception;
use common\models\CustomerStrategy;
use common\models\StrategyDesc;
use frontend\models\Translation;
use Yii;
use yii\helpers\FileHelper;
use himiklab\thumbnail\EasyThumbnailImage;

class TradeSystemHelper
{

  private static $token = '4c36506dfd337a2444832984fd526664';
  private static $serverLink = 'https://invest-api.com/v2/copy-trade/';

  public static function log($title, $message = '') {
    file_put_contents(
      FileHelper::normalizePath(YII::getAlias('@root') . '/trade_system.log'), 
      date('d.m.Y H:i:s') . ' - ' . $title . ' - ' . $message . "\r\n", 
      FILE_APPEND
    );
  }

  private static function curlGetRequest($url, $options = array()) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);

    if (curl_error($ch)) {
      self::log('Ошибка подключения', curl_error($ch));
      return false;
    }

    if (in_array($info['http_code'], array(401,403,404))) {
      self::log('Ошибка подключения', curl_error($ch));
      return false;
    }

    return $result;
  }

  private static function curlPostRequest($url, $body) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);

    if (curl_error($ch)) {
      self::log('Ошибка подключения', curl_error($ch));
      return false;
    }

    if (in_array($info['http_code'], array(401,403,404))) {
      self::log('Ошибка подключения', curl_error($ch));
      return false;
    }

    return $result;
  }

  public static function customerExists(int $customer_id)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $request_url = self::$serverLink . 'check-id?token=' . self::$token . '&id=' . $customer_id;

      try {
        // получение статуса пользователя в торговой системе 
        $response = self::curlGetRequest($request_url);
        if ($response) {
          $res = json_decode($response, true);

          if ($res['status'] == 'success') {
            $result = true; 
          }

          self::log('Успешное получение статуса пользователя', 'ID пользователя: ' . $customer->id);
        }

      } catch (Exception $e) {
        self::log('Ошибка получения статуса пользователя', $e->getMessage());
      }
    }

    return $result;
  }

  public static function customerLinks(int $customer_id)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $request_url = self::$serverLink . 'links?token=' . self::$token . '&id=' . $customer_id;

      try {
        // получение ссылок для пользователя в торговой системе 
        $response = self::curlGetRequest($request_url);
        if ($response) {
          $res = json_decode($response, true);

          if ($res['status'] == 'success') {

            $registration = !empty($res['links']['registration']) ? $res['links']['registration'] . '&copy_trade_id=' . $customer->id : '';
            $authorization = !empty($res['links']['authorization']) ? $res['links']['authorization'] . '?copy_trade_id=' . $customer->id : '';

            $result = [
              'registration' => $registration,
              'authorization' => $authorization,
            ]; 
          }

          self::log('Успешное получение ссылок для пользователя', 'ID пользователя: ' . $customer->id);
        }

      } catch (Exception $e) {
        self::log('Ошибка получения ссылок для пользователя', $e->getMessage());
      }
    }

    return $result;
  }

  public static function traderStrategies(int $customer_id)
  {
    $result = [];

    $customer = Customer::findOne($customer_id);
    if (($customer instanceof Customer) && $customer->user_type == Customer::CUSTOMER_TRADER) {

      $request_url = self::$serverLink . 'trader-summary?token=' . self::$token . '&id=' . $customer_id;
      
      try {

        self::log('Начало получения списка стратегий трейдера', '');

        // получаем список всех стратегий трейдера 
        $response = self::curlGetRequest($request_url);

        if ($response) {
          $res = json_decode($response, true);

          self::log('Ответ АПИ', $response);
          
          if (isset($res['status']) && $res['status'] == 'success' && !empty($res['info']) && !empty($res['info']['summary'])) {

            $result = $res['info']['summary'];
            
            self::log('Успешное получение списка стратегий трейдера', 'ID трейдера: ' . $customer_id);
          } elseif (isset($res['message'])) {
            self::log('Ошибка получения списка стратегий трейдера', $res['message']);
          }
        }

      } catch (Exception $e) {
        self::log('Ошибка получения списка стратегий трейдера', $e->getMessage());
      }
    }

    return $result;
  }

  public static function strategyConnect(int $customer_id, int $strategy_id, $form)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {

        $request_url = self::$serverLink . 'copy-start?token=' . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login;
        
        try {
          // подключаемся к стратегии в торговой системе 
          $response = self::curlPostRequest($request_url, $form);

          if ($response) {
            $result = json_decode($response, true);

            self::log('Ответ АПИ', $response);
            
            if (isset($result['status']) && $result['status'] == 'success') {
              self::log('Успешное подключение к стратегии', 'ID Стратегии: ' . $strategy->id);
            } elseif (isset($result['message'])) {
              self::log('Ошибка подключения', $result['message']);
            }
          }

        } catch (Exception $e) {
          self::log('Ошибка подключения', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function strategyConnectForm(int $customer_id, int $strategy_id)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {

        $lang = explode('-', Yii::$app->language)[0];
        
        $request_url = self::$serverLink . 'copy-start?token=' . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login . '&_lang=' . $lang;

        try {
          // подключаемся к стратегии в торговой системе 
          $response = self::curlGetRequest($request_url);
          if ($response) {
            $result = json_decode($response, true);

            self::log('Успешное получение формы', 'ID Стратегии: ' . $strategy->id);
          }

        } catch (Exception $e) {
          self::log('Ошибка получение формы', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function strategyEditConnectionForm(int $customer_id, int $strategy_id)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {

        $lang = explode('-', Yii::$app->language)[0];
        
        $request_url = self::$serverLink . 'copy-edit?token=' . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login . '&_lang=' . $lang;

        try {
          // подключаемся к стратегии в торговой системе 
          $response = self::curlGetRequest($request_url);
          if ($response) {
            $result = json_decode($response, true);

            self::log('Успешное получение формы редактирования', 'ID Стратегии: ' . $strategy->id);
          }

        } catch (Exception $e) {
          self::log('Ошибка получение формы редактирования', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function editStrategyConnection(int $customer_id, int $strategy_id, $form)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {
        
        $request_url = self::$serverLink . 'copy-edit?token=' . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login;

        try {
          // изменяем параметры подключения к стратегии в торговой системе 
          $response = self::curlPostRequest($request_url, $form);
          if ($response) {
            $result = json_decode($response, true);

            if ($result['status'] == 'success') { 

              $res = self::getConnectedStrategyStats($customer_id, '1', '2100290772');
              $result['strategy'] = is_array($res) ? $res[0] : null;

              self::log('Успешное изменение подключенной стратегии', 'ID Стратегии: ' . $strategy->id);
            } else {
              if (!empty($result['message'])) {
                self::log('Ошибка изменения подключенной стратегии', $result['message'] );
              } else {
                self::log('Ошибка изменения подключенной стратегии', 'Неизвестная ошибка' );
              }
            }
          }

        } catch (Exception $e) {
          $result = false;
          self::log('Ошибка изменения подключенной стратегии', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function strategyDisconnect(int $customer_id, int $strategy_id)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {
        
        $request_url = self::$serverLink . 'copy-stop?token=' . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login;

        try {
          // отключаемся от стратегии в торговой системе 
          $response = self::curlGetRequest($request_url);
          if ($response) {
            $res = json_decode($response, true);

            if ($res['status'] == 'success') { 
              $result = true;
              self::log('Успешное отключение от стратегии', 'ID Стратегии: ' . $strategy->id);
            } else {
              if (!empty($res['message'])) {
                self::log('Ошибка отключения от стратегии', $res['message'] );
              } else {
                self::log('Ошибка отключения от стратегии', 'Неизвестная ошибка' );
              }
            }
          }

        } catch (Exception $e) {
          self::log('Ошибка отключения от стратегии', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function getStrategyIncome(int $strategy_id, int $customer_id, int $from_date)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {
        
        try {
          // получаем сумму дохода инвестора по стратегии в торговой системе с указанной даты $from_date

          $result = 3000;
          self::log('Успешное получение дохода по стратегии', 'ID Стратегии: ' . $strategy_id . ', ID пользователя: ' . $customer_id);

        } catch (Exception $e) {
          self::log('Ошибка получения дохода по стратегии', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function getConnectedStrategyStats(int $customer_id, $server = '', $login = '')
  {
    $result = [];

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $request_url = self::$serverLink . 'copy-list?token=' . self::$token . '&id=' . $customer_id;

      try {
        // получаем статитстику инвестора по стратегии в торговой системе

        $response = self::curlGetRequest($request_url);
        if ($response) {
          $res = json_decode($response, true);

          if ($res['status'] == 'success') { 

            foreach ($res['list'] as $connected) {

              $strategy = Strategy::findOne([
                'forex_server' => $connected['server'], 
                'forex_login' => $connected['login'], 
                'type' => Strategy::STRATEGY_FOREX, 
                'status' => Strategy::STATUS_ACTIVE
              ]);

              if ($strategy instanceof Strategy) {
            
                if ((!$server && !$login) || ($server == $connected['server'] && $login == $connected['login'])) {

                  $chart = [];
                  if (!empty($connected['chart']) && is_array($connected['chart'])) {

                    $labels = [];
                    $data1 = [];
                    $data2 = [];
                    foreach ($connected['chart'] as $point) {
                      $labels[] = date('d.m.Y', (int)$point[0]);
                      $data1[] = (float)$point[1];
                      $data2[] = (float)$point[2];
                    }

                    $chart = [
                      'labels' => $labels,
                      'datasets' => [
                        [
                          'label' => Translation::getTranslation('Profitability'),
                          'data' => $data1,
                          'borderColor' => "rgba(0, 100, 255, 1)",
                          'backgroundColor' => "rgba(0, 100, 255, 0.5)",
                          'borderWidth' => 2,
                          'pointRadius' => 0,
                          'hoverRadius' => 0,
                          'tension' => 0,
                          'fill' => false,
                        ],
                      ],
                    ];
                  }

                  $result[] = [
                    'server' => $connected['server'],
                    'login' => $connected['login'],
                    'equity' => (float)$connected['equity'],
                    'balance' => (float)$connected['balance'],
                    'date' => (int)$connected['date'],
                    'overal_income' => (float)$connected['total_profit'],
                    'overal_income_percent' => 0,
                    'day_income' => (float)$connected['daily_profit'],
                    'day_income_percent' => 0,
                    'opentrades_floating' => (float)$connected['floating_profit'],
                    'opentrades_fixed' => (float)$connected['close_profit'],
                    'opentrades_fixed_percent' => 0,
                    'pay_in' => (float)$connected['pay_in'],
                    'pay_out' => (float)$connected['pay_out'],
                    'profitability' => (float)$connected['profitability'],
                    'profitability_daily' => (float)$connected['profitability_daily'],
                    'closedtrades' => (int)$connected['close_trades'],
                    'chart' => $chart,
                  ];
                }
              }
            }
            self::log('Успешное получение статистики по подключенным стратегиям', '');
          } else {
            if (!empty($result['message'])) {
              self::log('Ошибка получения статистики по подключенным стратегиям', $result['message'] );
            } else {
              self::log('Ошибка получения статистики по подключенным стратегиям', 'Неизвестная ошибка' );
            }
          }
        }

      } catch (Exception $e) {
        self::log('Ошибка получения статистики по стратегии', $e->getMessage());
      }
    }

    return $result;
  }

  private static function getInvestorByID($id) {
    
    $result = false;

    $customer = Customer::findOne($id);
    if ($customer instanceof Customer) {

      $trader_thumb = $customer->picture ? EasyThumbnailImage::thumbnailFileUrl(
        '@root' . $customer->picture,
        150,
        150,
        EasyThumbnailImage::THUMBNAIL_OUTBOUND
      ) : '';

      $person_data = [
        'date' => date('d.m.Y', (int)$customer->created_at),
        'name' => $customer->first_name . ' ' . $customer->last_name,
        'image' => $trader_thumb,
      ]; 

      $result = $person_data;
    }

    return $result;
  }

  private static function getNumberSign(float $number) 
  {
    return $number > 0 ? '+' : ($number < 0 ? '-' : '');
  }

  public static function getStrategyProfit(int $strategy_id, int $customer_id)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {

        $strategy_desc = StrategyDesc::findOne(['strategy_id' => $strategy_id, 'language_id' => \Yii::$app->language]);

        $request_url = self::$serverLink . 'trader-info?token='  . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login;

        try {

          // получаем статитстику инвестора по стратегии в торговой системе

          $response = self::curlGetRequest($request_url);
          if ($response) {
            $res = json_decode($response, true);

            if ($res['status'] == 'success') { 

              $result = [
                'id' => $strategy_id,
                'name' => $strategy_desc->name,
                'date' => date('d.m.Y', (int)$strategy->created_at),
                'type' => (int)$strategy->type,
                'investors' => [],
                'positions' => [],
                'investors_count' => count($res['info']['investors']),
                'amount_invested' => 0, 
                'investors_balance' => 0,
                'investors_profit' => 0,
                'commission' => 0,
              ];              

              $amount_invested = 0; 
              $investors_balance = 0;
              $investors_profit = 0;
              $commission = 0;

              $count = 0;
              foreach ($res['info']['investors'] as $person) {

                $amount_invested += (float)$person['equity'];
                $investors_balance += (float)$person['balance'];
                $investors_profit += (float)$person['profit'];
                $commission += (float)$person['commission'];

                $trading_person = self::getInvestorByID((int)$person['id']);

                $person_data = [
                  'id' => (int)$person['id'],
                  'date' => date('d.m.Y', (int)$person['date']),
                  'equity' => number_format((float)$person['equity'], 2, '.', ' ' ),
                  'balance' => number_format((float)$person['balance'], 2, '.', ' ' ),
                  'profit' => number_format(abs((float)$person['close_profit']), 2, '.', ' ' ),
                  'profit_sign' => self::getNumberSign((float)$person['close_profit']),
                  'commission' => number_format((float)$person['commission'], 2, '.', ' ' ),
                  'pay_in' => number_format((float)$person['pay_in'], 2, '.', ' ' ),
                  'pay_out' => number_format((float)$person['pay_out'], 2, '.', ' ' ),
                  'profitability' => number_format((float)$person['profitability'], 2, '.', ' ' ),
                  'opened' => $count < 2,
                  'history' => null,
                ];

                if ($trading_person) {
                  $person_data['name'] = $trading_person['name'];
                  $person_data['image'] = $trading_person['image'];
                } else {
                  $person_data['name'] = Translation::getTranslation('InvestorNotFound');
                }
                
                $result['investors'][] = $person_data;

                $count++;
              }

              $result['amount_invested'] = number_format($amount_invested, 2, '.', ' ' );
              $result['investors_balance'] = number_format($investors_balance, 2, '.', ' ' );
              $result['investors_profit'] = number_format($investors_profit, 2, '.', ' ' );
              $result['commission'] = number_format($$commission, 2, '.', ' ' );

              $operation_translations = [
                'sell' => 'OperationSale',
                'buy' => 'OperationBuy',
              ];
              foreach ($res['info']['positions'] as $position) {

                $position_data = [
                  'symbol' => $position['symbol'],
                  'cmd' => Translation::getTranslation($operation_translations[$position['cmd']]),
                  'open_time' => date('d.m.Y H:i:s', $position['open_time']),
                  'open_price' => number_format((float)$position['open_price'], 2, '.', '&nbsp;' ),
                  'current_price' => number_format((float)$position['current_price'], 2, '.', '&nbsp;' ),
                  'volume' => number_format((float)$position['volume'], 2, '.', ' ' ),
                  'profit' => number_format(abs((float)$position['profit']), 2, '.', '&nbsp;' ),
                  'profit_sign' => self::getNumberSign((float)$position['profit']),
                  'percent' => number_format(abs((float)$position['percent']), 2, '.', ' ' ),
                  'percent_sign' => self::getNumberSign((float)$position['percent']),
                ];

                $result['positions'][] = $position_data;
              }

              self::log('Успешное получение статистики по стратегии', 'ID Стратегии: ' . $strategy_id . ', ID пользователя: ' . $customer_id);

            } else {
              if (!empty($res['message'])) {
                self::log('Ошибка получения статистики по стратегии', $res['message'] );
              } else {
                self::log('Ошибка получения статистики по стратегии', 'Неизвестная ошибка' );
              }
            }
          }
        } catch (Exception $e) {
          self::log('Ошибка получения статистики по стратегии', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function getStrategyHistory(int $strategy_id, int $customer_id, $page)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {

        $request_url = self::$serverLink . 'trader-history?token='  . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login .
                                           '&page=' . $page;

        try {

          // получаем историю сделок по стратегии в торговой системе

          $response = self::curlGetRequest($request_url);
          if ($response) {
            $res = json_decode($response, true);

            if ($res['status'] == 'success') { 

              $result = [
                'list' => [],
                'page' => (int)$page,
                'page_count' => (int)$res['pagination']['page_count'],
                'page_size' => (int)$res['pagination']['page_size'],
                'total_count' => (int)$res['pagination']['total_count'],
              ];              

              $operation_translations = [
                'sell' => 'OperationSale',
                'buy' => 'OperationBuy',
              ];
              foreach ($res['history'] as $position) {

                $position_data = [
                  'symbol' => $position['symbol'],
                  'cmd' => Translation::getTranslation($operation_translations[$position['cmd']]),
                  'open_time' => date('d.m.Y H:i:s', $position['open_time']),
                  'close_time' => date('d.m.Y H:i:s', $position['close_time']),
                  'open_price' => number_format((float)$position['open_price'], 2, '.', '&nbsp;' ),
                  'current_price' => number_format((float)$position['current_price'], 2, '.', '&nbsp;' ),
                  'volume' => number_format((float)$position['volume'], 2, '.', ' ' ),
                  'profit' => number_format(abs((float)$position['profit']), 2, '.', '&nbsp;' ),
                  'profit_sign' => self::getNumberSign((float)$position['profit']),
                ];

                $result['list'][] = $position_data;
              }

              self::log('Успешное получение истории сделок стратегии', 'ID Стратегии: ' . $strategy_id . ', ID пользователя: ' . $customer_id);

            } else {
              if (!empty($res['message'])) {
                self::log('Ошибка получения истории сделок стратегии', $res['message'] );
              } else {
                self::log('Ошибка получения истории сделок стратегии', 'Неизвестная ошибка' );
              }
            }
          }
        } catch (Exception $e) {
          self::log('Ошибка получения истории сделок стратегии', $e->getMessage());
        }
      }
    }

    return $result;
  }

  public static function getInvStrategyHistory(int $strategy_id, int $customer_id, int $investor_id, $page)
  {
    $result = false;

    $customer = Customer::findOne($customer_id);
    if ($customer instanceof Customer) {

      $strategy = Strategy::findOne($strategy_id);
      if ($strategy instanceof Strategy) {

        $request_url = self::$serverLink . 'trader-investor-history?token='  . self::$token . '&id=' . $customer_id . 
                                           '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login .
                                           '&investorId=' . $investor_id .'&page=' . $page;

        try {

          // получаем историю сделок по стратегии в торговой системе

          $response = self::curlGetRequest($request_url);
          if ($response) {
            $res = json_decode($response, true);

            if ($res['status'] == 'success') { 

              $result = [
                'list' => [],
                'page' => (int)$page,
                'page_count' => (int)$res['pagination']['page_count'],
                'page_size' => (int)$res['pagination']['page_size'],
                'total_count' => (int)$res['pagination']['total_count'],
              ];              

              $operation_translations = [
                'sell' => 'OperationSale',
                'buy' => 'OperationBuy',
              ];
              foreach ($res['history'] as $position) {

                $position_data = [
                  'symbol' => $position['symbol'],
                  'cmd' => Translation::getTranslation($operation_translations[$position['cmd']]),
                  'open_time' => date('d.m.Y H:i:s', $position['open_time']),
                  'close_time' => date('d.m.Y H:i:s', $position['close_time']),
                  'open_price' => number_format((float)$position['open_price'], 2, '.', '&nbsp;' ),
                  'current_price' => number_format((float)$position['current_price'], 2, '.', '&nbsp;' ),
                  'volume' => number_format((float)$position['volume'], 2, '.', ' ' ),
                  'profit' => number_format(abs((float)$position['profit']), 2, '.', '&nbsp;' ),
                  'profit_sign' => self::getNumberSign((float)$position['profit']),
                ];

                $result['list'][] = $position_data;
              }

              self::log('Успешное получение истории сделок инвестора в стратегии', 'ID Стратегии: ' . $strategy_id . ', ID пользователя: ' . $customer_id);

            } else {
              if (!empty($res['message'])) {
                self::log('Ошибка получения истории сделок инвестора в стратегии', $res['message'] );
              } else {
                self::log('Ошибка получения истории сделок инвестора в стратегии', 'Неизвестная ошибка' );
              }
            }
          }
        } catch (Exception $e) {
          self::log('Ошибка получения истории сделок инвестора в стратегии', $e->getMessage());
        }
      }
    }

    return $result;
  }

 public static function getStrategyChart(int $strategy_id, string $period)
  {
    $result = [];

    $strategy = Strategy::findOne($strategy_id);
    if ($strategy instanceof Strategy) {
  
      $request_url = self::$serverLink . 'trader-chart?token='  . self::$token . '&id=' . Yii::$app->user->id . 
                                          '&server=' . $strategy->forex_server . '&login=' . $strategy->forex_login .
                                          '&chart=' . $period;
      try {
        // получаем статитстику инвестора по стратегии в торговой системе

        $response = self::curlGetRequest($request_url);
        if ($response) {
          $res = json_decode($response, true);

          if ($res['status'] == 'success') { 

            $labels = [];
            $data1 = [];
            $data2 = [];
            foreach ($res['chart'] as $point) {
              $labels[] = date('d.m.Y', (int)$point[0]);
              $data1[] = (float)$point[1];
              $data2[] = (float)$point[2];
            }

            $result = [
              'labels' => $labels,
              'datasets' => [
                [
                  'label' => Translation::getTranslation('StratgyInvestorsFunds'),
                  'data' => $data1,
                  'borderColor' => "rgba(255, 99, 132, 1)",
                  'backgroundColor' => "rgba(255, 99, 132, 0.5)",
                  'borderWidth' => 2,
                  'pointRadius' => 0,
                  'hoverRadius' => 0,
                  'tension' => 0,
                  'fill' => false,
                ],
                [
                  'label' => Translation::getTranslation('StratgyInvestorsBalance'),
                  'data' => $data2,
                  'borderColor' => "rgba(0, 100, 255, 1)",
                  'backgroundColor' => "rgba(0, 100, 255, 0.5)",
                  'borderWidth' => 2,
                  'pointRadius' => 0,
                  'hoverRadius' => 0,
                  'tension' => 0,
                  'fill' => false,
                ],
              ],
            ];
    
            self::log('Успешное получение графика по стратегии. ID стратегии: ' . $strategy_id);
        
          } else {
            if (!empty($res['message'])) {
              self::log('Ошибка получения графика по стратегии', $res['message'] );
            } else {
              self::log('Ошибка получения графика по стратегии', 'Неизвестная ошибка' );
            }
          }
        }

      } catch (Exception $e) {
        self::log('Ошибка получения графика по стратегии', $e->getMessage());
      }
    }

    return $result;
 }
}