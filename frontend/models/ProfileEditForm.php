<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Customer;

/**
 * Signup form
 */
class ProfileEditForm extends Model
{
    public $username;
    public $email;
    public $first_name;
    public $phone;
    public $telegram;
    public $oldPassword;
    public $newPassword;
    public $newPasswordRepeat;

    private $_customer;

    /**
     * @param Customer $customer
     * @param array $config
     */
    public function __construct(Customer $customer, $config = [])
    {
        $this->_customer = $customer;
        $this->first_name = $customer->first_name;
        $this->username = $customer->username;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->telegram = $customer->telegram;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\Customer',
                'message' => 'Этот логин уже занят.',
                'filter' => ['<>', 'id', $this->_customer->id],
            ],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\Customer',
                'message' => 'Этот email адрес уже зарегистрирован.',
                'filter' => ['<>', 'id', $this->_customer->id],
            ],

            [['phone', 'telegram'], 'string', 'max' => 50],
            ['first_name', 'string', 'max' => 100],

            ['oldPassword', 'required'],
            ['oldPassword', 'validateOldPassword'],
            ['newPassword', 'string', 'min' => 6],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->_customer->validatePassword($this->$attribute)) {
                $this->addError($attribute, 'Текущий пароль неверный');
            }
        }
    }

    /**
     * @return boolean
     */
    public function saveProfile()
    {
        if ($this->validate()) {
            $customer = $this->_customer;
            $customer->username = $this->username;
            $customer->email = $this->email;
            $customer->first_name = $this->first_name;
            $customer->phone = $this->phone;
            $customer->telegram = $this->telegram;
            if ($this->newPassword) {
                $customer->setPassword($this->newPassword);
            }
            return $customer->save();
        } else {
            return false;
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'first_name' => 'Имя',
            'phone' => 'Телефон',
            'telegram' => 'Телеграм',
            'oldPassword' => 'Старый пароль',
            'newPassword' => 'Новый пароль',
            'passwordRepeat' => 'Повторите пароль',
        ];
    }
}
