<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Login form
 */
class ProfileForm extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $password;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
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

            // email rules
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailLength' => ['email', 'string', 'max' => 255],
            'emailUnique' => [
                'email',
                'unique',
                'message' => 'Такой email уже используется',
            ],
            ['email', 'trim'],
            ['clients', 'string', 'max' => 255],
//            ['clients', 'required'],
            ['clients', 'trim'],

            ['fullname', 'string', 'max' => 255],
            ['fullname', 'required'],
            ['fullname', 'trim'],

            ['status', 'default', 'value' => User::STATUS_ACTIVE],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DELETED]],

            ['password', 'trim'],
            [['auth_key'], 'string', 'max' => 32],
            [['auth_key'], 'default', 'value' => ''],
        ];
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
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);

        if (!empty($this->password)) {
            $this->setPassword($this->password);
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
    public static function findAllIdentity($id)
    {
        return static::findOne(['id' => $id]);
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

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullname' => 'Имя',
            'username' => 'Логин',
            'password' => 'Новый пароль',
            'status' => 'Статус',
            'email' => 'Email',
        ];
    }
}