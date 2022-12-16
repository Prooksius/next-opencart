<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 */
class Customer extends ActiveRecord implements IdentityInterface
{
  const STATUS_DELETED = 0;
  const STATUS_ACTIVE = 10;

  private static $_hidden_users = [];

  /**
   * @var string Plain password. Used for model validation
   */
  public $password;


  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_customer';
  }

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      // username rules
      'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
      'usernameMatch' => ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@\+]+$/'],
      'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
      'usernameTrim' => ['username', 'trim'],
      'usernameUnique' => [
        'username',
        'unique',
        'message' => 'Это имя пользователя уже используется',
      ],

      [['email_confirmed', 'last_visit', 'customer_group_id'], 'integer'],
      [['email_confirmed'], 'default', 'value' => 0],
      [['last_visit'], 'default', 'value' => 0],
      ['status', 'default', 'value' => self::STATUS_ACTIVE],
      ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
      [['auth_key'], 'string', 'max' => 32],
      [['picture'], 'string', 'max' => 255],
      [['first_name', 'last_name', 'ref_link'], 'string', 'max' => 100],
      [['last_name'], 'default', 'value' => ''],
      ['ref_link', 'unique', 'message' => 'Это реф. ссылка уже используется'],
      [['phone', 'telegram'], 'string', 'max' => 50],

      [['lastvisit'], 'safe'],

      // email rules
      'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
      'emailPattern' => ['email', 'email'],
      'emailLength' => ['email', 'string', 'max' => 255],
      'emailUnique' => [
        'email',
        'unique',
        'message' => 'Такой email уже используется',
      ],
      'emailTrim' => ['email', 'trim'],

      // password rules
      'passwordTrim' => ['password', 'trim'],
      'passwordRequired' => ['password', 'required', 'on' => ['register', 'create']],
      'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
    ];
  }

  public function getLastvisit()
  {
    if ($this->last_visit) {
      return date('d.m.Y H:i', $this->last_visit);
    } else {
      return '';
    }
  }

  public function setLastvisit($value)
  {
    if ($value) {
      $date_field = date_create_from_format('d.m.Y H:i', $value);
      $this->last_visit = date_timestamp_get($date_field);
    } else {
      $this->last_visit = 0;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function beforeSave($insert)
  {
    parent::beforeSave($insert);

    if (!empty($this->password)) {
      $this->setPassword($this->password);
    }

    if (empty($this->auth_key)) {
      $this->generateAuthKey();
    }

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentity($id)
  {
    return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentityByAccessToken($token, $type = null)
  {
    if ($type == 'yii\filters\auth\HttpBearerAuth') {
      $user = static::find()
        ->where(['auth_key' => $token, 'status' => self::STATUS_ACTIVE])
        ->one();

      return $user;
    } else {
      return false;
    }
  }

  /**
   * Finds user by username
   *
   * @param string $username
   * @return static|null
   */
  public static function findByUsername($username)
  {
    return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
  }

  /**
   * Finds user by email
   *
   * @param string $email
   * @return static|null
   */
  public static function findByEmail($email)
  {
    return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
  }

  /**
   * Finds user by password reset token
   *
   * @param string $token password reset token
   * @return static|null
   */
  public static function findByPasswordResetToken($token)
  {
    if (!static::isPasswordResetTokenValid($token)) {
      return null;
    }

    return static::findOne([
      'password_reset_token' => $token,
      'status' => self::STATUS_ACTIVE,
    ]);
  }

  /**
   * Finds out if password reset token is valid
   *
   * @param string $token password reset token
   * @return bool
   */
  public static function isPasswordResetTokenValid($token)
  {
    if (empty($token)) {
      return false;
    }

    $timestamp = (int) substr($token, strrpos($token, '_') + 1);
    $expire = Yii::$app->params['user.passwordResetTokenExpire'];
    return $timestamp + $expire >= time();
  }

  /**
   * {@inheritdoc}
   */
  public function getId()
  {
    return $this->getPrimaryKey();
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthKey()
  {
    return $this->auth_key;
  }

  /**
   * {@inheritdoc}
   */
  public function validateAuthKey($authKey)
  {
    return $this->getAuthKey() === $authKey;
  }

  /**
   * Validates password
   *
   * @param string $password password to validate
   * @return bool if password provided is valid for current user
   */
  public function validatePassword($password)
  {
    return Yii::$app->security->validatePassword($password, $this->password_hash);
  }

  /**
   * Generates password hash from password and sets it to the model
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
  }

  /**
   * Generates "remember me" authentication key
   */
  public function generateAuthKey()
  {
    $this->auth_key = Yii::$app->security->generateRandomString();
  }

  /**
   * Generates new password reset token
   */
  public function generatePasswordResetToken()
  {
    $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
  }

  /**
   * Removes password reset token
   */
  public function removePasswordResetToken()
  {
    $this->password_reset_token = null;
  }
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'customer_group_id' => YII::t('customer', 'Customer group'),
      'username' => 'Логин',
      'status' => 'Статус',
      'picture' => 'Аватар',
      'email' => 'Email',
      'first_name' => 'Имя',
      'last_name' => 'Фамилия',
      'phone' => 'Телефон',
      'telegram' => 'Телеграм',
      'ref_link' => 'Реф. ссылка',
      'created_at' => 'Дата регистрации',
      'updated_at' => 'Updated At',
      'email_confirmed' => 'Email подтв.?',
      'last_visit' => 'Время последнего визита',
      'lastvisit' => 'Время последнего визита',
    ];
  }
}
