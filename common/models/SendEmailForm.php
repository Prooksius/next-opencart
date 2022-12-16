<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 24.04.2020
 * Time: 12:08
 */

namespace common\models;

use Yii;
use yii\base\Model;

class SendEmailForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // email rules
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::ClassName(),
                'filter' => [
                    User::STATUS_ACTIVE,
                ],
                'message' => 'Данный email не зарегистрирован'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    public  function sendEmail()
    {
        $user = User::findOne(
            [
                'status' => User::STATUS_ACTIVE,
                'email' => $this->email,
            ]
        );

        if ($user) {
            $user->generateSecretKey();
            if ($user->save()) {
                return Yii::$app->mailer->compose('resetPassword', ['user' => $user])
                    ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name . ' (отправлено роботом)'])
                    ->setTo($this->email)
                    ->setSubject('Сброс пароля для ' . $this->email)
                    ->send();
            }
        }

        return false;
    }
}