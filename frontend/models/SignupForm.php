<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Customer;
use frontend\components\Helper;

/**
 * Signup form
 */
class SignupForm extends Model
{
  public $username;
  public $email;
  public $fio;
  public $phone;
  public $password;
  public $passwordRepeat;
  public $reCaptcha;
  public $ref_link;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      ['username', 'required'],
      ['username', 'string', 'min' => 3, 'max' => 255],
      ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@\+]+$/', 'message' => 'Логин может содержать латинские буквы, цифры, _ и @'],
      ['username', 'trim'],
      ['username', 'unique', 'targetClass' => '\common\models\Customer', 'message' => 'Этот логин уже занят.'],

      ['fio', 'trim'],
      ['fio', 'required'],
      ['fio', 'string', 'min' => 2, 'max' => 255],

      ['email', 'trim'],
      ['email', 'required'],
      ['email', 'email'],
      ['email', 'string', 'max' => 255],
      ['email', 'unique', 'targetClass' => '\common\models\Customer', 'message' => 'Этот email адрес уже зарегистрирован.'],

      ['ref_link', 'required'],
      ['ref_link', 'validateRefLink'],
      ['ref_link', 'string', 'min' => 2, 'max' => 50],

      ['phone', 'required'],
      [['phone'], 'string', 'min' => 2, 'max' => 255],

      [['password', 'passwordRepeat'], 'required'],
      ['password', 'string', 'min' => 6],
      ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
    ];
  }

  public function validateRefLink($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $partner = Customer::find()
        ->where(['status' => Customer::STATUS_ACTIVE, 'ref_link' => $this->ref_link]);

      $partner = $partner->one();
      if ($partner instanceof Customer) {
      } else {
        $this->addError($attribute, 'Неверный код партнера.');
      }
    }
  }
  /**
   * Signs user up.
   *
   * @return Customer|null the saved model or null if saving fails
   */
  public function signup()
  {
    if (!$this->validate()) {
      return null;
    }

    $user = new Customer();
    $user->username = $this->username;
    $user->first_name = $this->fio;
    $user->email = $this->email;
    $user->phone = $this->phone;
    $user->account_id = 'id' . Helper::numToken();
    $user->setPassword($this->password);
    $user->generateAuthKey();
    $user->ref_link = Yii::$app->security->generateRandomString(12);

    // если пользователь пришел по реф-ссылке, значит мы читаем промокод пользователя с этой реф-ссылкой
    $ref = $this->ref_link;
    $partner = Customer::find()
      ->where(['status' => Customer::STATUS_ACTIVE, 'ref_link' => $ref]);

    $partner = $partner->one();
    if ($partner instanceof Customer) { // если партнер с этой реф-ссылкой найден
      $user->parent_id = $partner->id;
    } else { // Если партнер по реф. ссылке не найден (такого не должно быть - мы это проверяем, когда пользователь пришел с реф ссылкой,
      $this->addError('username', 'Неверный код партнера');
      return null;
    }

    if ($user->save()) {

      $subject = 'Регистрация на сайте';

      $text = "Сайт Copy-Trade: вы зарегистрировались. Ваши данные:\r\n\r\n";
      $text .= "Имя: " . $this->fio . "\r\n";
      $text .= "Телефон: " . $this->phone . "\r\n";
      $text .= "Ваша реф-ссылка: https://copy-trade.ru/?ref=" . $user->ref_link . "\r\n\r\n";
      $text .= "Логин: " . $this->username . "\r\n";
      $text .= "Email: " . $this->email . "\r\n";
      $text .= "Пароль: " . $this->password . "\r\n";

      Yii::$app->mailer->compose()
        ->setTo($this->email)
        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
        ->setSubject($subject)
        ->setTextBody($text)
        ->send();

      return $user;
    }
    return null;
  }

  public function attributeLabels()
  {
    return [
      'reCaptcha;' => 'Проверочный код',
      'username' => 'Логин',
      'fio' => 'Имя',
      'email' => 'Email',
      'phone' => 'Телефон',
      'password' => 'Пароль',
      'passwordRepeat' => 'Повторите пароль',
      'ref_link' => 'Код партнера',
    ];
  }
}
