<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Customer;

/**
 * Signup form
 */
class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $password;
    public $passwordRepeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['oldPassword', 'validatePassword'],
            [['oldPassword', 'password', 'passwordRepeat'], 'required'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = Yii::$app->user->identity;
            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, YII::t('app', 'Wrong old password'));
            }
        }
    }
}
