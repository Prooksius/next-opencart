<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 24.04.2020
 * Time: 12:39
 */

namespace common\models;

use Yii;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    private $_user;

    public function rules()
    {
        return [
            ['password', 'required'],
        ];
    }

    public  function attributeLabels()
    {
        return [
            'password' => 'Пароль'
        ];
    }

    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);

    }
}