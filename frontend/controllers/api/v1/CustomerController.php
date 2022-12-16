<?php

/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use yii\helpers\FileHelper;
use common\components\Logger;
use common\components\TradeSystemHelper;
use frontend\models\BotDesc;
use common\models\Customer;
use frontend\models\ChangePasswordForm;
use frontend\models\ExternalWallet;
use frontend\models\PhotoUpload;
use frontend\models\Notes;
use frontend\resources\CustomerResource;
use Yii;
use frontend\components\ApiController;
use frontend\components\Helper;
use frontend\models\Bot;
use frontend\models\CustomerBbs;
use frontend\models\CustomerBot;
use frontend\models\CustomerStrategy;
use frontend\models\Message;
use frontend\models\Movement;
use frontend\models\Payback;
use frontend\models\Payment;
use frontend\models\PaymentSearch;
use frontend\models\Qualification;
use frontend\models\Settings;
use frontend\models\Strategy;
use frontend\models\TraderRequest;
use frontend\models\Translation;
use yii\web\UploadedFile;
use himiklab\thumbnail\EasyThumbnailImage;

class CustomerController extends ApiController
{

  private $periods = [
    1 => '-1 month',
    2 => '-1 year',
    3 => '-3 years',
    4 => '-10 years',
  ];

  private $statPeriods = [
    1 => '-1 day',
    2 => '-1 month',
    3 => '-3 months',
    4 => '-1 year',
  ];

  private $periodNames = [
    1 => 'year, quarter, month, day',
    2 => 'year, quarter, month',
    3 => 'year, quarter',
    4 => 'year',
  ];

  private function getStatistics()
  {
    $searchModel = new PaymentSearch();
    $customer_totals = $searchModel->customerTotals(Yii::$app->user->id);
    $customer_monthTotals = $searchModel->customerMonthTotals(Yii::$app->user->id);

    $linearBonus = [
      'all' => (float)$customer_totals['paybacksum'],
      'month' => (float)$customer_monthTotals['paybacksum'],
    ];
    $careerBonus = [
      'all' => (float)$customer_totals['paycareerbonussum'],
      'month' => (float)$customer_monthTotals['paycareerbonussum'],
    ];
    $leaderBonus = [
      'all' => (float)$customer_totals['payleaderbonussum'],
      'month' => (float)$customer_monthTotals['payleaderbonussum'],
    ];
    $quickStartBonus = [
      'all' => (float)$customer_totals['paystartbonussum'],
      'month' => (float)$customer_monthTotals['paystartbonussum'],
    ];
    $incomeBonus = [
      'all' => (float)$customer_totals['payincomebonussum'],
      'month' => (float)$customer_monthTotals['payincomebonussum'],
    ];
    $traderBonus = [
      'all' => (float)$customer_totals['paytraderbonussum'],
      'month' => (float)$customer_monthTotals['paytraderbonussum'],
    ];

    $invoicePay = [
      'all' => (float)$customer_totals['payinvoicesum'],
      'month' => (float)$customer_monthTotals['payinvoicesum'],
    ];

    $award = [
      'all' =>  (float)$customer_totals['paybacksum'] + 
                (float)$customer_totals['payleaderbonussum'] + 
                (float)$customer_totals['paycareerbonussum'] + 
                (float)$customer_totals['paystartbonussum'] +
                (float)$customer_totals['payincomebonussum'] +
                (float)$customer_totals['paytraderbonussum'],

      'month' => (float)$customer_monthTotals['paybacksum'] + 
                 (float)$customer_monthTotals['payleaderbonussum'] + 
                 (float)$customer_monthTotals['paycareerbonussum'] + 
                 (float)$customer_monthTotals['paystartbonussum'] +
                 (float)$customer_monthTotals['payincomebonussum'] +
                 (float)$customer_monthTotals['paytraderbonussum'],
    ];

    $statistics = [
      'partner_balance' => [
        'all' => 
            // общая сумма кошелька = пополнения + все приходы (бонусы и вознаграждения) - выводы - оплаты - переводы другим
            (float)$customer_totals['payinsum'] + $award['all'] 
            - (float)$customer_totals['paymentsum'] + (float)$customer_totals['payoutsum'] 
            + (float)$customer_totals['pay2othersum'] + (float)$customer_totals['payfromothersum'] + (float)$customer_totals['payinvoicesum'],
        'today' => 12901.15,
      ],
      'strategy_balance' => [
        'all' => 7898267.23,
        'today' => 979409.86,
      ],
      'payouts' => [
        'total' => (float)$customer_totals['payoutsum'] ? number_format($customer_totals['payoutsum'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$customer_monthTotals['payoutsum'] ? number_format($customer_monthTotals['payoutsum'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'payins' => [
        'total' => (float)$customer_totals['payinsum'] ? number_format($customer_totals['payinsum'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$customer_monthTotals['payinsum'] ? number_format($customer_monthTotals['payinsum'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'pay2others' => [
        'total' => (float)$customer_totals['pay2othersum'] ? number_format($customer_totals['pay2othersum'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$customer_monthTotals['pay2othersum'] ? number_format($customer_monthTotals['pay2othersum'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'payfromothers' => [
        'total' => (float)$customer_totals['payfromothersum'] ? number_format($customer_totals['payfromothersum'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$customer_monthTotals['payfromothersum'] ? number_format($customer_monthTotals['payfromothersum'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'linearBonus' => [
        'total' => (float)$linearBonus['all'] ? number_format($linearBonus['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$linearBonus['month'] ? number_format($linearBonus['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'careerBonus' => [
        'total' => (float)$careerBonus['all'] ? number_format($careerBonus['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$careerBonus['month'] ? number_format($careerBonus['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'leaderBonus' => [
        'total' => (float)$leaderBonus['all'] ? number_format($leaderBonus['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$leaderBonus['month'] ? number_format($leaderBonus['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'quickStartBonus' => [
        'total' => (float)$quickStartBonus['all'] ? number_format($quickStartBonus['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$quickStartBonus['month'] ? number_format($quickStartBonus['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'incomeBonus' => [
        'total' => (float)$incomeBonus['all'] ? number_format($incomeBonus['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$incomeBonus['month'] ? number_format($incomeBonus['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'traderBonus' => [
        'total' => (float)$traderBonus['all'] ? number_format($traderBonus['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$traderBonus['month'] ? number_format($traderBonus['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'invoicePay' => [
        'total' => (float)$invoicePay['all'] ? number_format($invoicePay['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$invoicePay['month'] ? number_format($invoicePay['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
      'award' => [
        'total' => (float)$award['all'] ? number_format($award['all'], 2, '.', ' ') . '&nbsp;$' : '—',
        'month_total' => (float)$award['month'] ? number_format($award['month'], 2, '.', ' ') . '&nbsp;$' : '—',
      ],
    ];

    return $statistics;
  }

  private function getActiveQualification()
  {
    $user = Yii::$app->user->identity;

    $user_turnover = (float)$user->getCustomerTurnover();

    // получаем активную квалификацию пользователя
    $active_qualification = $user->getActiveQualification();
    $qualification = [
      'id' => 0,
      'icon' => "",
      'name' => "—",
      'level' => 0,
      'color' => '#BBBBBB',
      'turnover' => $user_turnover,
      'turnover_str' => number_format($user_turnover, 2, '.', ' '),
      'next_turnover' => '0',
      'next_turnover_str' => '0',
      'next_turnover_percent' => 0,
      'next_license' => '',
      'next_license_level' => 0,
    ];
    if ($active_qualification) {
      $qualification['icon'] = $active_qualification->icon ? $active_qualification->icon : '';
      $qualification['id'] = $active_qualification->id;
      $qualification['name'] = $active_qualification->name;
      $qualification['level'] = (int)$active_qualification->level;
      $qualification['color'] = '#' . $active_qualification->color;
    }

    $next_level = $qualification['level'] + 1;
    $next_qualification = Qualification::find()
      ->where(['level' => $next_level])
      ->one();
    
    if ($next_qualification instanceof Qualification) {
      $qualification['next_turnover'] = (float)$next_qualification->partners_boughts;
      $qualification['next_turnover_str'] = number_format((float)$next_qualification->partners_boughts, 2, '.', ' ');
      if ($qualification['next_turnover']) {
        $qualification['next_turnover_percent'] = round($qualification['turnover'] / $qualification['next_turnover'] * 100, 2);
      } else {
        $qualification['next_turnover_percent'] = 0;
      }

      $next_license = Bot::findOne((int)$next_qualification->bot_id);
      $next_license_desc = BotDesc::findOne(['bot_id' => (int)$next_qualification->bot_id, 'language_id' => \Yii::$app->language]);
      if ($next_license_desc instanceof BotDesc) {
        $qualification['next_license'] = $next_license_desc->name;
        $qualification['next_license_level'] = (int)$next_license->level;
      }
    }

    return $qualification;
  }

  private function getActiveLicense()
  {
    $user = Yii::$app->user->identity;

    // получаем активную лицензию пользователя
    $active_license_data = $user->getActiveLicense();
    if ($active_license_data) {
      $active_license_id = (int)$active_license_data->bot_id;
    } else {
      $active_license_id = 0;
    }
    $license = [
      'id' => $active_license_id,
      'name' => '—',
      'level' => 0,
      'price' => 0,
      'percent' => 0,
      'color' => '#BBBBBB',
      'valid_till' => '—',
    ];
    $active_license = Bot::findOne($active_license_id);
    $active_license_desc = BotDesc::findOne(['bot_id' => $active_license_id, 'language_id' => \Yii::$app->language]);
    if ($active_license_desc instanceof BotDesc) {

      $max_level = (int)Bot::find()
        ->select(['MAX(level) AS discount'])
        ->scalar();

      $license['name'] = $active_license_desc->name;
      $license['level'] = $active_license->level;
      $license['price'] = $active_license->calcRealPrice();
      $license['color'] = '#' . $active_license->color;
      $license['percent'] = (int)($license['level']/$max_level * 100);
      $license['valid_till'] = date('d.m.Y', (int)$active_license_data->valid_till);
    }

    return $license;
  }

  public function actionIndex()
  {

    // определим время последнего входа пользователя в ЛК
    $user = Yii::$app->user->identity;
    $last_visit = (int)$user->last_visit;

    $last_events = Message::getUnreadMessages($user->id);

    // если не подтвержден email
    if (!(int)$user->email_confirmed) {
      $last_events[] = [
        'id' => -1,
        'type' => Translation::getTranslation(Message::$typeTranslation[Message::TYPE_PARTNER]),
        'icon' => Message::$icons[Message::ICON_MAIL],
        'title' => Translation::getTranslation('ConfirmEmail'),
        'description' => Translation::getTranslation('ToUseAllPlatformFeatures'),
        'system' => true,
        'hidden' => false, 
      ];
    }

    $forex_strategies = TradeSystemHelper::traderStrategies($user->id);

    // получаем активную лицензию пользователя
    $license = $this->getActiveLicense($user);

    $forexConnected = CustomerStrategy::find()
      ->alias('cs')
      ->leftJoin('strategy s', 's.id = cs.strategy_id')
      ->where(['s.status' => Strategy::STATUS_ACTIVE, 'cs.status' => 1])
      ->andWhere('cs.created_at < ' . time())
      ->count();

    // если еще есть время не получение ББС
    $bbs_countdown = (int)$user->created_at - strtotime('-1 month');
    if ($bbs_countdown > 0) {

      // если активная лицензия есть, берем ее цену
      if ((float)$license['price']) {
         $license_price = number_format((float)$license['price'], 0, '.', ' ');
         $last_events[] = [
          'id' => -2,
          'type' => Translation::getTranslation(Message::$typeTranslation[Message::TYPE_BONUS]),
          'icon' => Message::$icons[Message::ICON_BONUS],
          'title' => Translation::getTranslation('Get') . ' ' . $license_price . ' $',
          'description' => Translation::getTranslation('GetQuickStartBonus'),
          'system' => true,
          'hidden' => false, 
        ];

      // если активной лиензии нет - ставим 0
      } else {
        //$last_events['bbs'] = 0;
      }
    }

    /*
    $last_events[] = [
      'id' => -3,
      'type' => Translation::getTranslation(Message::$typeTranslation[Message::TYPE_PARTNER]),
      'icon' => Message::$icons[Message::ICON_MAIL],
      'title' => 'Какой-то заголовок',
      'description' => 'Какое-то описание события для пользователя',
      'system' => true,
      'hidden' => false, 
    ];

    $last_events[] = [
      'id' => -4,
      'type' => Translation::getTranslation(Message::$typeTranslation[Message::TYPE_PARTNER]),
      'icon' => Message::$icons[Message::ICON_MAIL],
      'title' => 'Еще заголовок',
      'description' => 'Какое-то описание события для пользователя',
      'system' => true,
      'hidden' => false, 
    ];

    $last_events[] = [
      'id' => -5,
      'type' => Translation::getTranslation(Message::$typeTranslation[Message::TYPE_PARTNER]),
      'icon' => Message::$icons[Message::ICON_MAIL],
      'title' => 'И снова заголовок',
      'description' => 'Какое-то описание события для пользователя',
      'system' => true,
      'hidden' => false, 
    ];
    */

    $bbs = [
      'bbs_countdown' => (int)$user->created_at,
      'count' => Payment::BBS_REGS_CNT - CustomerBbs::findUncompleteQuarter($user),
      'max' => Payment::BBS_REGS_CNT,
      'price' => number_format((float)$license['price'], 0, '.', ' '),
    ];

    // обновим время последнего входа пользователя в ЛК
    $user->last_visit = time();
    $user->save();

    return [
      'success' => 1,
      'user' => CustomerResource::findOne($user->id),
      'license' => $license,
      'qualification' => $this->getActiveQualification($user),
      'last_visit' => $user->lastvisit,
      'last_events' => $last_events,
      'bbs' => $bbs,
      'forexStrategies' => $forex_strategies,
      'wallets' => ExternalWallet::getAllWallets($user->id),
      'levels' => $user->getFeeArray(),
      'levelPercents' => $user->getAllFeesByLevel(),
      'forex' => [
        'active' => TradeSystemHelper::customerExists($user->id), 
        'links' => TradeSystemHelper::customerLinks($user->id),
      ],
      'statistics' => $this->getStatistics(),
    ];
  }

  public function actionProfitability()
  {
    
    $user = Yii::$app->user->identity;
    $period = (int)Helper::cleanData(Yii::$app->request->post('period', 1));

    $from_date = strtotime($this->periods[$period]);
    $from_stat_date = strtotime($this->statPeriods[$period]);
    $period_name = $this->periodNames[$period];

    $sql = "
      SELECT 
        YEAR(FROM_UNIXTIME(payment_date)) AS year,
        QUARTER(FROM_UNIXTIME(payment_date)) AS quarter,
        MONTH(FROM_UNIXTIME(payment_date)) AS month,
        DAY(FROM_UNIXTIME(payment_date)) AS day,
        SUM(amount) AS sum
      FROM (
        SELECT m.created_at AS payment_date, m.amount AS amount
        FROM movement m 
        WHERE 
          m.customer_id = " . $user->id . " AND
          m.created_at >= " . $from_date . " AND
          m.move_type = " . Movement::TYPE_BONUS . " 
        
        UNION
        
        SELECT p.created_at AS payment_date, pb.amount FROM payback pb
        LEFT JOIN payment p ON p.id = pb.payment_id
        WHERE 
          pb.customer_id = " . $user->id . " AND
          p.created_at >= " . $from_date . "
      ) pm
      GROUP BY " . $period_name . "
      ORDER BY pm.payment_date ASC
    ";

    $movements = Yii::$app->db->createCommand($sql)
      ->queryAll();

    $movements_data = [];
    $movements_labels = [];
    $key = 0;
    $curDate = 0;
    $quarter = 0;
    foreach ($movements as $movement) {

      if ($period == 1) {
        $curDate = strtotime($movement['year'] . '-' . sprintf("%02d", (int)$movement['month']) . '-' . sprintf("%02d", (int)$movement['day']));
        $movements_labels[] = $movement['day'] . '.' . sprintf("%02d", (int)$movement['month']) . '.' . $movement['year'];
      } elseif ($period == 2) {
        $curDate = strtotime($movement['year'] . '-' . $movement['month'] . '-01');
        if (Yii::$app->language == "ru-RU") {
          $movements_labels[] = Helper::rus_months((int)$movement['month']) . ' ' . $movement['year'];
        } else {
          $movements_labels[] = Helper::eng_months((int)$movement['month']) . ' ' . $movement['year'];
        }
      } elseif ($period == 3) {
        $curDate = strtotime($movement['year'] . '-' . sprintf("%02d", (int)$movement['month']) . '-01');
        if (Yii::$app->language == "ru-RU") {
          $movements_labels[] = (int)$movement['quarter'] . ' квартал ' . $movement['year'];
        } else {
          $movements_labels[] = (int)$movement['quarter'] . ' quarter ' . $movement['year'];
        }
        $quarter = (int)$movement['quarter'];
      } elseif ($period == 4) {
        $curDate = strtotime($movement['year'] . '-01-01');
        $movements_labels[] = $movement['year'];
      }

      $movements_data[] = (float)$movement['sum'];

      $key++;
    } 

    $max_points = 0;
    if ($period == 1) {
      $max_points = 15;
      if ($key < $max_points) {
        for ($i = 1; $i <= $max_points - $key; $i++) {
          $curDate = strtotime('+1 day', $curDate);
          $movements_labels[] = date('d.m.Y', $curDate);
        }
      }
    } elseif ($period == 2) {  
      $max_points = 12;
      if ($key < $max_points) {
        for ($i = 1; $i <= $max_points - $key; $i++) {
          $curDate = strtotime('+1 month', $curDate);
          if (Yii::$app->language == "ru-RU") {
            $movements_labels[] = Helper::rus_months(date('n', $curDate)) . ' ' . date('Y', $curDate);
          } else {
            $movements_labels[] = Helper::eng_months(date('n', $curDate)) . ' ' . date('Y', $curDate);
          }
        }
      }
    } elseif ($period == 3) {
      $max_points = 12;
      if ($key < $max_points) {
        for ($i = 1; $i <= $max_points - $key; $i++) {
          $curDate = strtotime('+3 months', $curDate);
          $quarter++;
          if ($quarter > 4) {
              $quarter = 1;
          }
          if (Yii::$app->language == "ru-RU") {
            $movements_labels[] = $quarter . ' квартал ' . date('Y', $curDate);
          } else {
            $movements_labels[] = $quarter . ' quarter ' . date('Y', $curDate);
          }
        }
      }
    } elseif ($period == 4) {
      $max_points = 10;
      if ($key < $max_points) {
        for ($i = 1; $i <= $max_points - $key; $i++) {
          $curDate = strtotime('+1 year', $curDate);
          $movements_labels[] = date('Y', $curDate);
        }
      }
    }

    $turnover = (float)$user->getCustomerTurnover(time(), $from_stat_date);
    $payback = (float)Payback::getClientDatePayback($user->id, time(), $from_stat_date);
    $allBonus = (float)Movement::getClientBonus($user->id, null, time(), $from_stat_date);
    $qualifBonus = (float)Movement::getClientBonus($user->id, Movement::BONUS_CAREER, time(), $from_stat_date);

    return [
      'success' => 1,
      'profitability' =>  [
        'profit' => number_format($payback + $allBonus, 2, '.', ' '),
        'turnover' => number_format($turnover, 2, '.', ' '),
        'payback' => number_format($payback, 2, '.', ' '),
        'qualif_bonus' => number_format($qualifBonus, 2, '.', ' '),
        'movements' =>  [
          'labels' => $movements_labels,
          'datasets' => [
            [
              'label' => Translation::getTranslation('Profitability'),
              'data' => $movements_data,
              'borderColor' => "rgba(0, 100, 255, 1)",
              'borderWidth' => 2,
              'pointRadius' => 5,
              'hoverRadius' => 10,
              'tension' => 0,
            ],
          ],
        ],
      ],
    ];
  }

  public function actionBuyLicense()
  {
    $user = Yii::$app->user->identity;
    $balance = $user->getOverallBalance();

    $license = $this->getActiveLicense();

    $actual_price = (float)$license['price'];

    $bot_id = (int)Helper::cleanData(Yii::$app->request->post('bot_id', 0));
    $bying_license = Bot::findOne($bot_id);
    if ($bying_license instanceof Bot) {

      $new_price = $bying_license->calcRealPrice();

      $cost = $new_price - $actual_price;

      if ($cost <= $balance) {

        $new_payment = new Payment();
        $new_payment->created_at = time();
        $new_payment->customer_id = $user->id;
        $new_payment->bot_id = $bot_id;
        $new_payment->amount = (float)$cost;
        $new_payment->status = 1;
        $new_payment->comment = 'Покупка лицензии в ЛК';
        $new_payment->save();

        return [
          'success' => 1,
          'license' => $this->getActiveLicense(),
          'qualification' => $this->getActiveQualification(),
          'statistics' => $this->getStatistics(),
        ];

      }

      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => YII::t('app', 'Not enough funds. Replenish in "Wallet" section'),
      ];
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => YII::t('app', 'License not found'),
    ];
  }
  
  public function actionPayoutRequest()
  {
    if (Helper::recaptchaCheck()) {

      $user = Yii::$app->user->identity;
      $sum = (float)Helper::cleanData(Yii::$app->request->post('sum', 0));
      $wallet_id = (int)Helper::cleanData(Yii::$app->request->post('wallet', 0));
      $currency_network = (int)Helper::cleanData(Yii::$app->request->post('currency', '30'));

      $wallet = '';
      if (!$wallet_id) {
        $wallet = $user->usdt_wallet;
      } else {
        $wallet_record = ExternalWallet::findOne($wallet_id);
        if ($wallet_record instanceof ExternalWallet) {
          $wallet = $wallet_record->wallet;
        }
      }

      if (!$wallet) {
        
        Logger::paymentLog('Ошибка', 'Wallet not found');

        Yii::$app->response->setStatusCode(500);
        return [
          'success' => 0,
          'error' => 'Wallet not found',
        ];
      }

      $merchant = Settings::readAllSettings('merchant');

      $comment = 'Вывод средств, пользователь' . $user->username;
      $paid_commission = '';
      $tag = '';
      $real_fee = true; // поддерживаются - BTC, LTC, DOGE, DASH, BSV, BCH, ZEC, ETH
      $priority = "high"; // low - медленно, medium - средне, high - быстро

      require_once FileHelper::normalizePath(Yii::getAlias('@common/components/paykassa_api.class.php'));
      
      try {

        $paykassa = new \PayKassaAPI(
            $merchant['api_id'],       // идентификатор api
            $merchant['api_secret'],   // пароль api
            (bool)$merchant['mode']
        );

        $res = $paykassa->api_payment(
            $merchant['merchant_id'],  // обязательный параметр, id мерчанта с которого нужно сделать выплату
            $currency_network,       // обязательный параметр, id платежного метода
            $wallet,                 // обязательный параметр, номер кошелька на который отправляем деньги
            (float)$sum,             // обязательный параметр, сумма платежа, сколько отправить
            $merchant['currency'],           // обязательный параметр, валюта платежа
            $comment,               // обязательный параметр, комментарий к платежу, можно передать пустой
            $paid_commission,       // необязательный параметр, кто оплачивает комиссию за перевод, shop или client
            $tag,                   // необязательный параметр, тег для выплаты, можно передать пустой
            $real_fee,              // устаревший параметр, всегда имеет значение true
            $priority               // необязательный параметр(по умолчанию medium), используется для задания
                                    // приоритета включения в блок вместе с $real_fee === true
        );

        if ($res['error']) { 
          $error = $res['message'];
          if (strpos($error, 'Error code:') !== false) {
            $code = trim(trim(explode('Error code:', $error)[1], '.'));
            if ((int)$code == 59) {
              $error = YII::t('app', 'Invalid Wallet Format');
            }
          } else {
            $code = '';
          }
        } else {
  
          $out_request = new Movement();
          $out_request->customer_id = $user->id;
          $out_request->created_at = time();
          $out_request->amount = $sum;
          $out_request->move_type = Movement::TYPE_PAYOUT;
          $out_request->bonus_type = Movement::BONUS_NONE;
          $out_request->destination = 'Вывод средств с баланса из ЛК';
          $out_request->status = Movement::STATUS_APPROVED;
          $out_request->save();

          Logger::paymentLog('Создание вывода', 'Успешно');

          $withdrawal = [
            'url' => $res["data"]["explorer_transaction_link"],
          ];

          return [
            'success' => 1,
            'withdrawal' => $withdrawal,
          ];

        }

      } catch (\Exception $e) {
        $error = $e->getMessage();
      }

      Logger::paymentLog('Ошибка', $error);

      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $error,
        'code' => $code,
      ];
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => 'captcha wrong',
    ];
  }

  public function actionAddRequest()
  {
    if (Helper::recaptchaCheck()) {

      $error = 'Unknown error';

      $user = Yii::$app->user->identity;
      $sum = (float)Helper::cleanData(Yii::$app->request->post('sum', 0));
      $currency_network = (int)Helper::cleanData(Yii::$app->request->post('currency', '30'));

      $merchant = Settings::readAllSettings('merchant');

      $comment = 'Пополнение счета, пользователь' . $user->username;

      $add_request = new Movement();
      $add_request->customer_id = $user->id;
      $add_request->created_at = time();
      $add_request->amount = $sum;
      $add_request->move_type = Movement::TYPE_ADD;
      $add_request->bonus_type = Movement::BONUS_NONE;
      $add_request->destination = 'Пополнение баланса из ЛК';
      $add_request->status = Movement::STATUS_PENDING;
      $add_request->save();
      $add_request->refresh();

      require_once FileHelper::normalizePath(Yii::getAlias('@common/components/paykassa_sci.class.php'));
      
      try {

        $paykassa = new \PayKassaSCI( 
            $merchant['merchant_id'],     // идентификатор мерчанта
            $merchant['merchant_secret']  // пароль мерчанта
        );

        $res = $paykassa->sci_create_order(
            $sum,    // обязательный параметр, сумма платежа, пример: 1.0433
            $merchant['currency'],  // обязательный параметр, валюта, пример: USDT
            $add_request->id,  // обязательный параметр, уникальный числовой идентификатор платежа в вашей системе, пример: 150800
            $comment,   // обязательный параметр, текстовый комментарий платежа, пример: Заказ услуги #150800
            $currency_network // обязательный параметр, указав его Вас минуя мерчант переадресует на платежную систему, пример: 12 - Ethereum
        );

        if ($res['error']) { 
          $error = $res['message'];
        } else {
  
          Logger::paymentLog('Создание платежа', 'Успешно');

          $bill = [
            'url' => $res["data"]["url"],
          ];

          return [
            'success' => 1,
            'bill' => $bill,
          ];

        }

      } catch (\Exception $e) {
        $error = $e->getMessage();
      }

      Logger::paymentLog('Ошибка', $error);

      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $error,
      ];
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => 'captcha wrong',
    ];
  }

  public function actionTraderRequest()
  {

    $trader_request = new TraderRequest();
    $trader_request->customer_id = Yii::$app->user->id;
    $trader_request->created_at = time();
    $trader_request->status = 0;
    $trader_request->save();

    return [
      'success' => 1,
    ];
  }

  public function actionTransferRequest()
  {
    if (Helper::recaptchaCheck()) {

      $login = Helper::cleanData(Yii::$app->request->post('login', ''));
      $found_user = null;

      $found_user = Customer::findByUsername($login);
      if (!($found_user instanceof Customer)) {
        $found_user = Customer::findByEmail($login);
      }
      if (!($found_user instanceof Customer)) {
        $found_user = Customer::findByAccountID($login);
      }

      if ($found_user instanceof Customer) {

        $user = Yii::$app->user->identity;
        $sum = (float)Helper::cleanData(Yii::$app->request->post('sum', 0));
        $descr = 'Перевод средств пользователю @' . $found_user->username;

        if (Movement::addTransferToOther($user->id, $found_user, $sum, $descr)) {

          $statistics = $this->getStatistics();

          return [
            'success' => 1,
            'statistics' => $statistics,
          ];
        }
      }

      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => YII::t('app', 'Customer not found by submitted credentials'),
      ];
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => 'captcha wrong',
    ];
  }

  public function actionSaveUsdtWallet()
  {
    if (Helper::recaptchaCheck()) {

      $usdt_wallet = Helper::cleanData(Yii::$app->request->post('usdt_wallet', ''));

      $user = Yii::$app->user->identity;
      $user->usdt_wallet = $usdt_wallet;
      $user->save();

      return [
        'success' => 1,
      ];
    } else {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'captcha wrong',
      ];
    }
  }

  public function actionSaveExternalWallet($id)
  {
    $error = '';
    if (Helper::recaptchaCheck()) {

      if ($id) {
        $wallet = ExternalWallet::findOne($id);
      } else {
        $wallet = new ExternalWallet();
      }
      if ($wallet instanceof ExternalWallet) {
        try {
          $wallet->customer_id = Yii::$app->user->id;
          $wallet->name = Helper::cleanData(Yii::$app->request->post('name', ''));
          $wallet->wallet = Helper::cleanData(Yii::$app->request->post('wallet', ''));
          $wallet->save();
          $wallet->refresh();

          if ($id) {
            return [
              'success' => 1,
            ];
          } else {
            return [
              'success' => 1,
              'id' => $wallet->id,
            ];
          }
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

  public function actionDeleteExternalWallet($id)
  {
    $error = '';
    $wallet = ExternalWallet::findOne($id);
    if ($wallet instanceof ExternalWallet) {
      try {
        $wallet->delete();

        return [
          'success' => 1,
        ];
      } catch (\Exception $e) {
        $error = $e->getMessage();
      }
    } else {
      $error = YII::t('app', 'Record not found');
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => $error,
    ];
  }

  public function actionSavePersonalInfo()
  {
    if (Helper::recaptchaCheck()) {

      $user = Yii::$app->user->identity;

      try {
        $user->first_name = Helper::cleanData(Yii::$app->request->post('first_name'));
        $user->last_name = Helper::cleanData(Yii::$app->request->post('last_name'));
        $user->email = Helper::cleanData(Yii::$app->request->post('email'));
        $user->phone = Helper::cleanData(Yii::$app->request->post('phone'));
        $user->country_id = Helper::cleanData(Yii::$app->request->post('country_id', 0));
        $user->city = Helper::cleanData(Yii::$app->request->post('city', ''));

        $user->save();

        return [
          'success' => 1,
          'user' => CustomerResource::findOne(Yii::$app->user->id),
        ];
      } catch (\Exception $e) {
        Yii::$app->response->setStatusCode(500);
        return [
          'success' => 0,
          'error' => $e->getMessage(),
        ];
      }
    } else {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'captcha wrong',
      ];
    }
  }

  public function actionSaveAvatar()
  {
    $user = Yii::$app->user->identity;

    try {
      $photo_upload = new PhotoUpload();
      $photo_upload->picture = UploadedFile::getInstance($photo_upload, 'picture');
      $new_picture = $photo_upload->upload();

      if ($new_picture) {
        $user->picture = $new_picture;
        $user->save();

        return [
          'success' => 1,
          'user' => CustomerResource::findOne(Yii::$app->user->id),
        ];
      }
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'Error uploading file',
      ];
    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionRequestEmailConfirm()
  {
    $error = '';
    $user = Yii::$app->user->identity;

    try {
      if (!Customer::isPasswordResetTokenValid($user->password_reset_token)) {
        $user->generatePasswordResetToken();
        $user->save();
      }

      $email_sent = Yii::$app
        ->mailer
        ->compose(
          ['html' => 'emailConfirm-html', 'text' => 'emailConfirm-text'],
          ['user' => $user, 'base_url' => Yii::$app->request->origin . '/']
        )
        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
        ->setTo($user->email)
        ->setSubject('Сброс пароля для ' . Yii::$app->name)
        ->send();

      return [
        'success' => 1,
      ];
    } catch (\Exception $e) {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => $e->getMessage(),
      ];
    }
  }

  public function actionCheckConfirmationToken()
  {
    $error = '';
    $token = Helper::cleanData(Yii::$app->request->post('confirm_token', ''));

    if (!empty($token) && is_string($token)) {
      $user = Customer::findByPasswordResetToken($token);
      if ($user) {
        $user->email_confirmed = 1;
        $user->password_reset_token = null;
        $user->save();

        return [
          'success' => 1,
        ];
      }
    }

    Yii::$app->response->setStatusCode(500);
    return [
      'success' => 0,
      'error' => YII::t('app', 'Confirmatin token invalid or expired'),
      'post' => Yii::$app->request->post(),
    ];
  }

  public function actionChangePassword()
  {
    if (Helper::recaptchaCheck()) {
      $user = Yii::$app->user->identity;

      try {

        $password_form = new ChangePasswordForm();

        if ($password_form->load(Helper::cleanData(Yii::$app->request->post())) && $password_form->validate()) {

          $user->setPassword($password_form->password);
          $user->save();

          return [
            'success' => 1,
          ];
        }

        Yii::$app->response->setStatusCode(500);
        $error = Helper::getErrorFromModel($password_form);
        return [
          'success' => 0,
          'error' => $error,
        ];
      } catch (\Exception $e) {
        Yii::$app->response->setStatusCode(500);
        return [
          'success' => 0,
          'error' => $e->getMessage(),
        ];
      }
    } else {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'captcha wrong',
      ];
    }
  }

  public function actionHideUserMessage($id)
  {
	  $message = Message::findOne(['id' => $id, 'customer_id' => Yii::$app->user->id]);
	  if ($message instanceof Message) {
		  $message->viewed = 1;
		  $message->save();
		  
		  return [
			'success' => 1,
			'id' => $id,
		  ];		  
	  } else {
		  Yii::$app->response->setStatusCode(404);
		  return [
        'success' => 0,
        'error' => 'Сообщение не найдено'
		  ];		  
	  }
  }

  public function actionReceiveNotes()
  {
	  return [
		'success' => 1,
		'notes' => Notes::getAllNotes(),
	  ];
  }

  public function actionAddNewNote()
  {
	  $post = Helper::cleanData(Yii::$app->request->post());
	  
	  $newNote = new Notes();
	  
	  $newNote->title = $post['title'];
	  $newNote->date = time();
	  $newNote->completed = 0;
	  $newNote->save();
	  $newNote->refresh();
	  
	  $attrs = $newNote->attributes;
	  $attrs['datetext'] = $newNote->datetext;
	  
	  return [
		'success' => 1,
		'note' => $attrs,
	  ];
  }

  public function actionEditNote($id)
  {
	if (Helper::recaptchaCheck()) {
	  $post = Helper::cleanData(Yii::$app->request->post());
	  
	  $editNote = Notes::findOne($id);
	  
	  $editNote->title = $post['title'];
	  $editNote->save();
	  
	  $attrs = $editNote->attributes;
	  $attrs['datetext'] = $editNote->datetext;
	  
	  return [
		'success' => 1,
		'note' => $attrs,
		'post' => $post,
	  ];
	  
    } else {
      Yii::$app->response->setStatusCode(500);
      return [
        'success' => 0,
        'error' => 'captcha wrong',
      ];
    }
  }

  public function actionDeleteThisNote($id)
  {
	  $note = Notes::findOne($id);
	  if ($note instanceof Notes) {
		  $note->delete();
		  
		  return [
			'success' => 1,
			'id' => $id,
		  ];		  
	  } else {
		  Yii::$app->response->setStatusCode(404);
		  return [
			'success' => 0,
			'error' => 'Заметка не найдена'
		  ];		  
		  
	  }
  }
}
