<?php


namespace common\components;


use yii\base\Component;

class Common extends Component
{
    public static function Debug($arr)
    {
        echo '<pre>' . print_r($arr, true) . '</pre>';
    }

}