<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator2;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $reCaptcha;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            [['reCaptcha'], ReCaptchaValidator2::class,
                'uncheckedMessage' => 'Подтвердите, что Вы не робот'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reCaptcha;' => 'Проверочный код',
            'name' => 'Ваше имя',
            'email' => 'Ваш Email',
            'subject' => 'Ваш вопрос',
            'body' => 'Содержание вопроса',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$this->email => $this->name])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }
}
