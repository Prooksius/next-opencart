<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 08.09.2021
 * Time: 15:08
 */

namespace frontend\components;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class ApiController extends Controller
{

  public function behaviors()
  {
    $behaviors = ArrayHelper::merge( Yii::$app->params['work_mode'] == 'development' ? [
      [
        'class' => Cors::className(),
        'cors' => [
          // Разрешаем доступ с указанных доменов.
          'Origin' => ['http://http://next-cart.site', 'http://localhost:3000'],
          'Access-Control-Request-Method' => ['POST'],
          'Access-Control-Request-Headers' => ['Authorization', 'content-type'],
          'Access-Control-Allow-Credentials' => true,
        ],
      ],
    ] : [], parent::behaviors());

    $behaviors['authenticator']['authMethods'] = [
      HttpBearerAuth::class
    ];

    return $behaviors;
  }

  public function beforeAction($action)
  {
    Yii::$app->language = Helper::cleanData(Yii::$app->request->post('lang', Yii::$app->shopConfig->getParam('language')));
    Yii::$app->currency->setCurrent(Helper::cleanData(Yii::$app->request->post('currency', Yii::$app->shopConfig->getParam('currency'))));
    
    Yii::$app->shopConfig->setParam('session_id', Helper::cleanData(Yii::$app->request->post('session_id', '')));

    return parent::beforeAction($action);
  }

  protected function verbs()
  {
    return [
      'index'  => ['POST'],
      'create' => ['POST'],
      'update' => ['POST'],
      'delete' => ['DELETE'],
      'options' => ['OPTIONS'],
    ];
  }
}