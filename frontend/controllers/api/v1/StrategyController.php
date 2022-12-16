<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use common\components\TradeSystemHelper;
use common\models\Customer;
use common\models\Language;
use frontend\models\Strategy;
use frontend\models\StrategyDesc;
use frontend\models\CustomerStrategy;
use frontend\models\Translation;
use Yii;
use frontend\components\ApiController;
use frontend\components\Helper;
use frontend\models\Message;
use frontend\models\Settings;
use frontend\models\StrategyPhotoUpload;
use yii\data\ActiveDataProvider;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\web\UploadedFile;

class StrategyController extends ApiController
{

  private function getNumberSign(float $number) 
  {
    return $number > 0 ? '+' : ($number < 0 ? '-' : '');
  }

  public function actionIndex($page = 1)
  {
    try {

      $user = Yii::$app->user->identity;
      
      $pagesize = (int)Yii::$app->request->post('pagesize');
      $type = Helper::cleanData(Yii::$app->request->post('type', 0));

      $query = Strategy::find()
        ->alias('s')
        ->select([
          's.*',
          'sd.name AS name',
          'sd.description AS description',
          '(SELECT COUNT(cs.id) 
            FROM customer_strategy cs 
            WHERE 
              cs.customer_id = ' . $user->id . ' AND
              cs.strategy_id = s.id AND
              cs.status = 1 AND
              cs.created_at < ' . time() . '
          ) AS connected',
        ])
        ->leftJoin('strategy_desc sd', 'sd.strategy_id = s.id AND sd.language_id = "' . \Yii::$app->language . '"')
        ->where(['s.type' => $type, 's.status' => Strategy::STATUS_ACTIVE]);

      $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'pagination' => [
          'pageSize' => $pagesize,
        ],
        'sort' => [
          'defaultOrder'=>[
            'created_at'=>SORT_DESC
          ],
        ],
      ]);

      $models = $dataProvider->getModels();
      $items = [];
      foreach ($models as $item) {

        $video_str = str_replace(array('https://www.youtube.com/watch?v=', 'https://youtu.be/'), 'https://www.youtube.com/embed/', $item->video);
        
        $thumb = $item->image ? EasyThumbnailImage::thumbnailFileUrl(
          '@root' . $item->image,
          1600,
          900,
          EasyThumbnailImage::THUMBNAIL_OUTBOUND
        ) : '';

        $items[] = [
          'id' => $item->id,
          'trader_id' => $item->customer_id,
          'date' => date('d.m.Y', $item->created_at),
          'name' => $item->name,
          'description' => $item->description,
          'trade_link' => $item->trade_link,
          'image' => $thumb,
          'video' => $video_str,
          'performance_fee' => number_format((float)$item->performance_fee, 2, '.', ' '),
          'connected' => (int)$item->connected,
        ];
      }
      $count = $dataProvider->getTotalCount();

      return [
        'success' => 1,
        'strategies' => [
          'list' => $items,
          'page' => (int)$page,
          'count' => $count,
          'type' => $type,
        ],
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionConnected($page = 1)
  {
    try {
      $pagesize = (int)Yii::$app->request->post('pagesize');
      $type = Helper::cleanData(Yii::$app->request->post('type', 0));

      $query = CustomerStrategy::find()
        ->alias('cs')
        ->select([
          'cs.*',
          'CONCAT(c.first_name , " ", c.last_name) AS trader_name',
          'c.picture AS trader_image',
          's.forex_server AS forex_server',
          's.forex_login AS forex_login',
          'sd.name AS name',
        ])
        ->leftJoin('strategy s', 's.id = cs.strategy_id')
        ->leftJoin('customer c', 'c.id = s.customer_id')
        ->leftJoin('strategy_desc sd', 'sd.strategy_id = s.id AND sd.language_id = "' . Yii::$app->language . '"')
        ->where(['cs.customer_id' => Yii::$app->user->id , 's.type' => $type, 'cs.status' => 1, 's.status' => Strategy::STATUS_ACTIVE]);

      $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'pagination' => [
          'pageSize' => $pagesize,
        ],
        'sort' => [
          'defaultOrder'=>[
            'created_at'=>SORT_DESC
          ],
        ],
      ]);

      $models = $dataProvider->getModels();
      $items = [];
      $trade_system_infos = [];

      if ($type == 0) {
      // получаем статистику инвестора по стратегии
        $trade_system_infos = TradeSystemHelper::getConnectedStrategyStats(Yii::$app->user->id);
      }

      foreach ($models as $item) {

        $trader_thumb = $item->trader_image ? EasyThumbnailImage::thumbnailFileUrl(
          '@root' . $item->trader_image,
          150,
          150,
          EasyThumbnailImage::THUMBNAIL_OUTBOUND
        ) : '';

        $strategy_item = [
          'id' => $item->id,
          'strategy_id' => $item->strategy_id,
          'name' => $item->name,
          'trader_name' => $item->trader_name,
          'trader_image' => $trader_thumb,
          'trade_system_info' => [],
        ];

        if ($type == 0) {
          foreach ($trade_system_infos as $connected_item) {
            if ($connected_item['server'] == $item->forex_server && $connected_item['login'] == $item->forex_login) {

              $strategy_item['trade_system_info'] = [
                'date' => date('d.m.Y', $connected_item['date']),
                'equity' => number_format((float)$connected_item['equity'], 2, '.', ' '),
                'balance' => number_format((float)$connected_item['balance'], 2, '.', ' '),
                'overal_income' => number_format(abs((float)$connected_item['overal_income']), 2, '.', ' '),
                'overal_income_sign' => $this->getNumberSign((float)$connected_item['overal_income']),
                'overal_income_percent' => '',
                'day_income' => number_format(abs((float)$connected_item['day_income']), 2, '.', ' '),
                'day_income_sign' => $this->getNumberSign((float)$connected_item['day_income']),
                'day_income_percent' => '',
                'opentrades_floating' => number_format(abs((float)$connected_item['opentrades_floating']), 2, '.', ' '),
                'opentrades_floating_sign' => $this->getNumberSign((float)$connected_item['opentrades_floating']),
                'opentrades_fixed' => number_format(abs((float)$connected_item['opentrades_fixed']), 2, '.', ' '),
                'opentrades_fixed_sign' => $this->getNumberSign((float)$connected_item['opentrades_fixed']),
                'opentrades_fixed_percent' => '',
                'pay_in' => number_format((int)$connected_item['pay_in'], 2, '.', ' '),
                'pay_out' => number_format((int)$connected_item['pay_out'], 2, '.', ' '),
                'profitability' => number_format((int)$connected_item['profitability'], 2, '.', ' '),
                'profitability_daily' => number_format((int)$connected_item['profitability_daily'], 2, '.', ' '),
                'closedtrades' => number_format((int)$connected_item['closedtrades'], 0, '.', ' '),
                'chart' => $connected_item['chart']
              ];
              break;
            }
          }
        }
        $items[] = $strategy_item;
      }
      $count = $dataProvider->getTotalCount();

      return [
        'success' => 1,
        'connected' => [
          'list' => $items,
          'page' => (int)$page,
          'count' => $count,
          'type' => $type,
        ],
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }
  public function actionMyStrategies($page = 1)
  {
    try {
      $pagesize = (int)Yii::$app->request->post('pagesize');
      $type = Helper::cleanData(Yii::$app->request->post('type', 0));

      $query = Strategy::find()
        ->alias('s')
        ->select([
          's.*',
          'sd.name AS name',
        ])
        ->leftJoin('strategy_desc sd', 'sd.strategy_id = s.id AND sd.language_id = "' . \Yii::$app->language . '"')
        ->where(['s.customer_id' => Yii::$app->user->id, 's.type' => $type, 's.status' => Strategy::STATUS_ACTIVE]);

      $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'pagination' => [
          'pageSize' => $pagesize,
        ],
        'sort' => [
          'defaultOrder'=>[
            'created_at'=>SORT_DESC
          ],
        ],
      ]);

      $models = $dataProvider->getModels();
      $items = [];
      $trade_system_infos = [];

      if ($type == 0) {
        $trade_system_infos = TradeSystemHelper::traderStrategies(Yii::$app->user->id);
      }

      foreach ($models as $item) {

        $strategy_item = [
          'id' => $item->id,
          'date' => date('d.m.Y', $item->created_at),
          'name' => $item->name,
          'trade_system_info' => [],
        ];

        if ($type == 0) {
          foreach ($trade_system_infos as $my_item) {
            if ($my_item['server'] == $item->forex_server && $my_item['login'] == $item->forex_login) {
              $strategy_item['trade_system_info'] = [
                'account' => $my_item['account'],
                'investors_count' => number_format((int)$my_item['investors_count'], 0, '.', ' '),
                'investors_balance' => number_format(abs((float)$my_item['investors_balance']), 2, '.', ' '),
                'investors_balance_sign' => $this->getNumberSign((float)$my_item['investors_balance']),
                'investors_equity' => number_format(abs((float)$my_item['investors_equity']), 2, '.', ' '),
                'investors_equity_sign' => $this->getNumberSign((float)$my_item['investors_equity']),
                'investors_profit' => number_format(abs((float)$my_item['investors_profit']), 2, '.', ' '),
                'investors_profit_sign' => $this->getNumberSign((float)$my_item['investors_profit']),
                'commission' => number_format(abs((float)$my_item['commission']), 2, '.', ' '),
                'commission_sign' => $this->getNumberSign((float)$my_item['commission']),
              ];
              break;
            }
          }
        }
        $items[] = $strategy_item;
      }
      $count = $dataProvider->getTotalCount();

      return [
        'success' => 1,
        'my' => [
          'list' => $items,
          'page' => (int)$page,
          'count' => $count,
          'type' => $type,
        ],
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionStrategyRequests()
  {
    try {    
      $type = Helper::cleanData(Yii::$app->request->post('type', 0));

      $not_active = Strategy::find()
        ->alias('s')
        ->select([
          's.*',
          'sd.name AS name',
          'sd.description AS description',
        ])
        ->leftJoin('strategy_desc sd', 'sd.strategy_id = s.id AND sd.language_id = "' . \Yii::$app->language . '"')
        ->where(['s.customer_id' => Yii::$app->user->id, 's.type' => $type])
        ->andWhere('s.status != ' . Strategy::STATUS_ACTIVE)
        ->all();

      $requests = [];
      foreach ($not_active as $item) {
        $requests[] = [
          'id' => $item->id,
          'type' => (int)$item->type,
          'image' => $item->image,
          'video' => $item->video,
          'performance_fee' => (float)$item->performance_fee,
          'name' => $item->name,
          'description' => $item->description,
          'trade_link' => $item->trade_link,
          'stock_name' => $item->stock_name,
          'market_name' => $item->market_name,
          'connect_key' => $item->connect_key,
          'connect_secret' => $item->connect_secret,
          'status' => (int)$item->status,
          'opened' => false,
        ];
      }  

      return [
        'success' => 1,
        'requests' => $requests,
      ];

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionStrategyProfitability($id)
  {
    try {    

      $strategy = Strategy::findOne($id);

      if ($strategy instanceof Strategy) {
      
        // получаем статистику стратегии 
        $result = TradeSystemHelper::getStrategyProfit($id, \Yii::$app->user->id);

        if ($result) {

          $history = TradeSystemHelper::getStrategyHistory($id, \Yii::$app->user->id, 1);

          return [
            'success' => 1,
            'profitability' => $result,
            'history' => $history,
          ];
        }

        throw new \Exception(YII::t('app', 'Strategy statistics not received')); 
      } else {
        throw new \Exception(YII::t('app', 'Record not found')); 
      }

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }
 
  public function actionStrategyHistory($id)
  {
    $page = Helper::cleanData(Yii::$app->request->post('page', 1));

    try {    

      $strategy = Strategy::findOne($id);

      if ($strategy instanceof Strategy) {
      
        // получаем статистику стратегии 
        $result = TradeSystemHelper::getStrategyHistory($id, \Yii::$app->user->id, $page);

        if ($result) {

          return [
            'success' => 1,
            'history' => $result,
          ];
        }

        throw new \Exception(YII::t('app', 'Strategy history not received')); 
      } else {
        throw new \Exception(YII::t('app', 'Record not found')); 
      }

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }
 
  public function actionInvestorStrategyHistory($id)
  {
    $page = Helper::cleanData(Yii::$app->request->post('page', 1));
    $investor_id = Helper::cleanData(Yii::$app->request->post('investor_id', 0));

    try {    

      $strategy = Strategy::findOne($id);

      if ($strategy instanceof Strategy) {
      
        // получаем статистику стратегии 
        $result = TradeSystemHelper::getInvStrategyHistory($id, \Yii::$app->user->id, (int)$investor_id, $page);

        if ($result) {

          return [
            'success' => 1,
            'history' => $result,
          ];
        }

        throw new \Exception(YII::t('app', 'Strategy investor history not received')); 
      } else {
        throw new \Exception(YII::t('app', 'Record not found')); 
      }

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }
 
  public function actionStrategyProfitabilityChart($id)
  {
    try {    
      $strategy = Strategy::findOne($id);

      if ($strategy instanceof Strategy) {
        $period = Helper::cleanData(Yii::$app->request->post('period', 'all'));

        // получаем статистику стратегии 
        $profitability_chart = TradeSystemHelper::getStrategyChart((int)$id, $period);

        if ($profitability_chart) {
          return [
            'success' => 1,
            'profitability_chart' => $profitability_chart,
          ];
        }

        throw new \Exception(YII::t('app', 'Strategy statistics chart not received')); 
      } else {
        throw new \Exception(YII::t('app', 'Record not found')); 
      }

    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionNewStrategy()
  {
    $error = '';
    if (Helper::recaptchaCheck()) {

      $id = (int)Helper::cleanData(Yii::$app->request->post('id', 0));

      $trader_settings = Settings::readAllSettings('trader');

      if ($id) {
        $strategy = Strategy::findOne($id);
      } else {
        $strategy = new Strategy();
        $strategy->created_at = time();
      }
      if ($strategy instanceof Strategy) {
        try {
          $strategy->customer_id = Yii::$app->user->id;
          $strategy->type = (int)Helper::cleanData(Yii::$app->request->post('type', 0));
          $strategy->trade_link = Helper::cleanData(Yii::$app->request->post('trade_link', ''));
          $strategy->referals_start = $trader_settings['referals_start'];
          $strategy->referals_period = $trader_settings['referals_period'];
          $strategy->platform_fee = $trader_settings['platform_fee'];
          $strategy->status = Strategy::STATUS_REVIEW;
          $strategy->save();
          $strategy->refresh();

          if ($id) {
            $strategy_desc = StrategyDesc::findOne(['strategy_id' => $id, 'language_id' => \Yii::$app->language]);
            if (!($strategy_desc instanceof StrategyDesc)) {
              $strategy_desc = new StrategyDesc();
            }
          } else {
            $strategy_desc = new StrategyDesc();
          }
          $strategy_desc->strategy_id = $strategy->id;
          $strategy_desc->language_id = \Yii::$app->language;
          $strategy_desc->name = Helper::cleanData(Yii::$app->request->post('name', ''));
          $strategy_desc->description = Helper::cleanData(Yii::$app->request->post('description', ''));
          $strategy_desc->save();

          return [
            'success' => 1,
            'request' => [
              'id' => $strategy->id,
              'type' => (int)$strategy->type,
              'image' => $strategy->image,
              'video' => $strategy->video,
              'performance_fee' => (float)$strategy->performance_fee,
              'name' => $strategy_desc->name,
              'description' => $strategy_desc->description,
              'trade_link' => $strategy->trade_link,
              'stock_name' => $strategy->stock_name,
              'market_name' => $strategy->market_name,
              'connect_key' => $strategy->connect_key,
              'connect_secret' => $strategy->connect_secret,
              'status' => (int)$strategy->status,
              'opened' => false,
            ],
          ];

        } catch (\Exception $e) {
          $error = $e->getMessage();
        }
      } else {
        $error = YII::t('app', 'Record not found');
      }
    } else {
      $error = 'captcha wrong';
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionFillStrategy()
  {
    $error = '';
    if (Helper::recaptchaCheck()) {

      $id = (int)Helper::cleanData(Yii::$app->request->post('id', 0));
      $strategy = Strategy::findOne($id);

      if ($id && ($strategy instanceof Strategy)) {
        $trader_settings = Settings::readAllSettings('trader');

        try {

          $strategyId = Helper::cleanData(Yii::$app->request->post('forex_strategy', ''));
          if ($strategyId) {
            if ($strategy->type == Strategy::STRATEGY_FOREX) {
              $strategyId_arr = explode('###', $strategyId);
              $server = $strategyId_arr[0];
              $login = isset($strategyId_arr[1]) ? $strategyId_arr[1] : '';
              if ($server && $login) {

                $found = Strategy::find()
                  ->where(['status' => Strategy::STATUS_ACTIVE, 'type' => Strategy::STRATEGY_FOREX, 'forex_server' => $server, 'forex_login' => $login])
                  ->count();

                if ($found) {
                  throw new \Exception(YII::t('app', 'This Investizo strategy already activated in Copy Trade'), 1);
                }

                $strategy->forex_server = $server;
                $strategy->forex_login = $login;
              } else {
                throw new \Exception(YII::t('app', 'Investizo strategy undefined'), 1);
              }
            }
          }

          $image_upload = new StrategyPhotoUpload();
          $image_upload->image = UploadedFile::getInstance($image_upload, 'image');
          $new_image = $image_upload->upload();

          if ($new_image) {
            $strategy->image = $new_image;
          }

          $strategy->video = Helper::cleanData(Yii::$app->request->post('video', ''));
          $strategy->performance_fee = (float)Helper::cleanData(Yii::$app->request->post('performance_fee', 0));
          $strategy->stock_name = Helper::cleanData(Yii::$app->request->post('stock_name', ''));
          $strategy->market_name = Helper::cleanData(Yii::$app->request->post('market_name', ''));
          $strategy->connect_key = Helper::cleanData(Yii::$app->request->post('connect_key', ''));
          $strategy->connect_secret = Helper::cleanData(Yii::$app->request->post('connect_secret', ''));
          $strategy->status = Strategy::STATUS_REVIEW;
          $strategy->save();

          $strategy_desc = StrategyDesc::findOne(['strategy_id' => $id, 'language_id' => \Yii::$app->language]);

          return [
            'success' => 1,
            'request' => [
              'id' => $strategy->id,
              'type' => (int)$strategy->type,
              'name' => $strategy_desc ? $strategy_desc->name : '',
              'description' => $strategy_desc ? $strategy_desc->description : '',
              'image' => $strategy->image,
              'video' => $strategy->video,
              'performance_fee' => (float)$strategy->performance_fee,
              'trade_link' => $strategy->trade_link,
              'stock_name' => $strategy->stock_name,
              'market_name' => $strategy->market_name,
              'connect_key' => $strategy->connect_key,
              'connect_secret' => $strategy->connect_secret,
              'status' => (int)$strategy->status,
              'opened' => false,
            ],
          ];

        } catch (\Exception $e) {
          $error = $e->getMessage();
        }
      } else {
        $error = YII::t('app', 'Record not found');
      }
    } else {
      $error = 'captcha wrong';
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionDeleteStrategyRequest($id)
  {
    try {    

      $strategy = Strategy::findOne($id);
      if ($strategy instanceof Strategy) {
        $strategy->delete();
        Strategy::getDb()
            ->createCommand()
            ->delete('strategy_desc', ['strategy_id' => $id])
            ->execute();

        return [
          'success' => 1,
          'id' => $id,
        ];
      } else {
        $error = YII::t('app', 'Record not found');
      }

    } catch (\Exception $e) {
      $error = $e->getMessage();
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionConnectStrategyFormRequest($id)
  {
    try {    

      $res = TradeSystemHelper::strategyConnectForm((int)Yii::$app->user->id, (int)$id);
      if ($res) {
        if (isset($res['status']) && $res['status'] == 'success') {
          return [
            'success' => 1,
            'id' => $id,
            'form' => $res,
          ];
        } else {
          $error = YII::t('app', 'Form not received');
          if (isset($res['code'])) {
            $error = $res['code'];
          } elseif (isset($res['message'])) {
            $error = $res['message'];
          }
        }
      } else {
        $error = YII::t('app', 'Form not received');
      }

    } catch (\Exception $e) {
      $error = $e->getMessage();
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionEditconnectionStrategyFormRequest($id)
  {
    try {    

      $res = TradeSystemHelper::strategyEditConnectionForm((int)Yii::$app->user->id, (int)$id);
      if ($res) {
        if (isset($res['status']) && $res['status'] == 'success') {
          return [
            'success' => 1,
            'id' => $id,
            'form' => $res,
          ];
        } else {
          $error = YII::t('app', 'Form not received');
          if (isset($res['code'])) {
            $error = $res['code'];
          } elseif (isset($res['message'])) {
            $error = $res['message'];
          }
        }
      } else {
        $error = YII::t('app', 'Form not received');
      }

    } catch (\Exception $e) {
      $error = $e->getMessage();
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionConnectStrategyRequest($id)
  {
    if (Helper::recaptchaCheck()) {
      
      $post = Helper::cleanData(Yii::$app->request->post());
      if (isset($post['token'])) {
        unset($post['token']);
      }
      if (isset($post['lang'])) {
        unset($post['lang']);
      }

      try {

        $res = CustomerStrategy::connect(Yii::$app->user->id, $id, $post);
        
        if ($res) {

          if (isset($res['status']) && $res['status'] == 'success') {
            return [
              'success' => 1,
              'post' => $post,
              'result' => $res,
              'id' => $id,
            ];
          } else {
            $error = YII::t('app', 'Not connected');
            if (isset($result['message'])) {
              $error = $result['message'];
            }
          }
        } else {
          $error = YII::t('app', 'Not connected');
        }

      } catch (\Exception $e) {
        $error = $e->getMessage();
      }

    } else {
      $error = 'captcha wrong';
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'post' => isset($post) ? $post : null,
      'result' => isset($result) ? $result : null,
      'error' => $error,
    ];
  }

  public function actionEditconnectionStrategyRequest($id)
  {
    if (Helper::recaptchaCheck()) {
      
      $post = Helper::cleanData(Yii::$app->request->post());
      if (isset($post['token'])) {
        unset($post['token']);
      }
      if (isset($post['lang'])) {
        unset($post['lang']);
      }

      try {

        $res = TradeSystemHelper::editStrategyConnection(Yii::$app->user->id, $id, $post);
        
        if ($res) {

          if (isset($res['status']) && $res['status'] == 'success') {
            return [
              'success' => 1,
              'post' => $post,
              'strategy' => $res['strategy'],
              'id' => $id,
            ];
          } else {
            $error = YII::t('app', 'Connection not edited');
            if (isset($result['message'])) {
              $error = $result['message'];
            }
          }
        } else {
          $error = YII::t('app', 'Connection not edited');
        }

      } catch (\Exception $e) {
        $error = $e->getMessage();
      }

    } else {
      $error = 'captcha wrong';
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'post' => isset($post) ? $post : null,
      'result' => isset($result) ? $result : null,
      'error' => $error,
    ];
  }

  public function actionSendTraderMessage($id)
  {      
    if (Helper::recaptchaCheck()) {

      $message = Helper::cleanData(Yii::$app->request->post('message', ''));
      if ($message) {

        $strategy = Strategy::findOne((int)$id);
        if ($strategy instanceof Strategy) {

          $strategy_desc = StrategyDesc::findOne(['strategy_id' => $id, 'language_id' => \Yii::$app->language]);

          // получаем статистику стратегии 
          $strategy_data = TradeSystemHelper::getStrategyProfit($id, \Yii::$app->user->id);

          try {

            foreach ($strategy_data['investors'] as $investor) {
              $customer = Customer::findOne($investor['id']);
              if ($customer instanceof Customer) {
                $titles = [];
                $texts = [];
                foreach (Language::find()->all() as $language) {
                  $titles[$language->locale] = sprintf(Translation::getTranslation('MessageStrategyTrader', $language->locale), $strategy_desc->name);
                  $texts[$language->locale] = $message;
                }
                Message::compose($customer->id, $titles, $texts, Message::TYPE_TRADER, Message::ICON_INFO);
              }
            }

            return [
              'success' => 1,
              'id' => $id,
            ];


          } catch (\Exception $e) {
            $error = $e->getMessage();
          }
        } else {
          $error = YII::t('app', 'Strategy not found');
        }
      } else {
        $error = YII::t('app', 'Message is empty');
      }

    } else {
      $error = 'captcha wrong';
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionDisconnectStrategyRequest($id)
  {      
    $cust_strategy = CustomerStrategy::findOne((int)$id);
    if ($cust_strategy instanceof CustomerStrategy) {

      try {

        $res = CustomerStrategy::disconnect($cust_strategy);
        
        if ($res) {

          return [
            'success' => 1,
            'id' => $id,
          ];
        } else {
          $error = YII::t('app', 'Not disconnected');
        }

      } catch (\Exception $e) {
        $error = $e->getMessage();
      }
    } else {
      $error = YII::t('app', 'Strategy not found');
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

}