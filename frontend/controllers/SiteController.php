<?php

namespace frontend\controllers;

use common\components\Logger;
use yii\helpers\FileHelper;
use frontend\models\ConsultForm;
use frontend\models\Infostep;
use frontend\models\TestRequestForm;
use Yii;
use yii\db\Expression;
use yii\helpers\Json;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\Settings;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
//use frontend\models\PaymentForm;
use frontend\models\Bot;
use frontend\models\Faq;
use frontend\models\Reviews;
use frontend\models\Tradecase;
use common\models\Customer;
use frontend\models\PartnerPromocode;
use frontend\models\Payment;
use frontend\models\Prepayment;
use yii\bootstrap\ActiveForm;
use frontend\components\Helper;
use frontend\models\CustomerStrategy;
use frontend\models\Movement;

/**
 * Site controller
 */
class SiteController extends Controller
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'only' => ['logout', 'signup'],
        'rules' => [
          [
            'actions' => ['signup'],
            'allow' => true,
            'roles' => ['?'],
          ],
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
      'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
      ],
    ];
  }

  public function beforeAction($action)
  {
    if ($action->id == 'payment-success' || $action->id == 'payment-fail' || $action->id == 'payment-callback') {
      $this->enableCsrfValidation = false;
    }
    return parent::beforeAction($action);
  }
  /**
   * Displays homepage.
   *
   * @return mixed
   */
  public function actionIndex()
  {
    //$this->layout = 'containerless';

    $bot = Bot::find()
      ->where(['status' => 1])
      ->orderBy(['sort_order' => SORT_ASC]);
    $faq = Faq::find()
      ->where(['status' => 1])
      ->orderBy(['sort_order' => SORT_ASC]);
    $infosteps = Infostep::find()
      ->where(['status' => 1])
      ->orderBy(['sort_order' => SORT_ASC]);
    $tradecase = Tradecase::find()
      ->where(['status' => 1])
      ->orderBy(['sort_order' => SORT_ASC]);
    $review = Reviews::find()
      ->where(['status' => 1])
      ->orderBy(new Expression('rand()'));

    //        Yii::$app->session->remove('partner-reflink');
    $ref = Yii::$app->request->get('ref', '');
    if ($ref) {
      $partner = Customer::find()->where(['status' => Customer::STATUS_ACTIVE, 'ref_link' => $ref]);
      if (!Yii::$app->user->isGuest) {
        $partner = $partner->andWhere(['not', ['id' => Yii::$app->user->id]]);
      }

      if ($partner->exists()) {
        Yii::$app->session->set('partner-reflink', $ref);
      } else {
        Yii::$app->session->remove('partner-reflink');
      }
    }
    $utm_source = Yii::$app->request->get('utm_source', '');
    if ($utm_source) {
      Yii::$app->session->set('utm_source', $utm_source);
    }
    $utm_campaign = Yii::$app->request->get('utm_campaign', '');
    if ($utm_campaign) {
      Yii::$app->session->set('utm_campaign', $utm_campaign);
    }
    $utm_medium = Yii::$app->request->get('utm_medium', '');
    if ($utm_medium) {
      Yii::$app->session->set('utm_medium', $utm_medium);
    }
    $utm_content = Yii::$app->request->get('utm_content', '');
    if ($utm_content) {
      Yii::$app->session->set('utm_content', $utm_content);
    }
    $utm_term = Yii::$app->request->get('utm_term', '');
    if ($utm_term) {
      Yii::$app->session->set('utm_term', $utm_term);
    }

    $model = new ConsultForm();
    if (!Yii::$app->user->isGuest) {
      $cur_cust = Customer::findOne(['id' => Yii::$app->user->id, 'status' => Customer::STATUS_ACTIVE]);
      $model->fio = $cur_cust->first_name;
      $model->phone = $cur_cust->phone;
      $model->email = $cur_cust->email;
    } else {
      $customer_reg_info = Yii::$app->session->get('customer_reg_info', '');
      if (is_array($customer_reg_info) && !empty($customer_reg_info)) {
        $model->fio = $customer_reg_info['cust_name'];
        $model->phone = $customer_reg_info['cust_phone'];
        $model->email = $customer_reg_info['cust_email'];
      }
    }

    return $this->render('index', [
      'model' => $model,
      'bot' => $bot,
      'faq' => $faq,
      'infosteps' => $infosteps,
      'tradecase' => $tradecase,
      'review' => $review,
      'merchant' => Settings::readAllSettings('merchant'),
      'seo' => Settings::readAllSettings('mainpage'),
      'general' => Settings::readAllSettings('general'),
      'social' => Settings::readAllSettings('social'),
    ]);
  }

  /**
   * Displays Login from.
   *
   * @return mixed
   */
  public function actionLoginpopup()
  {
    if (Yii::$app->user->isGuest) {
      $model = new LoginForm();
      return $this->renderAjax('login', [
        'model' => $model,
      ]);
    }
  }

  /**
   * Logs in a user.
   *
   * @return mixed
   */
  public function actionLogin()
  {
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }

    Yii::$app->response->format = Response::FORMAT_JSON;
    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($model->login()) {
        return [
          'success' => true,
          'token' => Yii::$app->user->identity->auth_key,
        ];
      } else {
        return ActiveForm::validate($model);
      }
    }
  }

  /**
   * Signs user up.
   *
   * @return mixed
   */
  public function actionSignup()
  {

    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }

    Yii::$app->response->format = Response::FORMAT_JSON;

    $action = Yii::$app->request->post('action', '');
    $token = Yii::$app->request->post('token', '');
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $params = [
      'secret' => '6Ld8b-kaAAAAAHvHSoxCkahnEwiaWu_DwzOejYgK',
      'response' => $token,
      'remoteip' => $_SERVER['REMOTE_ADDR'],
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    if (!empty($response)) $decoded_response = json_decode($response);

    if ($decoded_response && $decoded_response->success && $decoded_response->action == $action && $decoded_response->score > 0) {
      //      file_put_contents('/home/c/cw17100/sakura/public_html/11111.txt', json_encode($decoded_response), FILE_APPEND);

      $model = new SignupForm();
      if ($model->load(Yii::$app->request->post())) {
        if ($user = $model->signup()) {

          $reg_info = [
            'cust_name' => $model->fio,
            'cust_phone' => $model->phone,
            'cust_email' => $model->email,
          ];
          Yii::$app->session->set('customer_reg_info', $reg_info);

          if (Yii::$app->getUser()->login($user)) {
            return [
              'success' => true,
              'token' => Yii::$app->user->identity->auth_key,
            ];
          }
          return ActiveForm::validate($model);
        } else {
          return ActiveForm::validate($model);
        }
      } else {
        return ActiveForm::validate($model);
      }
    } else {
      return ['error' => 'captcha wrong'];
    }
  }

  /**
   * Displays SignUp form.
   *
   * @return mixed
   */
  public function actionSignuppopup()
  {
    if (Yii::$app->user->isGuest) {
      $model = new SignupForm();
      $customer_reg_info = Yii::$app->session->get('customer_reg_info', '');
      $ref = Yii::$app->session->get('partner-reflink', '');
      $model->ref_link = $ref;
      //            var_dump($customer_reg_info);
      if (is_array($customer_reg_info) && !empty($customer_reg_info)) {
        $model->fio = $customer_reg_info['cust_name'];
        $model->phone = $customer_reg_info['cust_phone'];
        $model->email = $customer_reg_info['cust_email'];
      }
      return $this->renderAjax('signup', [
        'model' => $model,
      ]);
    }
  }

  /**
   * Logs out the current user.
   *
   * @return mixed
   */
  public function actionLogout()
  {
    Yii::$app->user->logout();

    return $this->goHome();
  }

  /**
   * Requests password reset.
   *
   * @return mixed
   */
  public function actionRequestPasswordReset()
  {
    $model = new PasswordResetRequestForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      if ($model->sendEmail()) {
        Yii::$app->session->setFlash('success', 'Проверьте вашу почту для дальнейших инструкций.');
      } else {
        Yii::$app->session->setFlash('error', 'Извините, мы не смогли сбросить пароль для указанного email.');
      }
    }

    return $this->render('requestPasswordResetToken', [
      'model' => $model,
    ]);
  }

  /**
   * Resets password.
   *
   * @param string $token
   * @return mixed
   * @throws BadRequestHttpException
   */
  public function actionResetPassword($token)
  {
    try {
      $model = new ResetPasswordForm($token);
    } catch (InvalidParamException $e) {
      throw new BadRequestHttpException($e->getMessage());
    }

    if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
      Yii::$app->session->setFlash('success', 'Новый пароль сохранен');

      return $this->goHome();
    }

    return $this->render('resetPassword', [
      'model' => $model,
    ]);
  }
  
  // CRON процедура, проходящая по всем подкоюченным стратегиям и выставляющая счета всем подключенным инвесторам, если уже настало время
  // также она пытается автоматически оплатить счет, если на балансе у инвестора есть достаточные для оплаты деньги.
  public function actionCronProcessInvoices()
  {
		set_time_limit(0);
		ignore_user_abort(1);

    CustomerStrategy::addAllInvoices();
  }

  // Callback проведения оплаты для мерчанта
  public function actionPaymentCallback()
  {

    $ips_text = file_get_contents('https://paykassa.pro/ips.txt');
    $ips = explode("\n", $ips_text);

    if (!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
      die('Запрос с неразрешенного IP');
    }

    $merchant = Settings::readAllSettings('merchant');

    require_once FileHelper::normalizePath(Yii::getAlias('@common/components/paykassa_sci.class.php'));

    Logger::paymentLog('Payment Callback', 'Начало');

    $paykassa = new \PayKassaSCI(
        $merchant['merchant_id'],       // идентификатор мерчанта
        $merchant['merchant_secret'],   // пароль мерчанта
        (bool)$merchant['mode']         // test
    );

    $res = $paykassa->sci_confirm_order();

    if ($res['error']) {
      Logger::paymentLog('Payment Callback', 'Ошибка: ' . $res['message']);
      die($res['message']);
    } else {
      $id = $res["data"]["order_id"];        // уникальный числовой идентификатор платежа в вашей системе, пример: 150800
      
      $transaction = $res["data"]["transaction"]; // номер транзакции в системе paykassa: 96401
      $hash = $res["data"]["hash"];               // hash, пример: bde834a2f48143f733fcc9684e4ae0212b370d015cf6d3f769c9bc695ab078d1
      $currency = $res["data"]["currency"];       // валюта платежа, пример: DASH
      $system = $res["data"]["system"];           // система, пример: Dash
      $address = $res["data"]["address"];         // адрес криптовалютного кошелька, пример: Xybb9RNvdMx8vq7z24srfr1FQCAFbFGWLg
      $tag = $res["data"]["tag"];                 // Tag для Ripple и Stellar
      $partial = $res["data"]["partial"];         // настройка приема недоплаты или переплаты, 'yes' - принимать, 'no' - не принимать
      $amount = (float)$res["data"]["amount"];    // сумма счета, пример: 1.0000000
      
      $movement = Movement::findOne($id);
      if ($movement instanceof Movement) {
        $movement->status = Movement::STATUS_APPROVED;
        $movement->save();
  
        Logger::paymentLog('Payment Callback', 'Успешно');

        echo $id.'|success'; // обязательно, для подтверждения зачисления платежа
        exit();

      } else {
        Logger::paymentLog('Payment Callback', 'Ошибка: Локальный платеж не найден');
        die('Локальный платеж не найден');
      }
    }
  }
}
