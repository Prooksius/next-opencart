<?php

namespace frontend\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\validators\Validator;

/**
 * Class ReCaptchaValidator
 * @package kekaadrenalin\recaptcha3
 */
class ReCaptchaValidator extends Validator
{
    /**
     * @var bool
     */
    public $skipOnEmpty = false;


    /**
     * Recaptcha component
     * @var string|array|ReCaptcha
     */
    public $component = 'reCaptcha3';


    /**
     * the minimum score for this request (0.0 - 1.0)
     * @var null|int
     */
    public $acceptance_score = null;

    /**
     * @var ReCaptcha
     */
    private $_component = null;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $component = Instance::ensure($this->component, ReCaptcha::className());
        if ($component == null) {
            throw new InvalidConfigException('Component is required.');
        }
        $this->_component = $component;

        if ($this->message === null) {
            $this->message = 'The verification code is incorrect.';
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $result = $this->_component->validateValue($value);
        if ($result === false) {
            return [$this->message . 'false', []];
        }

        if ($this->acceptance_score !== null && $result < $this->acceptance_score) {
            return [$this->message . ' ('. $result .')', []];
        }

        return null;
    }

}