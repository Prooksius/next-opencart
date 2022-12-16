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
use frontend\models\LayoutModule;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;

class SettingsController extends PublicApiController
{

  public function actionIndex()
  {

    Yii::$app->cart->setCustomer(Helper::cleanData(Yii::$app->request->post('customer', '')));
    Yii::$app->cart->setAddress(Helper::cleanData(Yii::$app->request->post('address', '')));

    Yii::$app->delivery->setCurrent(Helper::cleanData(Yii::$app->request->post('delivery', '')));
    Yii::$app->payment->setCurrent(Helper::cleanData(Yii::$app->request->post('payment', '')));

    try {

      $settings = Settings::readAllSettings('general');
      $mainpage = Settings::readAllSettings('mainpage');

      $settings['appLanguage'] = Yii::$app->language;
      $settings['phoneMasks'] = Country::getPhoneMasks();
      $settings['phoneMasksArr'] = Country::getPhoneMasksArr();

      $settings['recaptchaSite'] = Yii::$app->params['recaptchaSite'];

//    $settings['stocks'] = Helper::getArrayFullFromText($trader['stocks']);
//    $settings['markets'] = Helper::getArrayFullFromText($trader['markets']);

      $settings['mainSettings']['title'] = !empty($mainpage['title'][Yii::$app->language]) ? $mainpage['title'][Yii::$app->language] : '';
      $settings['mainSettings']['meta_title'] = !empty($mainpage['meta_title'][Yii::$app->language]) ? $mainpage['meta_title'][Yii::$app->language] : '';
      $settings['mainSettings']['meta_desc'] = !empty($mainpage['meta_desc'][Yii::$app->language]) ? $mainpage['meta_desc'][Yii::$app->language] : '';
      $settings['mainSettings']['meta_keywords'] = !empty($mainpage['meta_keywords'][Yii::$app->language]) ? $mainpage['meta_keywords'][Yii::$app->language] : '';

      $user_data = 0;
      $user = null;

      if (Yii::$app->user->loginByAccessToken(\Yii::$app->request->post('auth_key'), 'auth_key')) {
        $user = Yii::$app->user->identity;
      } else {
        $user = null;
      }

      if ($user) {
        $user_data = [
          'id' => $user->id,
          'first_name' => $user->first_name,
          'last_name' => $user->last_name,
          'middle_name' => $user->middle_name,
          'email' => $user->email,
          'phone' => $user->phone,
          'avatar' => $user->picture,
          'auth_key' => $user->auth_key,
        ];
      } else {
        $user_data = null;
      }

    } catch (\Exception $e) {
      //$user_data = $e->getMessage();
      $user_data = null;
    }

    $customer_id = isset($user_data['id']) ? $user_data['id'] : 0;

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
    $cart['customer_data'] = Yii::$app->cart->getCustomer();
    $cart['customer_address'] = Yii::$app->cart->getAddress();

    return [
        'success' => 1,
        'globals' => [
          'lang_settings' => [
              'site_name' => Yii::$app->name,
              'home' => YII::t('app', 'Home'),
          ],
          'currency'	=> [
            'symbol_left' 	=> Yii::$app->currency->getSymbolLeft(Yii::$app->currency->getCurrent()),
            'symbol_right' 	=> Yii::$app->currency->getSymbolRight(Yii::$app->currency->getCurrent()),
            'decimal_place' => (int)Yii::$app->currency->getDecimalPlace(Yii::$app->currency->getCurrent()),
          ],
          'translations' => Translation::getAllTranslations(),
          'languages' => LanguageChoose::getAllLanguages(),
          'settings' => $settings,
          'modules' => LayoutModule::getAllModules(),
          'user' => $user_data,
        ],
        'cart' => $cart,
    ];
  }
}