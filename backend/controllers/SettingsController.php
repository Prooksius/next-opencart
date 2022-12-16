<?php

namespace backend\controllers;

use common\components\Common;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\Settings;
use app\models\CustomerGroup;
use app\models\LengthClass;
use app\models\OrderStatus;
use app\models\WeightClass;
use common\models\Currency;
use yii\filters\AccessControl;

use common\models\search\PartnerPercentsSearch;
use yii\web\NotFoundHttpException;

class SettingsController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        if (Yii::$app->request->isPost) {
            Settings::saveAllSettings(Yii::$app->request->post(), '', ['prices_blocks']);
            return $this->goBack();
        }

        return $this->render('index', [
            'languages' => Language::find()->all(),
            'settings' => Settings::readAllSettings('general'),
            'mainpage' => Settings::readAllSettings('mainpage'),
            'bots' => Settings::readAllSettings('bots'),
            'social' => Settings::readAllSettings('social'),
            'merchant' => Settings::readAllSettings('merchant'),
            'bitrix24' => Settings::readAllSettings('bitrix24'),
            'investor' => Settings::readAllSettings('investor'),
            'partner' => Settings::readAllSettings('partner'),
            'trader' => Settings::readAllSettings('trader'),
            'trips' => Settings::readAllSettings('trips'),
            'customerGroupsList' => CustomerGroup::getAllGroups(),
            'currenciesList' => Yii::$app->currency->getList(),
            'languagesList' => Language::getList(),
            'orderStatusesList' => OrderStatus::getAllStatuses(),
            'weightClassesList' => WeightClass::getAllClasses(),
            'lengthClassesList' => LengthClass::getAllClasses(),
        ]);
    }
}