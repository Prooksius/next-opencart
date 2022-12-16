<?php
namespace frontend\models;

use frontend\components\Helper;
use Yii;
use yii\base\Model;
use common\models\Customer;

/**
 * Signup form
 */
class ConsultForm extends Model
{
    public $fio;
    public $phone;
    public $email;
    public $reCaptcha;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['fio', 'trim'],
            ['fio', 'required'],
            ['fio', 'string', 'min' => 2, 'max' => 255],

            ['phone', 'required'],
            ['phone', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
        ];
    }

    /**
     * Signs user up.
     *
     * @return Customer|null the saved model or null if saving fails
     */
    public function sendEmail()
    {
        $subject = 'аказ консультации';

        $text = "Сайт Copy-Trade: з" . $subject . "\r\n\r\n";
        $text .= "Имя: " . $this->fio . "\r\n";
        $text .= "Телефон: " . $this->phone . "\r\n";
        $text .= "Email: " . $this->email;
        

            Yii::$app->mailer->compose()
                ->setTo([Yii::$app->params['adminEmail'], Yii::$app->params['devEmail']])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setSubject('З' . $subject)
            ->setTextBody($text)
            ->send();
           

        // и еще скинуть в b24
        /*
        $b24_access = Settings :: readAllSettings('bitrix24');
        Helper :: send_b24_lead($b24_access, 'З'.$subject.' с сайта Copy-Trade', 'request', [
            'name' => $this -> fio,
            'email' => $this -> email,
            'phone' => $this -> phone,
            'amount' => '',
            'botname' => '',
            'promocode' => ''
        ]);
        */

            //,
            //'form_name' => 'consult-form'
            
        //file_put_contents(Yii :: $app -> basePath . '/data_send_mail.txt', json_encode([$this->fio, $this->phone, $this->email]));

        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fio' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'Email',
            'reCaptcha;' => 'Проверочный код',
        ];
    }
}
