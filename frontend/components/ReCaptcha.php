<?php

namespace frontend\components;

use Yii;
use yii\helpers\Json;

/**
 * Class ReCaptcha
 * @package kekaadrenalin\recaptcha3
 */
class ReCaptcha extends \yii\base\Component
{
    public $site_key = null;

    public $secret_key = null;

    private $verify_endpoint = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->site_key)) {
            throw new \Exception('site key cant be null');
        }

        if (empty($this->secret_key)) {
            throw new \Exception('secret key cant be null');
        }

        defined('RECAPTCHA_OFF') or define('RECAPTCHA_OFF', false);
    }

    /**
     * @param $view
     *
     */
    public function registerScript($view)
    {
        /** @var View $view */
    }


    /**
     * @param $value
     *
     * @return bool|int
     */
    public function validateValue($value)
    {
        if (YII_ENV_TEST) {
            return 1;
        }

        try {
            $response = $this->curl([
                'secret'   => $this->secret_key,
                'response' => $value,
                'remoteip' => Yii::$app->has('request') ? Yii::$app->request->userIP : null,
            ]);
            if (!empty($response) && $response['success']) {
                return $response['score'];
            }
        } catch (\Exception $e) {

        }

        return false;
    }

    /**
     * Sends User post data with curl to google and receives 'success' if valid
     *
     * @param array $params
     *
     * @return mixed
     */
    protected function curl(array $params)
    {
        $curl = curl_init($this->verify_endpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $curlData = curl_exec($curl);
        curl_close($curl);

        //file_put_contents('/home/c/cw17100/sakura/public_html/11111.txt', json_encode($curlData));
        return Json::decode($curlData, true);
    }
}