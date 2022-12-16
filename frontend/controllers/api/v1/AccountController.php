<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 08.09.2021
 * Time: 15:08
 */

namespace frontend\controllers\api\v1;


use common\models\Customer;
use frontend\models\Translation;
use frontend\resources\CustomerResource;
use Yii;
use yii\filters\AccessControl;
use yii\rest\Controller;
use frontend\models\LoginForm;
use frontend\models\SignupForm;
use frontend\models\InviteForm;

class AccountController extends Controller
{

    public function beforeAction($action)
    {
        \Yii::$app->language = Yii::$app->request->post('lang', 'ru-RU');

        return parent::beforeAction($action);
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return [
                'success' => 1,
                'user' => Yii::$app->user->identity,
            ];
        }

        if (empty($model->errors)) {
            $model->validate();
        }
        Yii::$app->response->setStatusCode(401);
        return [
            'success' => 0,
            'errors' => $model->errors,
            'user' => null,
        ];
    }

    public function actionRegister()
    {
        /*
        $token = Yii::$app->request->post('token', '');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $params = [
            'secret' => Yii::$app->params['recaptchaSecret'],
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ];

        $error = false;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if (!empty($response)) $decoded_response = json_decode($response);

        if ($decoded_response && $decoded_response->success && $decoded_response->score > 0) {
        */
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $user = $model->signup()) {
            if (Yii::$app->getUser()->login($user)) {

                return [
                    'success' => 1,
                    'user' => Yii::$app->user->identity,
                ];
            } else {
                $model->addError('email', Translation::getTranslation('UserLoginError'));
            }
        }
        /*
        } else {
            $model->addError('email', Translation::getTranslation('CaptchaError'));
        }
        */
        if (empty($model->errors)) {
            $model->validate();
        }
        Yii::$app->response->setStatusCode(401);
        return [
            'success' => 0,
            'error' => $model->errors,
            'user' => null,
        ];
    }

    public function actionInvite()
    {
        /*
        $token = Yii::$app->request->post('token', '');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $params = [
            'secret' => Yii::$app->params['recaptchaSecret'],
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ];

        $error = false;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if (!empty($response)) $decoded_response = json_decode($response);

        if ($decoded_response && $decoded_response->success && $decoded_response->score > 0) {
        */
            $model = new InviteForm();

            if ($model->load(Yii::$app->request->post()) && $user = $model->invite()) {
                return [
                    'success' => 1,
                    'user' => $user,
                ];
            }
        /*
        } else {
            $model->addError('email', Translation::getTranslation('CaptchaError'));
        }
        */
            if (empty($model->errors)) {
                $model->validate();
            }
            Yii::$app->response->setStatusCode(401);
            return [
                'success' => 0,
                'error' => $model->errors,
                'user' => null,
            ];
    }

    public function actionInviteAccept($auth_key)
    {

        $auth_key = strip_tags($auth_key);

        if ($user = CustomerResource::findIdentityByAccessToken($auth_key)) {
            $user->status = Customer::STATUS_ACTIVE;
            $user->save();

            if (Yii::$app->user->login($user)) {

                return [
                    'success' => 1,
                    'user' => $user,
                ];
            }
        }
        Yii::$app->response->setStatusCode(401);
        return [
            'success' => 0,
            'errors' => [
                'auth_key' => Translation::getTranslation('InvalidAuthKey')
            ],
            'user' => null,
        ];
    }

    public function actionInviteReject($auth_key)
    {
        $auth_key = strip_tags($auth_key);

        if ($user = CustomerResource::findIdentityByAccessToken($auth_key)) {

            $user->status = Customer::STATUS_DELETED;
            $user->save();

            return [
                'success' => 1,
                'user' => null,
            ];
        }
        Yii::$app->response->setStatusCode(401);
        return [
            'success' => 0,
            'errors' => [
                'auth_key' => Translation::getTranslation('InvalidAuthKey')
            ],
            'user' => null,
        ];
    }
}